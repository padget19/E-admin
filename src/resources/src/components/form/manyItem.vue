<template>
    <el-divider content-position='left' v-if="title && !table">{{title}}</el-divider>
    <el-form-item :label="title" v-if="table">
        <a-table row-key="id" v-if="value.length > 0" :data-source="value" size="small"  :pagination="false" :custom-row="customRow" class="manyItemEadminTable">
            <a-table-column v-for="column in columns" :title="column.title" :data-index="column.prop">
                <template #default="{ record , index}">
                    <render :slot-props="{ row:record ,$index:index ,propField:field,validator:$attrs.validator}" :data="column.component"></render>
                </template>
            </a-table-column>
            <a-table-column :width="70">
                <template #default="{ record , index}">
                    <el-space size="5">
                        <i class="el-icon-arrow-up" style="cursor: pointer;" @click="handleUp(index)" v-show='hoverIndex == index && value.length > 1 && index > 0'></i>
                        <i class="el-icon-arrow-down" style="cursor: pointer;" v-show='hoverIndex == index && value.length > 1 && index < value.length-1' @click="handleDown(index)"></i>
                        <i class="el-icon-error" style="cursor: pointer;color: red" v-show='hoverIndex == index && value.length > 0' @click="remove(index)"></i>
                    </el-space>
                 </template>
            </a-table-column>
        </a-table>
        <el-button size="mini" type='primary' plain @click="add" v-if="limit == 0 || limit > value.length">新增</el-button>
    </el-form-item>
    <div v-else>
        <div v-for="(item,index) in value">
            <slot :row="item" :$index="index" :prop-field="field" :validator="$attrs.validator"></slot>
            <el-form-item v-if="!disabled">
                <el-button size="mini" v-if="value.length - 1 == index && (limit == 0 || limit > value.length)" type='primary' plain @click="add">新增</el-button>
                <el-button size="mini" type='danger' v-show='value.length > 0' @click="remove(index)">移除</el-button>
                <el-button size="mini" @click="handleUp(index)" v-show='value.length > 1 && index > 0'>上移</el-button>
                <el-button size="mini" v-show='value.length > 1 && index < value.length-1' @click="handleDown(index)">下移</el-button>
            </el-form-item>
            <el-divider></el-divider>
        </div>
        <el-form-item v-if="value.length == 0 && !disabled">
            <el-button size="mini" type='primary' plain @click="add">新增</el-button>
        </el-form-item>
    </div>
</template>

<script>
    import {defineComponent,reactive,watch,ref} from "vue";
    export default defineComponent({
        name: "EadminManyItem",
        inheritAttrs:false,
        props: {
            title:String,
            modelValue: Array,
            field:String,
            limit:{
                type:Number,
                default:0,
            },
            columns: Array,
            manyData:Object,
            disabled:Boolean,
            table:Boolean,
        },
        emits:['update:modelValue'],
        setup(props,ctx){
            const value = reactive(props.modelValue)
            const hoverIndex = ref(-1)
            watch(value,(val)=>{
                ctx.emit('update:modelValue',val)
            })
            // 上移
            function handleUp (index) {
                const len = value[index - 1]
                value[index - 1] = value[index]
                value[index] = len
            }
            // 下移
            function handleDown (index) {
                const len = value[index + 1]
                value[index + 1] = value[index]
                value[index] = len
            }
            //添加元素
            function add(){
                value.push({...props.manyData})
            }
            //移除元素
            function remove(index){
                value.splice(index, 1)
            }
            function customRow(record,index) {
                return {
                    onMouseenter: (event) => {
                        hoverIndex.value = index
                    },
                    onMouseleave: (event) => {
                        hoverIndex.value = -1
                    }
            };
            }
            return {
                value,
                add,
                remove,
                handleUp,
                handleDown,
                customRow,
                hoverIndex,
            }
        }
    })
</script>

<style>
.manyItemEadminTable .ant-table{
    border-left: 1px solid #ededed;
    border-right: 1px solid #ededed;
    clear: none;
}
</style>
