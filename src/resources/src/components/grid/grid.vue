<template>
    <div class="grid">

        <!--工具栏-->
        <div :class="['tools',custom?'custom':'']" v-if="!hideTools">
            <el-row style="padding-top: 10px">
                <el-col :md="5" style="display: flex;margin-bottom: 10px" v-if="quickSearchOn">
                    <!--快捷搜索-->
                    <el-input class="hidden-md-and-down" v-model="quickSearch" clearable prefix-icon="el-icon-search"
                              size="small" style="margin-right: 10px;flex: 1" :placeholder="quickSearchText" @change="handleFilter"  @keyup.enter="handleFilter">
                        <template #append>
                            <el-button class="hidden-md-and-down searchButton" type="primary" size="small" @click="handleFilter">搜索</el-button>
                        </template>
                    </el-input>
                </el-col>
                <el-col :md="quickSearchOn ? 15:20" style="margin-bottom: 10px">
                    <!--添加-->
                    <render v-if="addButton" :data="addButton" :slot-props="grid"></render>
                    <!--导出-->
                    <el-dropdown trigger="click" v-if="export">
                        <el-button type="primary" size="small" icon="el-icon-download">
                            导出<i class="el-icon-arrow-down el-icon--right"></i>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click.native="exportData('page')">导出当前页</el-dropdown-item>
                                <el-dropdown-item @click.native="exportData('select')" v-show="selectIds.length > 0">导出选中行</el-dropdown-item>
                                <el-dropdown-item @click.native="exportData('all')">导出全部</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                    <el-button plain size="small" icon="el-icon-delete" v-if="!hideDeleteSelection && selectIds.length > 0" @click="deleteSelect">删除选中</el-button>
                    <el-button plain size="small" icon="el-icon-help" v-if="trashed && selectIds.length > 0" @click="recoverySelect">恢复选中</el-button>
                    <el-button type="danger" size="small" icon="el-icon-delete" v-if="!hideDeleteButton" @click="deleteAll()">{{trashed && !hideTrashed?'清空回收站':'清空数据'}}</el-button>

                    <render v-for="tool in tools" :data="tool" :ids="selectIds" :add-params="{eadmin_ids:selectIds}" :grid-params="params" :slot-props="grid"></render>
                </el-col>
                <el-col :md="4" >
                    <div style="float: right;margin-left: 15px">
                        <el-tooltip placement="top" :content="filterShow?'收起筛选':'展开筛选'"  v-if="filter">
                            <el-button size="mini" icon="el-icon-search" circle @click="visibleFilter"></el-button>
                        </el-tooltip>
                        <el-tooltip placement="top" :content="trashed?'数据列表':'回收站'"  v-if="!hideTrashed">
                            <el-button :type="trashed?'primary':'info'" size="mini" circle :icon="trashed?'el-icon-s-grid':'el-icon-delete'" @click="trashedHandel"></el-button>
                        </el-tooltip>
                        <!--刷新-->
                        <el-button icon="el-icon-refresh" size="mini" circle style="margin-right: 10px"
                                   @click="loading=true"></el-button>
                        <!--列过滤器-->
                        <el-dropdown trigger="click" :hide-on-click="false" v-if="!custom">
                            <el-button icon="el-icon-s-grid" size="mini"></el-button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-checkbox-group v-model="checkboxColumn">
                                        <el-dropdown-item v-for="item in columns">
                                            <el-checkbox :label="item.prop" v-if="item.label && !item.hide">{{item.label}}</el-checkbox>
                                        </el-dropdown-item>
                                    </el-checkbox-group>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </div>
                </el-col>
            </el-row>
        </div>
        <!--筛选-->
        <div :class="['filter',custom?'filterCustom':'']" v-if="filter" v-show="filterShow">
            <render :data="filter" ></render>
        </div>
        <div v-if="custom" >
            <a-list :data-source="tableData" :loading="loading" row-key="eadmin_id" v-bind="custom.attribute">
                <template #header v-if="custom.header"><render :data="custom.header" :slot-props="grid"></render></template>
                <template #footer v-if="custom.footer"><render :data="custom.footer" :slot-props="grid"></render></template>
                <template #renderItem="{ item }">
                    <a-list-item>
                        <render :data="item.custom" :slot-props="grid"></render>
                        <div class="customEadminAction" v-if="!hideSelection || item.EadminAction">
                            <el-checkbox v-model="item.checkbox" :checked="selectIds.indexOf(item.eadmin_id) > -1" v-if="!hideSelection && selectionType=='checkbox'" @change="e=>changeSelect(item.eadmin_id,e)" :label="item.eadmin_id"><span></span></el-checkbox>
                            <el-radio v-if="!hideSelection && selectionType=='radio'" v-model="selectRadio" @change="changeSelect"  :label="item.eadmin_id"><span></span></el-radio>
                            <render v-if="item.EadminAction" :data="item.EadminAction" :slot-props="grid"></render>
                        </div>
                    </a-list-item>
                </template>
            </a-list>
        </div>
        <div v-else>
            <div v-if="isMobile" style="background: #ffffff;overflow: auto" v-loading="loading">
                <el-row v-for="row in tableData" :key="row.eadmin_id" style="border-top: 1px solid rgb(240, 240, 240);">
                    <el-col :span="24" >
                        <div v-for="column in tableColumns" style="padding: 15px 10px;font-size: 14px;display: flex">
                            <div v-if="column.label" style="margin-right: 5px;color: #888888">{{column.label}}<span>:</span></div>
                            <render :data="row[column.prop]" :slot-props="grid"></render>
                        </div>
                    </el-col>
                </el-row>
            </div>
            <!--表格-->
            <a-table v-else :row-selection="rowSelection" @expand="expandChange" @change="tableChange" :columns="tableColumns" :data-source="tableData"  :expanded-row-keys="expandedRowKeys" :pagination="false" :loading="loading" v-bind="$attrs" row-key="eadmin_id" ref="dragTable">
                <template #title v-if="header">
                    <div class="header"><render :data="header" :slot-props="grid"></render></div>
                </template>
                <template v-for="column in tableColumns" v-slot:[column.slots.title]>
                    <render :data="column.header" :slot-props="grid"></render>
                </template>
                <template #expandedRowRender="{ record  }" v-if="expandedRow">
                    <render :data="record.EadminExpandRow" :slot-props="grid"></render>
                </template>
                <template #default="{ text , record , index }">
                    <render :data="text" :slot-props="grid"></render>
                </template>

                <template #sortDrag="{ text , record , index }">
                    <div style="display: flex;flex-direction: column">
                        <el-tooltip  effect="dark" content="置顶" placement="right-start"><i @click="sortTop(index,record)" class="el-icon-caret-top" style="cursor: pointer"></i></el-tooltip>
                        <el-tooltip effect="dark" content="拖动排序" placement="right-start"><i class="el-icon-rank sortHandel" style="font-weight:bold;cursor: grab"></i></el-tooltip>
                        <el-tooltip  effect="dark" content="置底" placement="right-start"><i @click="sortBottom(index,record)" class="el-icon-caret-bottom" style="cursor: pointer"></i></el-tooltip>
                    </div>
                </template>
                <template #sortInput="{ text , record , index }">
                    <el-input v-model="text.content.default[0]" @change="sortInput(record.eadmin_id,text.content.default[0])"></el-input>
                </template>
            </a-table>

        </div>

        <!--分页-->
        <el-pagination :class="['pagination',custom?'custom':'']"
                       @size-change="handleSizeChange"
                       @current-change="handleCurrentChange"
                       v-if="pagination"
                       v-bind="pagination"
                       :layout="pageLayout"
                       :total="total"
                       :page-size="size"
                       :current-page="page">
        </el-pagination>
        <!-- 导出excel进度-->
        <el-dialog title="导出进度" v-model="excel.excelVisible" width="30%" :before-close="excelVisibleClose" :close-on-click-modal="false">
            <div style="text-align: center">
                <el-progress type="circle" :percentage="excel.progress" :status="excel.status"></el-progress>
                <div v-if="excel.status == 'success'">已导出成功，请点击<el-link :href="excel.file" type="primary">下载</el-link></div>
                <div v-else-if="excel.status == 'exception'" style="color: red">导出失败</div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {defineComponent, ref, watch,reactive, inject,nextTick,computed,unref,onActivated,onMounted,onUnmounted} from "vue"
    import {useHttp} from '@/hooks'
   // import {tableDefer} from '@/hooks/use-defer'
    import request from '@/utils/axios'
    import {store} from '@/store'
    import {forEach, unique, deleteArr, buildURL, findTree,treeMap} from '@/utils'
    import {ElMessageBox,ElMessage} from 'element-plus'
    import Sortable from 'sortablejs'
    import {useRoute} from 'vue-router'
    export default defineComponent({
        name: "EadminGrid",
        props: {
            data: Array,
            columns: Array,
            pagination: [Object, Boolean],
            modelValue: Boolean,
            loadDataUrl: String,
            hideTools: Boolean,
            export: Boolean,
            static: Boolean,
            sortDrag: Boolean,
            sortInput: Boolean,
            tools:[Object,Array],
            hideSelection: Boolean,
            selectionType:{
                type:String,
                default:'checkbox'
            },
            selection:{
                type:Array,
                default:[]
            },
            hideDeleteButton: Boolean,
            hideTrashed: Boolean,
            hideDeleteSelection: Boolean,
            expandedRow: Boolean,
            filter: [Object, Boolean],
            header: [Object, Boolean],
            expandFilter: Boolean,
            addButton: [Object, Boolean],
            filterField:String,
            filterExceptField:{
                type:Array,
                default:[]
            },
            params:Object,
            addParams:Object,
            proxyData:Object,
            //自定义视图
            custom:[Object, Boolean],
        },
        inheritAttrs: false,
        emits: ['update:modelValue','update:selection'],
        setup(props, ctx) {
            const route = useRoute()
            const state = inject(store)
            const proxyData = props.proxyData
            const dragTable = ref('')
            const grid = {grid:ctx.attrs.eadmin_grid, gridParam:ctx.attrs.eadmin_grid_param}
            const {loading,http} = useHttp()
            const selectRadio = ref(false)
            if(props.selection.length > 0){
                selectRadio.value = props.selection[0]
            }
            const filterShow = ref(props.expandFilter)
            const quickSearch = ref('')
            const selectIds = ref(props.selection || [])
            const expandedRowKeys = ref([])
            const eadminActionWidth = ref(0)
            const trashed = ref(false)
            const excel  = reactive({
                excelVisible:false,
                excelTimer:null,
                progress:0,
                file:'',
                status:'',
            })
            const quickSearchOn = ctx.attrs.quickSearch
            const quickSearchText = ctx.attrs.quickSearchText || '请输入关键字'
            const columns = ref(props.columns)
            const tableData = ref([])
            if(props.static){
                tableData.value = props.data
            }
            // if(props.defer){
            //     tableDefer(tableData.value,props.data)
            // }else{
            //     tableData.value = props.data
            // }
            const total = ref(props.pagination.total || 0)
            const tools = ref(props.tools)
            const header = ref(props.header)
            let page = 1
            let size = props.pagination.pageSize
            let sortableParams = {}
            let filterInitData = null
            function globalRequestParams(){
                let requestParams = {
                    ajax_request_data: 'page',
                    page: page,
                    size: size,
                }
                const filterData = JSON.parse(JSON.stringify(proxyData[props.filterField] || ''))
                forEach(filterData,function (val,key) {
                    if(props.filterExceptField.indexOf(key) > -1){
                        delete filterData[key]
                    }
                })
                requestParams = Object.assign(requestParams, filterData,{quickSearch:quickSearch.value},route.query,props.params,props.addParams,sortableParams)
                if(trashed.value){
                    requestParams = Object.assign(requestParams ,{eadmin_deleted:true})
                }
                return requestParams
            }
            onMounted(()=>{
                if(!props.static){
                    loading.value = true
                }
            })
            onUnmounted((e)=>{
                if(excel.excelTimer != null){
                    clearInterval(excel.excelTimer)
                }
            })
            onActivated((e)=>{

                if(!props.static){
                    loading.value = true
                }
                if(excel.excelTimer != null){
                    clearInterval(excel.excelTimer)
                }
            })
            watch(() => props.modelValue, (value) => {
                if(value){
                    //quickSearch.value = ''
                    loading.value = value
                }
            })

            watch(loading, (value) => {
                if (value) {
                    loadData()
                }
            })
            //动态控制列显示隐藏
            const checkboxColumn = ref([])
            checkboxColumn.value = props.columns.map(item => {
                return item.prop
            })
            const tableColumns = computed(()=>{
                return columns.value.filter(item=>{
                    if(item.prop === 'EadminAction'){
                        item.width = eadminActionWidth.value
                        //有滚动条操作列fixed
                        if(dragTable.value && !item.fixed){
                            const el = dragTable.value.$el.querySelectorAll('.ant-table-body')[0]
                            const table = dragTable.value.$el.querySelectorAll('.ant-table-body > table')[0]
                            if(table.clientWidth > el.clientWidth){
                                item.fixed = 'right'
                            }
                        }

                    }
                    return checkboxColumn.value.indexOf(item.prop) >= 0 && !item.hide
                })

            })
            nextTick(()=>{
                if(proxyData[props.filterField]){
                    filterInitData = JSON.parse(JSON.stringify(proxyData[props.filterField]))
                }
                dragSort()
            })
            function actionAutoWidth(){
                let width = 0
                //操作列宽度自适应
                document.getElementsByClassName('EadminAction').forEach(item=>{
                    if(width < item.offsetWidth){
                        width = item.offsetWidth
                    }
                })
                width += 30
                eadminActionWidth.value = width
            }
            //拖拽排序
            function dragSort(){
                if(dragTable.value){
                    const el = dragTable.value.$el.querySelectorAll('.ant-table-body > table > tbody')[0]
                    Sortable.create(el, {
                        handle:'.sortHandel',
                        ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
                        onEnd: evt => {
                            var newIndex = evt.newIndex;
                            var oldIndex = evt.oldIndex;
                            var oldItem = tableData.value[oldIndex]
                            var startPage = (page-1) * size
                            const targetRow = tableData.value.splice(evt.oldIndex, 1)[0]
                            tableData.value.splice(evt.newIndex, 0, targetRow)
                            if(evt.newIndex != evt.oldIndex){
                                sortRequest(oldItem.eadmin_id,startPage +newIndex).catch(()=>{
                                    const targetRow = tableData.value.splice(evt.newIndex, 1)[0]
                                    tableData.value.splice(evt.oldIndex, 0, targetRow)
                                })
                            }
                        }
                    })
                }
            }
            function sortRequest(id,sort,action='eadmin_sort') {
                return new Promise((resolve, reject) =>{
                    request({
                        url: 'eadmin/batch.rest',
                        params:Object.assign(props.params,route.query),
                        method: 'put',
                        data:{
                            action:action,
                            id:id,
                            sort: sort,
                            eadmin_ids:[id]
                        }
                    }).then(res=>{
                        resolve(res)
                    }).catch(res=>{
                        reject(res)
                    })
                })
            }
            //排序置顶
            function sortTop(index,data){
                sortRequest(data.eadmin_id,0).then(res=>{
                    if(page === 1){
                        const targetRow = tableData.value.splice(index, 1)[0]
                        tableData.value.unshift(targetRow)
                    }else{
                        tableData.value.splice(index,1)

                    }
                })

            }
            //排序置底
            function sortBottom(index,data){
                sortRequest(data.eadmin_id,total.value-1).then(res=>{
                    if(page === 1){
                        const targetRow = tableData.value.splice(index, 1)[0]
                        tableData.value.push(targetRow)
                    }else{
                        tableData.value.splice(index,1)
                    }

                })
            }
            //输入框排序
            function sortInput(id,sort){
                sortRequest(id,sort,'')
            }
            //分页大小改变
            function handleSizeChange(val) {
                page = 1
                size = val
                loading.value = true
            }
            //分页改变
            function handleCurrentChange(val) {
                page = val
                loading.value = true
            }

            const rowSelection = computed(()=>{
                if(props.hideSelection){
                    return null
                }else{
                    return {
                        selectedRowKeys:unref(selectIds),
                        type:props.selectionType,
                        //当用户手动勾选数据行的 Checkbox 时触发的事件
                        onSelect: (record, selected, selectedRows, nativeEvent) => {
                            const ids = selectedRows.map(item=>{
                                return item.eadmin_id
                            })
                            if(selected){
                                if(props.selectionType === 'checkbox'){
                                    selectIds.value = unique(selectIds.value.concat(ids))
                                }else{
                                    selectIds.value = ids
                                }
                            }else{
                                deleteArr(selectIds.value,record.eadmin_id)
                            }
                            ctx.emit('update:selection',selectIds.value)
                        },
                        onSelectAll:(selected, selectedRows, changeRows)=>{
                            const ids = selectedRows.map(item=>{
                                return item.eadmin_id
                            })
                            if(selected){
                                selectIds.value = unique(selectIds.value.concat(ids))
                            }else{
                                changeRows.map(item=>{
                                    deleteArr(selectIds.value,item.eadmin_id)
                                })
                            }
                            ctx.emit('update:selection',selectIds.value)
                        },
                    }
                }
            })
            //快捷搜索
            function handleFilter() {
                page = 1
                loading.value = true
                //重置筛选条件
                if(filterInitData){
                    proxyData[props.filterField] = Object.assign(proxyData[props.filterField],JSON.parse(JSON.stringify(filterInitData)))
                }
            }
            //请求获取数据
            function loadData() {

                http({
                    url: props.loadDataUrl,
                    params: globalRequestParams()
                }).then(res => {
                    if(ctx.attrs.defaultExpandAllRows){
                        expandedRowKeys.value = treeMap(res.data,'eadmin_id')
                    }
                    tableData.value = res.data
                    total.value = res.total
                    header.value = res.header
                    tools.value = res.tools
                    nextTick(()=>{
                        actionAutoWidth()
                    })
                }).finally(() => {
                    ctx.emit('update:modelValue', false)
                })
            }
            //回收站
            function trashedHandel() {
                trashed.value = !trashed.value
                loading.value = true
            }
            //删除全部
            function deleteAll(){
                deleteRequest('此操作将删除清空所有数据, 是否继续?',true)

            }
            //删除选中
            function deleteSelect() {
                deleteRequest('此操作将删除选中数据, 是否继续?',selectIds.value)
            }
            //恢复选中
            function recoverySelect() {
                ElMessageBox.confirm('此操作将恢复选中数据','是否继续?',{type: 'warning'}).then(()=>{
                    request({
                        url: props.loadDataUrl.replace('.rest','/batch.rest'),
                        data: Object.assign({eadmin_ids: selectIds.value},props.params,{delete_time:null}),
                        method:'put',
                    }).then(res=>{
                        loadData()
                    })
                })
            }
            //删除请求
            function deleteRequest(message,ids) {

                ElMessageBox.confirm(message,'是否继续?',{type: 'warning'}).then(()=>{
                    let params = {}
                    if(trashed.value){
                        params.trueDelete = true
                    }
                    request({
                        url: props.loadDataUrl.replace('.rest','/delete.rest'),
                        data: Object.assign({eadmin_ids: ids},route.query,props.params,params),
                        method:'delete',
                    }).then(res=>{
                        selectIds.value = []
                        loadData()
                    })
                })
            }
            //表格分页、排序、筛选变化时触发事件
            function tableChange(pagination, filters, sorter) {
                if(sorter.order === 'descend'){
                    sortableParams = {
                        eadmin_sort_field:sorter.field,
                        eadmin_sort_by:'desc'
                    }

                }else if(sorter.order === 'ascend'){
                    sortableParams = {
                        eadmin_sort_field:sorter.field,
                        eadmin_sort_by:'asc'
                    }
                }else{
                    sortableParams = {}
                }
                loading.value = true
            }
            //导出进度关闭定时器
            function excelVisibleClose(done){
                if(excel.excelTimer != null){
                    clearInterval(excel.excelTimer)
                }
                done()
            }
            //导出
            function exportData(type){
                if(tableData.value.length == 0){
                    ElMessage.warning('暂无数据')
                    return false
                }
                let requestParams = {
                        eadmin_export:true,
                        export_type:type,
                        Authorization:ctx.attrs.Authorization,
                        eadmin_ids:selectIds.value
                }
                requestParams = Object.assign(globalRequestParams(),requestParams)
                if(type == 'all'){
                    excel.progress = 0
                    excel.file = ''
                    request({
                        url:'/eadmin.rest',
                        params: Object.assign(requestParams,{eadmin_queue:true})
                    }).then(res=>{
                        excel.status = ''
                        excel.excelVisible = true
                        excel.excelTimer = setInterval(()=>{
                            request({
                                url: 'queue/progress',
                                params: {
                                    id: res.data
                                }
                            }).then(result=>{
                                excel.progress = result.data.progress
                                if(result.data.status == 4){
                                    excel.status = 'exception'
                                    clearInterval(excel.excelTimer)
                                }
                                if(result.data.status == 3){
                                    clearInterval(excel.excelTimer)
                                    excel.status = 'success'
                                    excel.file = result.data.history.slice(-2)[0].message
                                }
                            })
                        },500)
                    })
                }else{
                    location.href = buildURL('/eadmin.rest',requestParams)
                }
            }
            const pageLayout = computed(()=>{
                if(state.device === 'mobile'){
                    return 'total, prev, pager, next, jumper'
                }else{
                    return 'total, sizes, prev, pager, next, jumper'
                }
            })
            const isMobile = computed(()=>{
                if(state.device === 'mobile'){
                    return true
                }else{
                    return false
                }
            })
            function expandChange(bool,record) {
                if(bool){
                    expandedRowKeys.value.push(record.eadmin_id)
                }else{
                    deleteArr(expandedRowKeys.value,record.eadmin_id)
                }
            }
            function visibleFilter() {
                filterShow.value = !filterShow.value
            }
            //自定义视图单选框切换
            function changeSelect(value,bool) {
                if(props.selectionType=='checkbox'){
                    if(bool){
                        selectIds.value.push(value)
                        selectIds.value = unique(selectIds.value)
                    }else{
                        deleteArr(selectIds.value,value)
                    }
                }else{
                    selectIds.value = [value]
                }
                ctx.emit('update:selection',selectIds.value)
            }
            return {
                isMobile,
                grid,
                pageLayout,
                eadminActionWidth,
                quickSearchOn,
                quickSearchText,
                page,
                size,
                total,
                handleFilter,
                tableColumns,
                checkboxColumn,
                handleSizeChange,
                expandChange,
                handleCurrentChange,
                loading,
                tableData,
                quickSearch,
                rowSelection,
                visibleFilter,
                filterShow,
                deleteSelect,
                recoverySelect,
                deleteAll,
                selectIds,
                dragTable,
                sortTop,
                sortBottom,
                sortInput,
                tableChange,
                trashedHandel,
                trashed,
                expandedRowKeys,
                exportData,
                header,
                tools,
                excel,
                selectRadio,
                changeSelect,
                excelVisibleClose,
            }
        }
    })
</script>

<style scoped>
    .searchButton{
        background:#409eff!important;
        color: #FFFFFF!important;
        border-radius:0!important;
        border-top-right-radius:4px!important;
        border-bottom-right-radius:4px!important;
    }
    .custom{
        background: none !important;
        padding-left: 0 !important;
    }
    .header{
        margin-left: 10px;

    }
    .sortable-selecte{
        background-color: #EBEEF5 !important;
    }
    .sortable-ghost{
        opacity: .8;
        color: #fff!important;
        background: #2d8cf0!important;
    }
    .pagination {
        background: #fff;
        padding: 10px 16px;
        border-radius: 5px;
    }
    .tools {
        background: #fff;
        position: relative;
        border-radius: 4px;
        padding-left: 10px;
        padding-bottom: 10px;
    }
    .filter .el-main{
        padding-bottom: 0;
    }
    .filter{
        border-top: 1px solid #ededed;
        background: #fff;
    }
    .filterCustom{
        margin-bottom: 10px;
    }
    .customEadminAction{
        margin-top: 10px;
        display: flex;align-items: center;
        justify-content: space-between;
    }
    .customEadminAction .el-radio{
        margin-right: 0;
    }
</style>
