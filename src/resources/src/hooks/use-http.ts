import request from '@/utils/axios'
import {ref} from "vue"
const useHttp =  function () {
    const loading = ref(false)
    const http =  function (params:object) {
        return new Promise((resolve, reject) =>{
            loading.value = true
            request(params).then((res:any)=>{
                resolve(res)
            }).catch((res:any)=>{
                reject(res)
            }).finally(()=>{
                loading.value = false
            })
        })
    }
    return {loading,http}
}
export default useHttp
