<template>
    <component :is="dialog" v-model="visible" v-bind="$attrs" custom-class="eadmin-dialog">
                    <template #title>
                        <slot name="title"></slot>
                    </template>
                    <slot></slot>
            <render :data="content" :slot-props="slotProps" @success="emitSuccess()"></render>
            <template #footer v-if="footer">
                <div v-if="footerShow" class="eadmin_footer">
                    <render v-for="item in action.leftAction" :data="item"></render>
                    <render v-if="action.submit" :loading="dialogRef.loading" :data="action.submit" :disabled="dialogRef.disabled"></render>
                    <render v-if="action.reset" :data="action.reset" @click="dialogRef.resetForm"></render>
                    <render v-if="action.cancel" :data="action.cancel" @click="dialogRef.cancelForm"></render>
                    <render v-for="item in action.rightAction" :data="item"></render>
                </div>
            </template>
    </component>
    <span @click.stop="open">
        <slot name="reference"></slot>
    </span>
</template>

<script>
    import {defineComponent, watch,computed ,reactive,toRefs,ref ,nextTick } from "vue";
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
        emits: ['update:modelValue','update:show','update:reRender','success',],
        setup(props, ctx) {
            const dialogRef = ref('')
            const state = reactive({
                footer:false,
                footerShow:false,
                action:{},
                formLoading:false,
                isEmitSuccess:false,
            })
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
               if(!value){
                   state.footerShow = false
                   state.action = {}
               }
            })
            watch(()=>props.show,(value)=>{
                if(value){
                    open()
                }
                ctx.emit('update:show',value)
            })
            watch(content,value=>{
                if(value.name == 'EadminForm'){
                    value.attribute.ref = dialogRef
                    state.isEmitSuccess = value.attribute.saveCloseDialog || false
                    state.action = value.attribute.action
                    if(!value.attribute.action.hide){
                        state.footer = true
                        value.attribute.action.hide = true
                        nextTick(()=>{
                            state.footerShow = true
                        })
                    }
                }
            })
            function open(){
                if(props.gridBatch && props.addParams.eadmin_ids.length == 0){
                    return ElMessage('请勾选操作数据')
                }
                if(!visible.value){
                    init = true
                    http(props,true)
                }
            }
            const dialog = computed(()=>{
                if(visible.value || init){
                    return 'ElDialog'
                }else{
                    return null
                }
            })
            function emitSuccess() {
                if(state.isEmitSuccess){
                  hide()
                  ctx.emit('success')
                }
            }
            return {
                emitSuccess,
                dialogRef,
                ...toRefs(state),
                dialog,
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
    .eadmin-dialog .el-button{
      margin-right: 10px;
    }
</style>
