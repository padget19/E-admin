<template>
    <div ref="filesystem">
    <el-card class="filesystem" shadow="never">
        <template #header>
            <el-row style="display: flex;align-items: center;justify-content: space-between">
                <el-col :md="16" :xs="24" >
                    <el-button-group style="display: flex">
                        <el-button icon="el-icon-back" size="mini" @click="back"></el-button>
                        <div class="breadcrumb">
                            <el-breadcrumb separator-class="el-icon-arrow-right" style="display: flex;white-space: nowrap;">
                                <el-breadcrumb-item @click="changePath(initPath)" style="cursor: pointer">根目录</el-breadcrumb-item>
                                <el-breadcrumb-item v-for="item in breadcrumb" @click="changePath(item.path)"
                                                    :style="item.path ? 'cursor: pointer':''">{{item.name}}
                                </el-breadcrumb-item>
                            </el-breadcrumb>
                        </div>
                        <el-button icon="el-icon-refresh" size="mini" @click="loading = true"></el-button>
                        <render :data="upload" :drop-element="filesystem"  :save-dir="savePath" :on-progress="uploadProgress" @success="uploadSuccess"></render>
                        <el-button  size="mini" @click="mkdir">新建文件夹</el-button>
                        <el-button  size="mini" type="danger" v-if="selectPaths.length > 0" @click="delSelect">删除选中</el-button>
                    </el-button-group>
                </el-col>
                <el-col :md="8" :xs="24" style="display: flex;">
                    <!--快捷搜索-->
                    <el-input class="hidden-md-and-down" v-model="quickSearch" clearable prefix-icon="el-icon-search"
                              size="mini" style="margin-right: 10px;flex: 1" placeholder="请输入关键字" @change="loading = true" ></el-input>
                    <el-button class="hidden-md-and-down" type="primary" size="mini" icon="el-icon-search" @click="loading = true">搜索</el-button>
                    <el-tooltip v-if="showType === 'grid'" content="列表排序">
                        <el-button icon="el-icon-s-grid"  size="mini" @click="showType='menu'"></el-button>
                    </el-tooltip>
                    <el-tooltip v-else content="图标排序">
                        <el-button icon="el-icon-menu" size="mini" @click="showType='grid'"></el-button>
                    </el-tooltip>
                </el-col>

            </el-row>
        </template>
        <div>
            <a-table v-if="showType === 'grid'" :scroll="{y:height?height:'calc(100vh - 320px)'}" :locale="{emptyText:'暂无数据'}"  row-key="url" :pagination="false" :row-selection="rowSelection" :columns="tableColumns" :data-source="tableData" :loading="loading" :custom-row="customRow">
                <template #name="{ text , record , index }">
                    <div class="filename" @click="changePath(  record.path,record.dir)">
                        <el-image :src="record.url" :preview-src-list="[record.url]"
                                  style="width: 32px;height: 32px;margin-right: 10px">
                            <template #error >
                                <el-image :src="fileIcon(record.dir ? '.dir':text)"
                                          style="width: 32px;height: 32px;margin-right: 10px">
                                    <template #error >
                                        <div style="display: flex; align-items: center;"><i class="el-icon-document"
                                                                                            style="font-size: 32px"/></div>
                                    </template>
                                </el-image>
                            </template>
                        </el-image>
                        {{ text }}
                    </div>
                </template>
                <template #action="{ record }" >
                    <div v-show="mouseenterIndex == record.path">
                        <el-button icon="el-icon-folder-opened" size="mini" round v-if="record.dir" @click="rename(record.path)">重命名</el-button>
                        <el-button icon="el-icon-download" size="mini" round v-else @click="link(record.download)">下载</el-button>
                        <el-button icon="el-icon-delete"  type="danger" size="mini" round @click="del(record.path)">删除</el-button>
                    </div>
                </template>
            </a-table>
            <div v-else class="menuGrid" :style="{height:height?height:'calc(100vh - 280px)'}">
                <el-row v-loading="loading">

                    <el-col class="menuBox" :lg="4" :md="6" :sm="6" :xs="12" v-for="item in tableData"  @mouseenter="mouseenterIndex = item.path" @mouseleave="mouseenterIndex=''" @click="select(item.url)">

                        <div :class="[selectIds.indexOf(item.url) !== -1?'selected':'','item']">
                            <i class="el-icon-circle-check" v-if="selectIds.indexOf(item.url) !== -1"></i>
                            <el-image :src="item.url" :preview-src-list="[item.url]"
                                      style="width: 80px;height: 80px;margin-right: 10px" @click="changePath(item.path,item.dir )">
                                <template #error >
                                    <el-image :src="fileIcon(item.dir ? '.dir':item.name)"
                                              style="width: 80px;height: 80px;margin-right: 10px" @click="changePath(item.path,item.dir )">
                                        <template #error >
                                            <div style="display: flex; align-items: center;"><i class="el-icon-document" style="font-size: 80px"/></div>
                                        </template>
                                    </el-image>
                                </template>
                            </el-image>
                            <div class="text"> {{ item.name }}</div>
                        </div>

                        <div class="tool" v-show="mouseenterIndex == item.path">
                                <el-button icon="el-icon-folder-opened" size="mini" round v-if="item.dir" @click="rename(item.path)">重命名</el-button>
                                <el-button icon="el-icon-download" size="mini" round v-else @click="link(item.download)">下载</el-button>
                                <el-button icon="el-icon-delete"  type="danger" size="mini" round @click="del(item.path)">删除</el-button>
                        </div>
                    </el-col>

                </el-row>
            </div>
            <!--分页-->
            <el-pagination @size-change="handleSizeChange"
                           @current-change="handleCurrentChange"
                           layout="total, sizes, prev, pager, next, jumper"
                           :total="total"
                           :page-size="size"
                           :current-page="page">
            </el-pagination>
        </div>
    </el-card>
    </div>
