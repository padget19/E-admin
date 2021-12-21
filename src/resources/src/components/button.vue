<template>
    <el-button @click="clickHandel" :loading="loading" ref="eadminButton"><slot></slot></el-button>
</template>

<script>
    import {defineComponent,nextTick,ref} from 'vue'
    import {useAjax} from '@/hooks'
    import Clipboard from 'clipboard'
    import {ElMessage} from 'element-plus'
    export default defineComponent({
        name: "EadminButton",
        emits: ['gridRefresh'],
        setup(props, ctx) {
            const eadminButton = ref('')
            const {loading,http} = useAjax()
            function clickHandel() {
                http(ctx)
            }
            if(ctx.attrs['data-clipboard-text']){
                nextTick(()=>{
                    const clipboard = new Clipboard(eadminButton.value.$el)
                    clipboard.on('success', e => {
                        ElMessage({
                            message: '复制成功',
                            type: 'success'
                        })
                    })
                    clipboard.on('error', e => {
                        ElMessage.error('复制失败')
                        clipboard.destroy()
                    })
                })
            }
            return {
                eadminButton,
                loading,
                clickHandel
            }
        }
    })
</script>

<style scoped>

</style>
