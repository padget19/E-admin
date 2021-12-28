import request from '@/utils/axios'
// @ts-ignore
import {ref} from "vue"
const useAjax =  function () {
    const loading = ref(false)
    const http =  function ( ctx :any) {
        if (ctx.attrs.url) {
            loading.value = true
            request({
                url: ctx.attrs.url,
                method: ctx.attrs.method || 'post',
                data: ctx.attrs.params
            }).then((res:any) => {
                ctx.emit('gridRefresh')
            }).finally(()=>{
                loading.value = false
            })
        }
    }
    return {loading,http}
}

export default useAjax
