<template>
    <el-select v-model="value">
        <slot></slot>
        <template #prefix><slot name="prefix"></slot></template>
    </el-select>
</template>

<script>
    import {defineComponent,watch,ref} from "vue";
    import request from '@/utils/axios'
    import {findTree} from '@/utils'
    export default defineComponent({
        name: "EadminSelect",
        props:{
            params: Object,
            modelValue:[Object,Array,String,Number],
            loadOptionField:[Object,Array,String,Number],
            loadField:[Object,Array,String,Number],
            options:[Object,Array,String,Number],
        },
        emits:['update:modelValue','update:loadField','update:loadOptionField'],
        setup(props,ctx){
            const value = ref(props.modelValue)
            const loadFieldValue = props.loadField
            watch(()=>props.modelValue,val=>{
                value.value = val
                changeHandel(val)
            })
            watch(value,value=>{
                ctx.emit('update:modelValue',value)
            })
            if(!ctx.attrs.multiple && !findTree(props.options,value.value,'id')){
                value.value = ''
            }
            changeHandel(value.value)
            function changeHandel(val) {
                if(props.params){
                    ctx.emit('update:loadField','')
                    ctx.emit('update:loadOptionField',[])
                    if(val){
                        request({
                            url: '/eadmin.rest',
                            params: Object.assign(props.params, {eadminSelectLoad: true, eadmin_id: val}),
                        }).then(res=>{
                            ctx.emit('update:loadOptionField',res.data)
                            if(findTree(res.data,loadFieldValue,'id')){
                                ctx.emit('update:loadField',loadFieldValue)
                            }else{
                                ctx.emit('update:loadField','')
                            }
                        })
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

<style scoped>

</style>
