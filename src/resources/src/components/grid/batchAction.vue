<template>
    <span @click.stop="clickHandel">
        <slot></slot>
    </span>
</template>

<script>
    import {defineComponent} from "vue";
    import request from '@/utils/axios'
    import {ElMessageBox,ElMessage} from 'element-plus';
    export default defineComponent({
        name: "BatchAction",
        props:{
            url: String,
            ids:Array,
            gridParams:Object,
            params:Object,
            confirm:Object,
        },
        emits: ['gridRefresh'],
        setup(props,ctx){
            function clickHandel() {
                if(props.ids.length == 0){
                    return ElMessage('请勾选操作数据')
                }
                if(props.confirm){
                    ElMessageBox.confirm(props.confirm.message,props.confirm.title,{type:props.confirm.type}).then(()=>{
                        save()
                    })
                }else{
                    save()
                }
            }
            function save() {
                request({
                    url: props.url || '/eadmin/batch.rest',
                    method: 'put',
                    data: Object.assign(JSON.parse(JSON.stringify(props.gridParams)),props.params,{eadmin_ids:props.ids})
                }).then((res) => {
                    ctx.emit('gridRefresh')
                })
            }
            return {
                clickHandel
            }
        }
    })
</script>

<style scoped>

</style>
