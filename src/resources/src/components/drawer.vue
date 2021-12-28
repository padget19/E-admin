<template>
    <component :is="drawer" v-model="visible" v-bind="$attrs" custom-class="eadmin-drawer">
        <template #title>
            <slot name="title"></slot>
        </template>
        <slot></slot>
        <render :data="content" :slot-props="slotProps" @success="hide"></render>
    </component>
    <span @click="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {computed, defineComponent, watch} from "vue";
    import {useVisible, useHttp} from '@/hooks'
    import {ElMessage} from "element-plus";
    export default defineComponent({
        name: "EadminDrawer",
        inheritAttrs: false,
        props: {
            modelValue: {
                type: Boolean,
                default: false,
            },
            url: String,
            params:{
                type:Object,
                default:{}
            },
            gridBatch:Boolean,
            addParams:{
                type:Object,
                default:{}
            },
            reRender:Boolean,
            //请求method
            method: {
                type: String,
                default: 'get'
            },
            slotProps:{
                type:Object,
                default:{},
            },
        },
        emits:['update:modelValue','update:show','update:reRender'],
        setup(props,ctx){
            if(ctx.attrs.eadmin_popup){
                props.slotProps.eadmin_popup = ctx.attrs.eadmin_popup
            }
            const {visible,hide,useHttp} = useVisible(props,ctx)
            const {content,http} = useHttp()
            let init = false
            watch(()=>props.reRender,value=>{
                if(value){
                    http(props,false)
                    ctx.emit('update:reRender',false)
                }
            })
            watch(()=>props.modelValue,(value)=>{
                if(visible.value && !value){
                    hide()
                }
            })
            watch(()=>props.show,(value)=>{
                if(value){
                    open()
                }
            })
            function open(){
                if(props.gridBatch && props.addParams.eadmin_ids.length == 0){
                    return ElMessage('请勾选操作数据')
                }
                init = true
                http(props)
            }
            const drawer = computed(()=>{
                if(visible.value || init){
                    return 'ElDrawer'
                }else{
                    return null
                }
            })
            return {
                drawer,
                open,
                visible,
                content,
                hide
            }
        }
    })
</script>

<style scoped>
.eadmin-drawer .el-button{
  margin-right: 10px;
}
</style>
