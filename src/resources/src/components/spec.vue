<template>
    <EadminSelect :options="group" v-model="specGroup" style="width: 100%;margin-bottom: 5px" clearable @change="selectHandel" v-if="!disabled">
        <el-option v-for="item in group" :key="item.id" :value="item.id" :label="item.name"></el-option>
    </EadminSelect>
    <el-table :data="specs" border size="mini" v-if="specGroup" v-show="!disabled" style="margin-bottom: 5px" :row-class-name="tableRowClassName" @cell-mouse-leave="cellMouseLeave" @cell-mouse-enter="cellMouseEnter">
        <el-table-column
                prop="name"
                label="可选规格">
        </el-table-column>
        <el-table-column
                label="规格内容">
            <template #default="scope">
                <div class="specValue">
                    <el-checkbox-group v-model="scope.row.selected">
                        <el-checkbox v-for="item in scope.row.spec" :label="item"></el-checkbox>
                    </el-checkbox-group>
                    <div class="action" v-if="hoverIndex === scope.$index">
                        <el-tooltip content="上移">
                            <i class="el-icon-caret-top" @click="handleUp(selectSpec,scope.$index)"></i>
                        </el-tooltip>
                        <el-tooltip content="下移">
                            <i class="el-icon-caret-bottom" @click="handleDown(selectSpec,scope.$index)"></i>
                        </el-tooltip>
                    </div>
                </div>
            </template>
        </el-table-column>
    </el-table>
    <a-table row-key="group" :data-source="tableData" v-if="tableData.length > 0" size="small" bordered :pagination="false" :scroll="{x:'max-content'}">
        <a-table-column title="规格" data-index="eadminSpecGroup">
            <template #default="{ record }">
                <el-breadcrumb separator="|">
                    <el-breadcrumb-item v-for="item in record.group">
                        <el-tag size="mini" type="info" effect="dark" class="tag">{{item.name}}</el-tag>
                        <el-tag size="mini" effect="dark">{{item.value}}</el-tag>
                    </el-breadcrumb-item>
                </el-breadcrumb>
            </template>
        </a-table-column>
        <template v-for="(column,index) in columns">
            <a-table-column  v-if="!column.hide"  :data-index="column.prop">
                <template #title>
                    {{column.title}} <i class="el-icon-edit-outline" style="cursor: pointer" v-if="column.component.content.default[0].name != 'EadminDisplay' && !column.component.content.default[0].attribute.disabled" @click="dialogs[index].dialog = true"></i>
                    <el-dialog
                            title="批量修改"
                            v-model="dialogs[index].dialog"
                            width="30%">
                        <render :data="column.component.content.default[0]" v-model="dialogs[index].value"></render>
                        <template #footer>
                            <el-button size="small" @click="dialogs[index].dialog = false">取 消</el-button>
                            <el-button size="small" type="primary" @click="()=>{batch(index,column.prop,dialogs[index].value)}">保 存</el-button>
                        </template>
                    </el-dialog>
                </template>
                <template #default="{ record , index}">
                    <div style="margin: 10px 0">
                        <render :slot-props="{ row:record ,$index:index ,propField:field,validator:$attrs.validator}" :data="column.component"></render>
                    </div>
                </template>
            </a-table-column>
        </template>

    </a-table>
</template>

