<template>
    <el-card shadow="never" :body-style="{padding: '0px'}"  v-loading="loading">
        <template #header v-if="header"><slot name="header"></slot></template>
        <div class="tools" v-if="!hideTools">
            <render v-if="!hideAdd" :data="tools.addButton" :params="addParams" @success="getData"></render>
            <render v-if="!hideEdit && editUrl" :data="tools.editButton" :url="editUrl" @success="getData"></render>
            <render v-else-if="!hideEdit && !editUrl" :data="tools.edit" :url="editUrl" disabled></render>
            <el-button
                    v-if="!hideDel"
                    type="danger"
                    icon="el-icon-delete"
                    class="ele-btn-icon"
                    :disabled="!current"
                    @click="del"
                    size="small">删除
            </el-button>
        </div>
        <div class="search" v-if="!hideFilter">
            <el-input prefix-icon="el-icon-search" size="small" v-model="keyword"></el-input>
        </div>
        <div class="tree-group">
            <el-tree v-bind="tree.attribute" :data="treeData" :current-node-key="current" @node-click="onNodeClick"></el-tree>
        </div>
    </el-card>
</template>

<script>
    import {defineComponent,reactive,toRefs,computed} from "vue";
    import useHttp from "../../hooks/use-http";
    import { ElMessageBox} from 'element-plus'
    export default defineComponent({
        name: "EadminSidebarGrid",
        props: {
            hideAdd: Boolean,
            hideEdit: Boolean,
            hideDel: Boolean,
            hideFilter: Boolean,
            hideTools: Boolean,
            gridValue: Boolean,
            header: Boolean,
            tree: Object,
            default: {
              type:  [String, Number],
              default:'',
            },
            tools: [Object, Boolean],
            params:  {
                type: Object,
                default:{},
            },
            gridParams:{
              type: Object,
              default:{},
            },
            field: {
                type: String,
                default:'group_id',
            },
            dataSource :{
              type: Array,
              default:[],
            },
        },
        emits:['update:gridValue','update:gridParams','update:dataSource'],
        setup(props,ctx){
            ctx.emit('update:gridParams',props.params)
            const {loading,http} = useHttp()
            const state = reactive({
                current:props.default,
                keyword:'',
                editUrl:'',
                dataSource: [],
            })
            getData()
            const addParams = computed(()=>{
                let params = {}
                params[props.field] = state.current
                if(props.tools.addButton){
                    params =  Object.assign(props.tools.addButton.attribute.params,params)
                }
                return params
            })
            function onNodeClick(row) {
                state.current = row[props.tree.attribute.nodeKey]
                let params = {}
                params[props.field] = state.current
                if(state.current){
                    state.editUrl = '/eadmin/'+state.current+'/edit.rest'
                }else{
                    state.editUrl = ''
                }
                ctx.emit('update:gridParams',params)
                ctx.emit('update:gridValue',true)
            }
            function filterTree (tree, arr = []) {
                let name = props.tree.attribute.props.label
                let children = props.tree.attribute.props.children
                if (!tree.length) return []
                for (let item of tree) {
                    // 循环数组，然后过滤数据
                    // 如果该条数据type不为0时，跳出本次循环
                    if (item[name].indexOf(state.keyword) >-1){
                        // 如果满足条件时，用新node代替，然后把chilren清空
                        let node = {...item, children: []}
                        // 然后添加到新数组中
                        arr.push(node)
                        // 如果有子节点，调用递归函数，并把空数组传给下一个函数
                        // 利用引用数据类型的特性，实现浅拷贝
                        // 递归函数过滤时会改变这个空数组数据，从而实现层级结构过滤
                        if (item[children] && item[children].length) {
                            filterTree(item[children], node.children)
                        }
                    }else if (item[children] && item[children].length) {
                        filterTree(item[children], arr)
                    }
                }
                return arr
            }
            function getData() {
                let name = props.tree.attribute.props.label
                let id = props.tree.attribute.nodeKey
                let data = []
                let all = {}
                all[id] = ''
                all[name] = '全部'
                data.push(all)
                http({
                    url:'/eadmin.rest',
                    params:Object.assign({eadmin_sidebar_data:true},ctx.attrs.remoteParams)
                }).then(res=>{
                    state.dataSource = data.concat(res.data)
                })
            }
            function del() {
                ElMessageBox.confirm('确定要删除选中吗?', '提示',{
                    type: 'warning'
                }).then(()=>{
                    http({
                        url:'/eadmin.rest',
                        params: Object.assign({eadmin_sidebar_delete:true,id:state.current},ctx.attrs.remoteParams)
                    }).then(res=>{
                        getData()
                    })
                })
            }
            const treeData = computed(()=>{
              const data =  filterTree(state.dataSource)
              ctx.emit('update:dataSource',data)
              return data
            })
            return {
                del,
                loading,
                getData,
                addParams,
                treeData,
                onNodeClick,
                ...toRefs(state)
            }
        }
    })
</script>

<style scoped>
.tools{
    margin:10px 0;
    padding:0 20px;
}
.search{
    padding:0 20px;
    margin:10px 0;
}
/deep/ .tree-group .el-tree-node__content{
    height: 35px;
}
</style>
