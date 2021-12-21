<template>
    <el-tree :class="[horizontal ? 'eadmin-tree':'']"  :current-node-key="modelValue" @node-click='handelClick' @check="handleCheckChange" :default-checked-keys="defaultChecked"></el-tree>
</template>

<script>
    import {defineComponent} from "vue";
    export default defineComponent({
        name: "EadminTree",
        props: {
            modelValue:[Array,Object,String],
            horizontal:Boolean,
        },
        emits: ['update:modelValue'],
        setup(props, ctx) {
            const defaultChecked = [];
            if(Array.isArray(props.modelValue)){
                props.modelValue.forEach(item=>{
                    checked(item,ctx.attrs.data,defaultChecked)
                })
            }

            function checked(id,data,newArr){
                data.forEach(item => {
                    if(item.id == id){
                        if(!item.children){
                            newArr.push(item.id)
                        }
                    }else{
                        if(item.children &&  item.children.length > 0 ){
                            checked(id,item.children,newArr)
                        }
                    }
                });
            }
            function handleCheckChange(node,{checkedKeys,halfCheckedKeys}){
                ctx.emit('update:modelValue',checkedKeys.concat(halfCheckedKeys))
            }
            function handelClick(data) {
                if(!ctx.attrs.showCheckbox){
                    ctx.emit('update:modelValue',data.id)
                }
            }
            return {
                handelClick,
                defaultChecked,
                handleCheckChange
            }
        }
    })
</script>

<style >
    .eadmin-tree .el-tree-node__content{
        margin-bottom:10px;

    }

    .eadmin-tree .el-tree-node__children .el-tree-node__children .el-tree-node__content{
        float:left;
    }
</style>