<script>
    import {defineComponent, reactive, toRefs, computed, watch,watchEffect,toRaw} from "vue";
    import {findTree} from "@/utils";

    export default defineComponent({
        name: "EadminSpec",
        inheritAttrs: false,
        props: {
            field:String,
            data: Array,
            disabled: Boolean,
            specId: {
                type:[String, Number],
                default:'',
            },
            specs: [Array, String],
            columns: Array,
            modelValue: [Object, Array, String],
        },
        emits: ['update:modelValue', 'update:specs'],
        setup(props, ctx) {
            const state = reactive({
                group:[],
                specGroup: props.specId == '' ? '':'select'+props.specId,
                selectSpec: [],
                hoverIndex:-1,
                tableData:[],
                dialogs:[],
                value:'',
            })
            props.columns.forEach(item=>{
                state.dialogs.push({
                    dialog:false,
                    value:'',
                })
            })
            let selectValue = props.modelValue
            let propsSpecs = props.specs
            if(propsSpecs){
                props.data.unshift({
                    id:'select'+props.specId,
                    name:'当前规格',
                    specs:toRaw(propsSpecs)
                })

            }
            state.group = props.data
            //规格分组
            const specs = computed(() => {
                const spec = findTree(state.group, state.specGroup, 'id')
                if (spec) {
                    state.selectSpec = spec.specs.map((item, index) => {
                        const selectSpecs = findTree(propsSpecs, item.name, 'name')
                        item.selected = item.spec.filter(function (num) {
                            if(selectSpecs){
                                return selectSpecs.spec.indexOf(num) !== -1;
                            }
                            return false
                        })

                        return item
                    })
                    return state.selectSpec
                } else {
                    return []
                }
            })
            let checkboxSpec = []

            watchEffect(() => {
                let data = []
                let selectedArr = []
                checkboxSpec = []
                state.selectSpec.forEach(item => {
                    if(item.selected.length > 0){
                        checkboxSpec.push({name: item.name, spec: item.selected})
                    }
                    let arr = []
                    item.selected.forEach(selected => {
                        arr.push({
                            group: [{name: item.name, value: selected}],
                            spec: selected
                        })
                    })
                    selectedArr.push(arr)
                })
                if (selectedArr.length > 0) {
                    data = specParse(selectedArr, data)
                    if (data.length > 0) {
                        data = data.shift()
                    }
                }
                data =  data.map(item => {
                    const spec = findTree(selectValue, item.spec, 'spec')
                    if (spec) {
                        item.id = spec.id
                    }
                    props.columns.forEach(column => {
                        if (spec) {
                            item[column.dataIndex] = spec[column.dataIndex]
                        } else {
                            item[column.dataIndex] = ''
                        }
                    })
                    return item
                })

                state.tableData = data
            })

            watch(()=>state.tableData , value => {
                selectValue = value
                try {
                    ctx.emit('update:specs', checkboxSpec)
                    ctx.emit('update:modelValue', value)
                }catch (e) {

                }
            },{deep:true})
            function selectHandel(val) {
                state.selectSpec = []
            }
            function specParse(arr1, arr3) {
                if (arr1[0] && arr1[0].length === 0) {
                    arr1.shift()
                    if (arr1.length > 1) {
                        return specParse(arr1, arr3)
                    } else if (arr1.length == 1) {
                        return arr1
                    }
                    return arr3
                }
                arr1[0].forEach(item1 => {
                    if (arr1[1] && arr1[1].length > 0) {
                        arr1[1].forEach(item2 => {
                            arr3.push({
                                group: item1.group.concat(item2.group),
                                spec: `${item1.spec}-${item2.spec}`
                            })
                        });
                    } else {
                        arr3.push({
                            group: item1.group,
                            spec: item1.spec,
                        })
                    }
                });
                arr1 = arr1.slice(2)
                arr1.unshift(arr3)
                arr3 = []
                if (arr1.length > 1) {
                    return specParse(arr1, arr3)
                } else {
                    return arr1
                }
            }
            // 上移
            function handleUp (arr,index) {
                if(index > 0){
                    const len = arr[index - 1]
                    arr[index - 1] = arr[index]
                    arr[index] = len
                }

            }
            // 下移
            function handleDown (arr,index) {
                if(arr.length-1 > index){
                    const len = arr[index + 1]
                    arr[index + 1] = arr[index]
                    arr[index] = len
                }
            }
            function tableRowClassName({row, column, rowIndex, columnIndex}){
                row.eadminIndex = rowIndex;
            }
            //当单元格 hover 进入时会触发该事件
            function cellMouseEnter(row, column, cell, event){
                state.hoverIndex = row.eadminIndex
            }
            //当单元格 hover 退出时会触发该事件
            function cellMouseLeave(row, column, cell, event){
                state.hoverIndex = -1
            }
            function batch(index,field,value) {
                if(value){
                    state.dialogs[index].dialog = false
                }
                state.tableData = state.tableData.map(item=>{
                    item[field] = value
                    return item
                })
            }
            return {
                batch,
                tableRowClassName,
                cellMouseEnter,
                cellMouseLeave,
                handleUp,
                handleDown,
                selectHandel,
                specs,
                ...toRefs(state)
            }
        }
    })
</script>

<style scoped>
    .tag {
        margin-right: 5px;
    }

    .specValue {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .specValue i{
        font-size: 14px;
        margin-right: 5px;
        cursor: pointer;
    }
    .specValue .action{
        display: flex;
        flex-direction: column;
    }
</style>
