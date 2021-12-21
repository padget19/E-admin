<template>
    <component :is="dialog" v-model="visible" v-bind="$attrs" custom-class="eadmin-dialog">
                    <template #title>
                        <slot name="title"></slot>
                    </template>
                    <slot></slot>
           <render :data="content" :slot-props="slotProps" @success="hide"></render>
    </component>
    <span @click.stop="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent, watch,computed} from "vue";
    import {useVisible} from '@/hooks'
    import {ElMessage} from "element-plus";

    export default defineComponent({
        name: "EadminDialog",
        inheritAttrs: false,
        props: {
            modelValue: {
                type: Boolean,
                default: false,
            },
            show:Boolean,
            gridBatch:Boolean,
            url: String,
            params:{
                type:Object,
                default:{}
            },
            reRender:Boolean,
            addParams:{
                type:Object,
                default:{}
            },
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
        emits: ['update:modelValue','update:show','update:reRender'],
        setup(props, ctx) {
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
                ctx.emit('update:show',value)
            })
            function open(){
                if(props.gridBatch  && props.addParams.eadmin_ids.length == 0){
                    return ElMessage('请勾选操作数据')
                }
                init = true
                http(props)
            }
            const dialog = computed(()=>{
                if(visible.value || init){
                    return 'ElDialog'
                }else{
                    return null
                }
            })
            return {
                dialog,
                hide,
                content,
                visible,
                open,
            }
        }
    })
</script>

<style>
    .eadmin-dialog{
        display: flex;
        flex-direction: column;
        margin-top: 4vh !important;
        max-height: 90vh !important;
        overflow: hidden;
    }
    .eadmin-dialog .el-dialog__body{
        overflow: auto;
    }
</style>
