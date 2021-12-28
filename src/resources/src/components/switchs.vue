<template>
    <a-switch @change="changeHandel" v-model:checked="value"></a-switch>
</template>

<script>
    import {defineComponent,ref,watch} from "vue";
    import request from '@/utils/axios'
    export default defineComponent({
        name: "EadminSwitch",
        props: {
            modelValue: [String,Number,Boolean],
            url: String,
            params:Object,
            field:String,
        },
        emits: ['update:modelValue','change'],
        setup(props,ctx){
            const value = ref(false)
            if(props.modelValue == ctx.attrs.activeValue) {
                value.value = true
            }
            watch(() => props.modelValue, val => {
                if(val == ctx.attrs.activeValue) {
                    value.value = true
                }else{
                    value.value = false
                }
            })
            function changeHandel(val) {
                if(props.url){
                    let failValue
                    if(val){
                        val = ctx.attrs.activeValue
                    }else{
                        val = ctx.attrs.inactiveValue
                    }
                    if (val == ctx.attrs.activeValue) {
                        failValue = ctx.attrs.inactiveValue
                    } else {
                        failValue = ctx.attrs.activeValue
                    }
                    let params = props.params
                    ctx.emit('change',val)
                    params[props.field] = val
                    request({
                        url:props.url,
                        method: 'put',
                        data: params
                    }).then(res=>{
                        ctx.emit('update:modelValue',val)
                    }).catch(res=>{
                        value.value = failValue
                        ctx.emit('update:modelValue',failValue)
                    })
                }else{
                    if(val){
                        ctx.emit('update:modelValue',ctx.attrs.activeValue)
                    }else{
                        ctx.emit('update:modelValue',ctx.attrs.inactiveValue)
                    }
                }
            }
            return {
                changeHandel,
                value
            }
        }
    })
</script>

<style lang="scss" scoped>
    @import '../styles/light';
    .ant-switch-checked {
        background-color: $--color-primary;
    }
</style>
