<template>
    <el-select v-model="value" ref="selectEl" @visible-change="visibleChange">
          <el-option v-if="treeLabel" :label="treeLabel" :value="treeValue"></el-option>
          <template #empty v-if="tree">
            <el-tree
                node-key="id"
                :data="options"
                default-expand-all
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
    import {findTree} from '@/utils'
    export default defineComponent({
        name: "EadminSelect",
        props:{
            tree:Boolean,
            params: Object,
            modelValue:[Object,Array,String,Number],
            loadOptionField:[Object,Array,String,Number],
            loadField:[Object,Array,String,Number],
            options:[Object,Array,String,Number],
        },
        emits:['update:modelValue','update:loadField','update:loadOptionField'],
        setup(props,ctx){
            const state = reactive({
              treeValue:'',
              treeLabel:'',
              select:null,
            })
            const value = ref(props.modelValue)
            const selectEl = ref()
            let loadFieldValue = props.loadField
            watch(()=>props.modelValue,val=>{
                value.value = val
                initClearValue()
                changeHandel(val)
            })
            watch(()=>props.options,val=>{
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
               state.treeLabel = node.label
               state.treeValue = node.id
               value.value = node.id
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
