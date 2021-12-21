<template>
    <div class="flex">
        <a-list bordered :data-source="tables" style="height: 565px;overflow-y: auto">
            <template #renderItem="{ item }">
                <div :class="['listItem',selectTableName==item.name?'active':'']">
                    <a-list-item @click="selectTable(item.name)" :class="['listItem',selectTableName==item.name?'active':'']" >
                        {{item.name}} - {{item.desc}}
                    </a-list-item>

                </div>
            </template>
        </a-list>
        <el-popover
                placement="right"
                :width="200"
                v-model:visible="tableVisible"
                trigger="manual"
        >
            <el-form :model="formTable">
                <el-form-item prop="name" :rules="[{ required: true, message: '请输入表名', trigger: 'blur' },{pattern: /^[a-zA-Z][0-9a-zA-Z_]*$/g, message: '只允许f字母开头，字母数字下划线', trigger: 'blur'}]">
                    <el-input v-model="formTable.name" placeholder="表名"></el-input>
                </el-form-item>
                <el-form-item prop="name" :rules="{ required: true, message: '请输入表注释', trigger: 'blur' }">
                    <el-input v-model="formTable.desc" placeholder="表注释"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" size="small" @click="addTable">确定</el-button>
                    <el-button type="primary" size="small" @click="tableVisible = false">取消</el-button>
                </el-form-item>
            </el-form>
            <template #reference>
                <span> </span>
            </template>
        </el-popover>
        <a-table size="small" :dataSource="dataSource" :scroll="{y:460}" :pagination="false" :custom-row="customRow" row-key="name" :locale="{emptyText:'暂无字段'}" style="flex: 1">
            <template #footer>
                <el-button size="small" @click="tableVisible = !tableVisible">新建表</el-button>
                <el-button size="small" @click="tableVisible = !tableVisible" v-show="selectTableName">修改表</el-button>
                <el-divider direction="vertical"></el-divider>
                <el-button @click="addField" size="small" v-show="selectTableName">添加字段</el-button>
                <el-button type="primary" @click="saveField" size="small" v-show="selectTableName">保存</el-button>
            </template>
            <a-table-column title="字段名" data-index="name" :width="150">
                <template #default="{ record }">
                    <el-input v-model="record.name" placeholder="字段名" size="small"></el-input>
                </template>
            </a-table-column>
            <a-table-column title="默认值" data-index="value" :width="150">
                <template #default="{ record }">
                    <el-input v-model="record.value" placeholder="默认值" size="small"></el-input>
                </template>
            </a-table-column>
            <a-table-column title="类型" data-index="type" :width="110">
                <template #default="{ record }">
                    <el-select v-model="record.type" size="small">
                        <el-option v-for="item in typeOptions" :label="item" :value="item"></el-option>
                    </el-select>
                </template>
            </a-table-column>
            <a-table-column title="长度" data-index="length" :width="100">
                <template #default="{ record }">
                    <el-input v-model="record.length" placeholder="长度" type="number" size="small"></el-input>
                </template>
            </a-table-column>
            <a-table-column title="不为空" data-index="nullable" :width="60" align="center">
                <template #default="{ record }">
                    <el-checkbox v-model="record.nullable"  size="small"></el-checkbox>
                </template>
            </a-table-column>

            <a-table-column title="索引" data-index="index" :width="100">
                <template #default="{ record }">
                    <el-select v-model="record.index" clearable placeholder="索引" style="width:90px" size="small">
                        <el-option v-for="item in indexOptions" :label="item" :value="item"></el-option>
                    </el-select>
                </template>
            </a-table-column>
            <a-table-column title="注释" data-index="label" :width="150">
                <template #default="{ record }">
                    <el-input v-model="record.label" placeholder="字段注释" size="small"></el-input>
                </template>
            </a-table-column>
            <a-table-column>
                <template #default="{ record ,index}">
                    <el-popconfirm
                    title="确认删除字段？"
                    @confirm="removeField(index)"
                    >
                    <template #reference>
                        <div style="margin-left: 30px" v-show="hoverIndex == index">
                        <i class="el-icon-error" style="color: red;cursor: pointer"></i>
                        </div>
                    </template>
                    </el-popconfirm>
                </template>
            </a-table-column>
        </a-table>
    </div>
</template>

<script>
    import {defineComponent, toRefs, reactive, toRaw} from "vue";

    export default defineComponent({
        name: "fieldForm",
        setup() {
            const state = reactive({
                tableVisible:false,
                tables: [],
                dataSource: [],
                selectTableName:'',
                formTable: {
                    name:'',
                    desc:'',
                },
                hoverIndex: '',
                rules: {
                    name: [
                        {required: true, message: '请填写字段名称', trigger: 'blur'},
                        {pattern: /^[a-zA-Z][0-9a-zA-Z_]*$/g, message: '只允许f字母开头，字母数字下划线', trigger: 'blur'},
                    ],
                    label: [
                        {required: true, message: '请填写字段标题', trigger: 'blur'}
                    ]
                }
            })
            //字段类型
            const typeOptions = ['varchar', 'int', 'decimal', 'text', 'tinyint', 'datetime', 'date']
            //索引
            const indexOptions = ['index', 'unique']
            //添加字段
            function addField() {
                state.dataSource.push({
                    name: '',
                    label: '',
                    type: 'varchar',
                    length: '',
                    value: '',
                    index: '',
                    nullable: false,
                })
            }
            //移除字段
            function removeField(index) {
                state.dataSource.splice(index, 1)
            }
            //选中表
            function selectTable(name) {
                state.selectTableName = name
                if(state.dataSource.length == 0){
                    addField()
                }
            }
            //添加表
            function addTable() {
                state.tables.push(JSON.parse(JSON.stringify(state.formTable)))
                selectTable(state.formTable.name)
                state.formTable.name = ''
                state.formTable.desc = ''
                state.tableVisible = false
            }
            function saveField() {
                
            }
            function handleCommand(command) {
                if(command == 'add'){
                    state.tableVisible = !state.tableVisible
                }else if(command == 'edit'){
                    state.tableVisible = !state.tableVisible
                }
            }
            function customRow(record,index) {
                return {
                    onMouseenter:e=>{
                        state.hoverIndex = index
                    },
                    onMouseleave:e=>{
                        state.hoverIndex = -1
                    }
                }
            }
            return {
                ...toRefs(state),
                customRow,
                selectTable,
                indexOptions,
                typeOptions,
                removeField,
                addField,
                addTable,
                saveField,
                handleCommand
            }
        }
    })
</script>

<style scoped>
    .flex {
        display: flex;
    }
    .listItem{
        cursor: pointer;
    }
    .listItem:hover{
        background: #409eff;
        color:#FFFFFF;
    }
    .active{
        background: #409eff;
        color:#FFFFFF;
    }
    .form .el-form-item--small.el-form-item {
        margin-bottom: 3px;
    }
</style>