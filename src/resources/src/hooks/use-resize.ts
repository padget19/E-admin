import {onMounted,onBeforeUnmount} from "vue";
import {action} from '@/store'
const _isMobile = function(){
    return document.body.getBoundingClientRect().width - 1 < 992
}
function resizeHandler(){
    if (!document.hidden) {
        // @ts-ignore
        const isMobile = _isMobile()
        action.device(isMobile ? 'mobile' : 'desktop')
    }
}
export default function(){
    onMounted(()=>{
        resizeHandler()
        window.addEventListener('resize', resizeHandler)
    })
    onBeforeUnmount(()=>{
        window.removeEventListener('resize', resizeHandler)
    })
}

