<template>
    <el-select v-model="value" ref="selectEl" @visibleChange="visibleChange">
          <el-option v-if="!multiple && treeLabel" :label="treeLabel" :value="treeValue"></el-option>
          <template #empty v-if="tree">
            <el-tree
                class="eadmin-select-tree"
                :node-key="treeProps.value"
                :current-node-key="value"
                :data="treeData"
                :show-checkbox="multiple"
                default-expand-all
                :props="treeProps"
                :expandOnClickNode="false"
                @node-click="handleNodeClick"
            ></el-tree>
          </template>
          <slot></slot>
         <template #prefix><slot name="prefix"></slot></template>
    </el-select>
</template>

<script>
    import {defineComponent, watch, ref, reactive, toRefs} from "vue";
    import request from '@/utils/axios'
    import {findTree,treeData} from '@/utils'
    import {findArrKey} from "../utils";
    export default defineComponent({
        name: "EadminSelect",
        props:{
            tree:Boolean,
            params: Object,
            treeProps: {
              type:Object,
              default:{
                value:'id',
                children:'children',
                label:'label',
                pid:'pid',
              }
            },
            modelValue:[Object,Array,String,Number],
            loadOptionField:[Object,Array,String,Number],
            loadField:[Object,Array,String,Number],
            options:{
              type:[Object,Array,String,Number],
              default:[],
            },
        },
        emits:['update:modelValue','update:loadField','update:loadOptionField'],
        setup(props,ctx){
            const state = reactive({
              treeValue:props.modelValue,
              treeLabel:'',
              select:null,
              treeData:[],
              multiple:ctx.attrs.multiple || false
            })

            const value = ref(props.modelValue)
            const selectEl = ref()
            let loadFieldValue = props.loadField
            if(props.tree){
              const find = findTree(props.options,props.modelValue,'id')
              if(find){
                state.treeLabel = find[props.treeProps.label]
              }
              if(props.options){
                state.treeData = treeData(props.options,props.treeProps.value,props.treeProps.pid,props.treeProps.children)
              }
            }
            watch(()=>props.modelValue,val=>{
                value.value = val
                initClearValue()
                changeHandel(val)
            })
            watch(()=>props.options,val=>{
                if(props.tree){
                  state.treeData = treeData(val,props.treeProps.value,props.treeProps.pid,props.treeProps.children)
                }
                initClearValue()
            })
            watch(value,value=>{
                ctx.emit('update:modelValue',value)
            })
            initClearValue()
            changeHandel(value.value)
            function initClearValue() {
                if(!ctx.attrs.multiple && !findTree(props.options,value.value,'id')){
                    value.value = ''
                }
            }
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
                            if(Array.isArray(loadFieldValue)){
                                loadFieldValue = loadFieldValue.filter(selectVal=>{
                                    return findTree(res.data,selectVal,'id')
                                })
                                ctx.emit('update:loadField',loadFieldValue)
                            }else if(findTree(res.data,loadFieldValue,'id')){
                                ctx.emit('update:loadField',loadFieldValue)
                            }else{
                                ctx.emit('update:loadField','')
                            }
                        })
                    }
                }
            }
            function handleNodeClick(node){
               state.treeLabel = node[props.treeProps.label]
               state.treeValue = node[props.treeProps.value]
               value.value = node[props.treeProps.value]
               selectEl.value.blur()
            }
            function visibleChange(bool){
              if(bool){
                state.treeLabel = ''
              }
            }
            return {
                visibleChange,
                selectEl,
                handleNodeClick,
                changeHandel,
                value,
                ...toRefs(state)
            }
        }
    })
</script>

<style scoped>

</style>
