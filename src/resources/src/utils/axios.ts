// @ts-ignore
import axios from 'axios'
// @ts-ignore
import {ElMessage, ElNotification} from 'element-plus'
import router from '@/router'
import {action,state} from '@/store'
import {refresh, link} from '@/utils'

// create an axios instance
const service = axios.create({
    // baseURL: window.global_config.VUE_APP_BASE_API, // url = base url + request url
    baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
    // withCredentials: true, // send cookies when cross-domain requests
    timeout: 30000 // request timeout
})
// request interceptor
service.interceptors.request.use(
    (config: any) => {
        // do something before request is sent
        // let each request carry token
        // ['X-Token'] is a custom headers key
        // please modify it according to the actual situation
        config.headers['Authorization'] = localStorage.getItem('eadmin_token')
        return config
    },
    (error: any) => {
        // do something with request error
        console.log(error) // for debug
        return Promise.reject(error)
    }
)

// response interceptor
service.interceptors.response.use(
    (  /**
        * If you want to get http information such as headers or status
        * Please return  response => response
        */

       /**
        * Determine the request status by custom code
        * Here is just an example
        * You can also judge the status by HTTP Status Code
        */
       response: { data: any }) => {
        const res = response.data
        const token_expire = localStorage.getItem('eadmin_token_expire')
        if(token_expire){
            const timeNow = Math.round((new Date()).getTime() / 1000)
            // @ts-ignore
            const refreshTime = token_expire - timeNow
            if (refreshTime > 0 && refreshTime < 1800) {
                localStorage.removeItem('eadmin_token_expire')
                action.refreshToken()
            }
        }
        // if the custom code is not 20000, it is judged as an error.
        if (res.code !== 200) {
            // 登陆验证token判断
            if (res.code === 40000 || res.code === 40001 || res.code === 40002 || res.code === 40003) {
                localStorage.removeItem('eadmin_token')
                localStorage.removeItem('eadmin_token_expire')
                ElMessage({
                    message: res.message,
                    type: 'error',
                    duration: 1000,
                    onClose: function () {
                        if (!localStorage.getItem('eadmin_token') && location.href.indexOf('/#/admin/login') === -1) {
                            location.reload()
                        }
                    }
                })

            }else if(res.code == 44000){
                ElMessage({
                    message: res.message || 'Error',
                    type: 'error',
                    duration: 1000,
                    onClose: function () {
                        if(res.method == 'GET') {
                            router.replace('/')
                        }
                    }
                })
            } else if (res.code == 80020) {
                ElMessage({
                    showClose: true,
                    dangerouslyUseHTMLString: true,
                    message: res.message,
                    type: res.type
                })
                if (res.url) {
                    if (res.url == 'back') {
                        if (res.refresh) {
                            action.refresh(true)
                        }
                        router.back()
                    } else {
                        link(res.url)
                        return
                    }
                }
                if (res.refresh) {
                    refresh()
                }
                if (res.type == 'success') {
                    res.code = 200
                    return res
                }
            } else if (res.code == 80021) {
                ElNotification({
                    showClose: true,
                    dangerouslyUseHTMLString: true,
                    title: res.title,
                    message: res.message,
                    type: res.type,
                    duration: 1500
                })
                if (res.url) {
                    if (res.url == 'back') {
                        if (res.refresh) {
                            action.refresh(true)
                        }
                        router.back()
                        return
                    } else {
                        link(res.url)
                        return
                    }
                }
                if (res.refresh) {
                    refresh()
                }
                if (res.type == 'success') {
                    res.code = 200
                    return res
                }
            } else if (res.code === 40021) {
                link(res.url)
                return
            } else if (res.code == 422 || res.code == 412) {
                // 表单验证
                return res
            } else if (typeof (res) == 'object' && !res.message) {
                return res
            } else {
                ElMessage({
                    message: res.message || 'Error',
                    type: 'error',
                    duration: 3 * 1000
                })
            }
            return Promise.reject(new Error(res.message || 'Request Error'))
        } else {
            return res
        }
    },
    (error: any) => {
        console.log('err' + error) // for debug
        if (error.code === 'ECONNABORTED' || error.message === 'Network Error' || error.message.includes('timeout')) {
            ElMessage({
                message: '网络请求超时',
                type: 'error',
                duration: 3 * 1000
            })
        } else {
            let message
            if (error.response) {
                message = error.response.data.message
            } else {
                message = error.message
            }
            if (error.response.data.traces) {
                action.errorPageOpen(error.response.data)
            } else {
                ElMessage({
                    message: message,
                    type: 'error',
                    duration: 3 * 1000
                })
            }
        }
        return Promise.reject(error)
    }
)

export default service
