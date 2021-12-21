import {reactive} from "vue";
import request from '@/utils/axios'
import {findTree, appendCss, setObjectValue} from '@/utils'
export const store = Symbol()
// 使用 reactive 函数完成响应式转换
const states = reactive({
    device:'desktop',
    routerStatus: true,
    //侧边栏
    sidebar: {
        //打开关闭
        opend: true,
        //显示隐藏
        visible: true,
    },
    //主内容组件渲染
    mainLoading: false,
    mainComponent: [],
    mainComponentCss: [],
    mainTitle: '',
    mainDescription: '',
    component: null,
    //是否刷新
    refresh:false,
    //错误信息
    errorPage: {
        visable: false,
        data: null,
    },
    //个人信息
    info: {
        id: 0,
        init: [],
        webLogo: '',
        webName: '',
        dropdownMenu: [],
    },
    //菜单
    menus: [],
    menuModule: '',
    breadcrumb: [],
});
export const state = states
//操作方法
const action = {
    //刷新
    refresh(bool:boolean){
        states.refresh = bool
    },
    //设置面包屑
    setBreadcrumb(data: any) {
        states.breadcrumb = data
    },
    //侧边栏打开关闭
    sidebarOpen: function (bool: boolean) {
        states.sidebar.opend = bool
    },
    //显示隐藏侧边栏
    sidebarVisible: function (bool: boolean) {
        states.sidebar.visible = bool
    },
    //设置加载状态
    loading: function (bool: boolean) {
        states.mainLoading = bool
    },
    cacheCss(url:string,css){
        const index = action.getCacheCssIndex(url)
        if (index === -1) {
            states.mainComponentCss.push({
                // @ts-ignore
                url : url,
                // @ts-ignore
                css : [css],
            })
        }else{
            // @ts-ignore
            states.mainComponentCss[index].css.push(css)
        }
    },
    getCacheCssIndex(url: string) {
        return states.mainComponentCss.findIndex(item => {
            // @ts-ignore
            return item.url === url
        })
    },
    device(device:string){
        states.device = device
    },
    clearComponent(url: string) {
        let index = action.getComponentIndex(url)
        if(index > -1){
            states.mainComponent.splice(index, 1)
        }
        index = action.getCacheCssIndex(url)
        if(index > -1) {
            states.mainComponentCss.splice(index, 1)
        }
    },
    getComponentIndex(url: string) {
        return states.mainComponent.findIndex(item => {
            // @ts-ignore
            return item.url === url
        })
    },
    //设置主内容组件
    component: function (data: object, url: string) {
        state.component = null
        const index = action.getComponentIndex(url)
        if (index === -1) {
            // @ts-ignore
            const menu = findTree(state.menus, url.substr(1), 'url')
            if (menu) {
                states.mainComponent.push({
                    // @ts-ignore
                    title: menu.name || url,
                    // @ts-ignore
                    description: data.bind.eadmin_description || '',
                    // @ts-ignore
                    url: url,
                    // @ts-ignore
                    component: data,
                })
            } else {
                // @ts-ignore
                state.component = data
            }

            // @ts-ignore
            state.mainTitle = data.bind.eadmin_title || ''
            // @ts-ignore
            state.mainDescription = data.bind.eadmin_description || ''
        } else {
            const key = action.getCacheCssIndex(url)
            if(key > -1){
                // @ts-ignore
                states.mainComponentCss[key].css.forEach(css=>{
                    appendCss(url,css,false)
                })
            }

            // @ts-ignore
            state.mainTitle = states.mainComponent[index].title || ''
            //@ts-ignore
            state.mainDescription = states.mainComponent[index].description || ''
        }
        action.loading(false)
    },
    //关闭错误页面
    errorPageClose() {
        states.errorPage.visable = false
    },
    //打开错误页面
    errorPageOpen(data: object) {
        // @ts-ignore
        states.errorPage.data = data
        states.errorPage.visable = true
    },
    //选择头部菜单模块
    selectMenuModule(id: string) {
        states.menuModule = id
    },
    refreshToken() {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/admin/refreshToken'
            }).then((res: any) => {
                if (res.data.token) {
                    localStorage.setItem('eadmin_token', res.data.token)
                    localStorage.setItem('eadmin_token_expire', Math.round((new Date()).getTime() / 1000) + res.data.expire)
                    resolve(res)
                } else {
                    localStorage.removeItem('eadmin_token')
                    reject(res)
                }
            }).catch((res: any) => {
                reject(res)
            })
        })
    },
    getInfo() {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/admin/info'
            }).then((res: any) => {
                const info = res.data.info
                states.menus = res.data.menus
                if (info) {
                    states.component = null
                    states.info = info
                    states.info.webLogo = res.data.webLogo
                    states.info.webName = res.data.webName
                    states.info.dropdownMenu = res.data.dropdownMenu
                    states.info.init = res.data.init || []
                }
                resolve(res)
            }).catch((res: any) => {
                reject(res)
            })
        })
    },
    login(data: object) {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/login',
                method: 'post',
                data: data
            }).then((res: any) => {
                localStorage.setItem('eadmin_token', res.data.token)
                localStorage.setItem('eadmin_token_expire', Math.round((new Date()).getTime() / 1000) + res.data.expire)
                states.menuModule = ''
                states.mainComponent = []
                resolve(res)
            }).catch((res: any) => {
                reject(res)
            })
        })
    },
    logout() {
        return new Promise((resolve, reject) => {
            request({
                url: '/admin/login/logout'
            }).then((res: any) => {
                states.info.id = 0
                states.mainComponent = []
                localStorage.removeItem('eadmin_token')
                resolve(res)
            }).catch((res: any) => {
                reject(res)
            })
        })
    }
}
export {action}