</template>

<script>
    import {computed, defineComponent, reactive, toRefs, onActivated, watch,ref} from "vue";
    import {deleteArr, fileIcon, unique,link} from '@/utils'
    import {useHttp} from "@/hooks";
    import {ElMessageBox,ElLoading} from 'element-plus';
    export default defineComponent({
        name: "EadminFileSystem",
        props: {
            modelValue: [String, Array],
            data: Array,
            initPath: String,
            upload:Object,
            total:Number,
            limit: {
                type: Number,
                default: 0
            },
            multiple:{
                type:Boolean,
                default: true
            },
            height:{
                type:[String,Boolean,Number],
                default: false
            },
            selection:{
                type:Array,
                default:[]
            },
            display:{
                type: String,
                default: 'grid'
            },
        },
        emits: ['update:modelValue','update:selection'],
        setup(props,ctx) {
            onActivated(()=>{
                loadData()
            })
            const {loading, http} = useHttp()
            const filesystem = ref('')

            const state = reactive({
                tableColumns: [
                    {
                        title: '文件名',
                        dataIndex: 'name',
                        slots: {customRender: 'name'},
                    },
                    {
                        title: '大小',
                        width:100,
                        dataIndex: 'size',
                    },
                    {
                        title: '权限',
                        width:100,
                        dataIndex: 'permission',
                    },
                    {
                        title: '所有者',
                        width:100,
                        dataIndex: 'author',
                    },
                    {
                        title: '修改时间',
                        width:180,
                        dataIndex: 'update_time',
                    },
                    {
                        title: '操作',
                        width:210,
                        align:'right',
                        dataIndex: 'action',
                        slots: {customRender: 'action'}
                    }
                ],
                tableData: props.data,
                path: props.initPath,
                quickSearch:'',
                mouseenterIndex:'',
                showType:props.display,
                page:1,
                size:100,
                total:props.total,
                selectIds:props.selection,
                selectPaths:[]
            })
            watch(() => props.modelValue, (value) => {
                if (value) {
                    loading.value = true
                }
            })
            watch(() => state.path, value => {
                loading.value = true
            })
            watch(loading, (value) => {
                if (value) {
                    loadData()
                }
            })

            watch(()=>props.selection,value=>{
                state.selectIds  = value
            })
            function customRow(record){
                return {
                    onMouseenter:event=>{
                        state.mouseenterIndex = record.path
                    },
                    onMouseleave:event=>{
                        state.mouseenterIndex = ''
                    }
                }
            }
            function loadData() {
                const requestParams = {
                    page:state.page,
                    size:state.size,
                    search: state.quickSearch,
                    path: state.path,
                    ajax_request_data: 'page',
                }
                http({
                    url: '/filesystem',
                    params: requestParams
                }).then(res => {
                    state.tableData = res.data
                    state.total = res.total
                })
            }
            function delSelect() {
                del(state.selectPaths)
            }
            //删除
            function del(path) {
                if(!Array.isArray(path)){
                    path = [path]
                }
                ElMessageBox.confirm('确认删除? 不可恢复操作!','警告',{type:'error'}).then(()=>{
                    http({
                        url:'filesystem/del',
                        method:'delete',
                        data:{
                            paths:path
                        }
                    }).then(res=>{
                        loadData()
                    })
                })

            }
            //改变目录
            function changePath(path,isDir = true) {
                if (path && isDir) {
                    state.path = path
                }
            }
            //后退
            function back() {
                const arr = state.path.split('/').filter(item => {
                    return item
                })
                const initPath = props.initPath.split('/').filter(item => {
                    return item
                })
                arr.pop()
                if (arr.length >= initPath.length) {
                    state.path = '/' + arr.join('/')
                }
            }
            //新建文件夹
            function mkdir() {
                ElMessageBox.confirm('新建文件夹','',{showInput:true}).then(({value})=>{
                    const {http} = useHttp()
                    http({
                        url:'filesystem/mkdir',
                        method:'post',
                        data:{
                            path:state.path+'/'+value
                        }
                    }).then(res=>{
                        loadData()
                    })
                })
            }
            //重命名文件夹
            function rename(path) {
                ElMessageBox.confirm('重命名文件夹','',{showInput:true}).then(({value})=>{
                    const {http} = useHttp()
                    http({
                        url:'filesystem/rename',
                        method:'post',
                        data:{
                            name:value,
                            path:path,
                        }
                    }).then(res=>{
                        loadData()
                    })
                })
            }
            //选择
            function select(url) {

                if(props.multiple){
                    if(state.selectIds.indexOf(url)  === -1){
                        if(props.limit > 0 && state.selectIds.length >= props.limit){
                            return false
                        }
                        state.selectIds.push(url)
                    }else{
                        deleteArr(state.selectIds,url)
                    }
                }else{
                    state.selectIds = [url]
                }
                ctx.emit('update:selection',state.selectIds)
            }
            const savePath = computed(()=>{
                const path = state.path +'/'
                return path.replace(props.initPath,'')
            })
            //上传进度
            let uploadProgressLoading = null
            function uploadProgress(progress) {
                if(uploadProgressLoading){
                    uploadProgressLoading.setText('上传中 '+progress+'%')
                }else{
                    uploadProgressLoading = ElLoading.service({
                        target:'.filesystem',
                        text:'上传中 '+progress+'%',
                    })
                }
            }
            //上传成功
            function uploadSuccess() {
                loading.value  = true
                if(uploadProgressLoading){
                    uploadProgressLoading.close()
                }
            }
            const breadcrumb = computed(() => {
                const arr = state.path.split('/').filter(item => {
                    return item
                })
                const initPath = props.initPath.split('/').filter(item => {
                    return item
                })
                const breadcrumb = []
                let paths = []
                let path = ''
                for (let i = 0; i < arr.length; i++) {
                    paths = []
                    for (let k = 0; k <= i; k++) {
                        paths.push(arr[k])
                    }
                    if (paths.length < initPath.length) {
                        path = ''
                    } else {
                        path = '/' + paths.join('/')
                    }
                    breadcrumb.push({
                        name: arr[i],
                        path: path,
                    })
                }
                return breadcrumb
            })

            const rowSelection = computed(() => {
                if (props.hideSelection) {
                    return null
                } else {
                    return {
                        selectedRowKeys: state.selectIds,
                        type:props.multiple?'checkbox':'radio',
                        //当用户手动勾选数据行的 Checkbox 时触发的事件
                        onSelect: (record, selected, selectedRows, nativeEvent) => {
                            const ids = selectedRows.map(item => {
                                return item.url
                            })
                            const paths = selectedRows.map(item => {
                                return item.path
                            })
                            if (selected) {
                                if(props.multiple){
                                    if(props.limit > 0 && state.selectIds.length >= props.limit){
                                        return false
                                    }
                                    state.selectPaths =  unique(state.selectPaths.concat(paths))
                                    state.selectIds = unique(state.selectIds.concat(ids))
                                }else{
                                    state.selectIds = ids
                                    state.selectPaths = paths
                                }
                            } else {
                                deleteArr(state.selectPaths, record.path)
                                deleteArr(state.selectIds, record.url)
                            }

                            ctx.emit('update:selection',state.selectIds)
                        },
                        onSelectAll: (selected, selectedRows, changeRows) => {
                            const ids = selectedRows.map(item => {
                                return item.url
                            })
                            const paths = selectedRows.map(item => {
                                return item.path
                            })
                            if (selected) {
                                if(props.limit > 0 && (state.selectIds.length+ids.length) >= props.limit){
                                    return false
                                }
                                state.selectPaths = unique(state.selectPaths.concat(paths))
                                state.selectIds = unique(state.selectIds.concat(ids))
                            } else {
                                changeRows.map(item => {
                                    deleteArr(state.selectPaths, item.path)
                                    deleteArr(state.selectIds, item.url)
                                })
                            }
                            ctx.emit('update:selection',state.selectIds)
                        },
                    }
                }
            })

            //分页大小改变
            function handleSizeChange(val) {
                state.page = 1
                state.size = val
                loading.value = true
            }
            //分页改变
            function handleCurrentChange(val) {
                state.page = val
                loading.value = true
            }
            return {
                handleSizeChange,
                handleCurrentChange,
                customRow,
                link,
                delSelect,
                del,
                uploadProgress,
                uploadSuccess,
                back,
                mkdir,
                select,
                rename,
                breadcrumb,
                changePath,
                loading,
                rowSelection,
                fileIcon,
                savePath,
                ...toRefs(state),
                filesystem
            }
        }
    })
</script>

<style scoped lang="scss">
    .filename {
        display: flex;
        align-items: center;
        height: 35px;
        cursor: pointer;
    }

    .breadcrumb {
        height:28px;
        background-color: #f3f3f3;
        padding: 0 10px;
        display: flex;
        align-items: center;
        border: solid 1px #cccccc;
        overflow: auto;

    }

    .menuGrid{
        width: 100%;
        height: calc(100vh - 280px);
        overflow-y: auto;
    }
    .menuBox .item{
        text-align: center;
        margin-bottom: 30px;
        cursor: pointer;
        transition: 0.3s all;
        position: relative;
    }
    .menuBox .item .text{
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .menuBox .tool{
        display: flex;
        margin-bottom: 30px;
    }
    .menuBox .item:hover{
        background: #F8F9FB;
        border: solid 1px #A7D0FB;
        padding: 10px;
        margin-bottom: 5px;
        border-radius: 5px;
    }
    .menuGrid .selected{
        background: #F8F9FB;
        border: solid 1px #A7D0FB;
        padding: 10px;
        margin-bottom: 5px;
        border-radius: 5px;
    }
    .menuGrid .selected i{
        color: #13ce66;
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>
