<template>
    <div style="display: flex;">
        <div style="flex: 1">
            <el-popover
                    placement="right"
                    :width="200"
                    v-model:visible="visible"
                    trigger="manual"
            >
                <el-form :model="form">
                    <el-form-item prop="name"
                                  :rules="[{ required: true, message: '请输入控制器名称', trigger: 'blur' },{pattern: /^[a-zA-Z]*$/g, message: '只允许字母', trigger: 'blur'}]">
                        <el-input v-model="form.name" placeholder="控制器名称"></el-input>
                    </el-form-item>
                    <el-form-item prop="desc" :rules="{ required: true, message: '请输入控制器标题', trigger: 'blur' }">
                        <el-input v-model="form.desc" placeholder="控制器标题"></el-input>
                    </el-form-item>
                    <el-form-item prop="tabel" :rules="{ required: true, message: '请选择表', trigger: 'blur' }">
                        <el-select v-model="form.table_id">
                            <el-option v-for="item in tables" :value="item.id" :label="item.name + ' - ' +item.desc"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" size="small" @click="saveController" v-loading="loading">确定</el-button>
                        <el-button type="primary" size="small" @click="visible = false">取消</el-button>
                    </el-form-item>
                </el-form>
                <template #reference>
                    <span> </span>
                </template>
            </el-popover>
            <a-dropdown :trigger="['contextmenu']">
                <div style="height: 100%">
                    <div style="text-align: center;height: 20px">控制器</div>
                    <a-list bordered :data-source="controllers" style="height: calc(100vh - 20px)"
                            :locale="{emptyText:'右键添加控制器'}">
                        <template #renderItem="{ item ,index}">
                            <div :class="['listItem',selectIndex==index?'active':'']">
                                <a-list-item @click="selectController(index)" @contextmenu="selectController(index)">
                                    <a-list-item-meta>
                                        <template #title>
                                            <span :class="['listItem',selectIndex==index?'active':'']">{{item.name}}</span>
                                        </template>
                                        <template #description>
                                            <span :class="['listItem',selectIndex==index?'active':'']">{{item.desc}}</span>
                                        </template>
                                    </a-list-item-meta>
                                </a-list-item>
                            </div>
                        </template>
                    </a-list>
                </div>
                <template #overlay>
                    <a-menu>
                        <a-menu-item @click="addController"><i class="el-icon-plus"></i>添加控制器</a-menu-item>
                        <a-menu-item @click="editController"><i class="el-icon-edit"></i>修改控制器</a-menu-item>
                        <a-menu-item>
                            <el-popconfirm
                                    title="确认删除？"
                                    @confirm="delController"
                            >
                                <template #reference>
                                    <span><i class="el-icon-close"></i>删除控制器</span>
                                </template>
                            </el-popconfirm>
                        </a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>
        </div>
        <div style="flex: 1">
            <el-popover
                    placement="right"
                    :width="200"
                    v-model:visible="visibleMethod"
                    trigger="manual"
            >
                <el-form :model="formMethod">
                    <el-form-item prop="name"
                                  :rules="[{ required: true, message: '请输入方法名称', trigger: 'blur' },{pattern: /^[a-zA-Z]*$/g, message: '只允许字母', trigger: 'blur'}]">
                        <el-input v-model="formMethod.name" placeholder="方法名称"></el-input>
                    </el-form-item>
                    <el-form-item prop="desc" :rules="{ required: true, message: '请输入方法标题', trigger: 'blur' }">
                        <el-input v-model="formMethod.desc" placeholder="方法标题"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="formMethod.type">
                            <el-option v-for="item in methodOptoins" :label="item.label" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-checkbox v-model="formMethod.is_login" size="small">登陆验证</el-checkbox>
                        <el-checkbox v-model="formMethod.is_auth" size="small">权限验证</el-checkbox>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" size="small" @click="saveMethod" v-loading="loading">确定</el-button>
                        <el-button type="primary" size="small" @click="visibleMethod = false">取消</el-button>
                    </el-form-item>
                </el-form>
                <template #reference>
                    <span> </span>
                </template>
            </el-popover>
            <a-dropdown :trigger="['contextmenu']">
                <div style="height: 100%">
                    <div style="text-align: center;height: 20px">方法</div>
                    <a-list bordered :data-source="methods" style="height: calc(100vh - 20px)"
                            :locale="{emptyText:'右键添加方法'}">
                        <template #renderItem="{ item ,index}">
                            <div :class="['listItem',selectMethodIndex==index?'active':'']">
                                <a-list-item @click="selectMethod(index)" @contextmenu="selectMethod(index)">
                                    <a-list-item-meta>
                                        <template #title>
                                            <span :class="['listItem',selectMethodIndex==index?'active':'']">{{item.name}}</span>
                                        </template>
                                        <template #description>
                                            <span :class="['listItem',selectMethodIndex==index?'active':'']">{{item.desc}}</span>
                                        </template>
                                    </a-list-item-meta>
                                </a-list-item>
                            </div>
                        </template>
                    </a-list>
                </div>
                <template #overlay>
                    <a-menu>
                        <a-menu-item @click="addMethod"><i class="el-icon-plus"></i>添加方法</a-menu-item>
                        <a-menu-item @click="editMethod"><i class="el-icon-edit"></i>修改方法</a-menu-item>
                        <a-menu-item>
                            <el-popconfirm
                                    title="确认删除？"
                                    @confirm="delMethod"
                            >
                                <template #reference>
                                    <span><i class="el-icon-close"></i>删除方法</span>
                                </template>
                            </el-popconfirm>
                        </a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>
        </div>
        <el-dialog
                v-model="previewCodeVisible"
                width="60%"
                custom-class="previewCode"
        >
            <template #title>
               <div style="display: flex;justify-content: space-between;align-items: center">
                   <div>预览代码</div>
                   <render :data="codeButton"></render>
               </div>
            </template>
            <render :data="code"></render>
        </el-dialog>
    </div>

</template>

<script>
    import {defineComponent, reactive, toRefs,watch} from "vue"
    import {useHttp} from '@/hooks'
    export default defineComponent({
        name: "contollerView",
        props:{
            field:[Array,Object],
            methodConfig:[Array,Object],
            save:Boolean,
            codeVisible:Boolean,
            methodType:Number,
        },
        emits: ['update:field','update:methodConfig','update:methodType'],
        setup(props,ctx) {
            const {http,loading} = useHttp()
            const state = reactive({
                codeButton:null,
                previewCodeVisible:false,
                code:null,
                controllers: [],
                methods: [],
                selectIndex: -1,
                selectMethodIndex: -1,
                visible: false,
                visibleMethod: false,
                tables:[],
                methodOptoins:[
                    {
                        label:'列表(grid)',
                        value:1,
                    },
                    {
                        label:'表单(form)',
                        value:2,
                    },
                    {
                        label:'详情(detail)',
                        value:3,
                    }
                ],
                form: {
                    name: '',
                    table_id: '',
                    desc: '',
                },
                formMethod: {
                    type:'',
                    name: '',
                    desc: '',
                    config:'',
                    is_login:true,
                    is_auth:true,
                },
                tableField:[]
            })
            watch(()=>props.save,value=>{
                if(value){
                    state.formMethod.name = state.methods[state.selectMethodIndex].name
                    state.formMethod.desc = state.methods[state.selectMethodIndex].desc
                    state.formMethod.id = state.methods[state.selectMethodIndex].id
                    state.formMethod.type = state.methods[state.selectMethodIndex].type
                    state.formMethod.config = props.methodConfig
                    saveMethod()
                    ctx.emit('update:save',false)
                }
            })
            watch(()=>props.codeVisible,value=>{
                if(value){
                    state.previewCodeVisible = true
                    previewCode()
                    ctx.emit('update:codeVisible',false)
                }
            })
            watch(()=>state.tableField,value=>{
                ctx.emit('update:field',value)
            })
            http('rockysView/table').then(res=>{
                state.tables = res.data
            })
            http('rockysView/controller').then(res=>{
                state.controllers = res.data
            })
            function selectController(index) {
                state.selectIndex = index
                state.selectMethodIndex = -1
              //  ctx.emit('update:methodConfig',[])
                http({
                    url:'rockysView/method',
                    params:{
                        controller_id :state.controllers[state.selectIndex].id
                    }
                }).then(res=>{
                    state.methods = res.data
                })
            }

            function selectMethod(index) {
                state.selectMethodIndex = index
                const type = state.methods[state.selectMethodIndex].type
                ctx.emit('update:methodType',type)
                if(state.methods[state.selectMethodIndex].config){
                    const config = props.methodConfig
                    switch (type) {
                        case 1:
                            config.grid = state.methods[state.selectMethodIndex].config.grid
                            break
                        case 2:
                            config.form = state.methods[state.selectMethodIndex].config.form
                            break
                        case 3:
                            config.detail = state.methods[state.selectMethodIndex].config.detail
                            break
                    }
                    ctx.emit('update:methodConfig',config)
                }else{
                    http({
                        url:'rockysView/field',
                        params:{
                            table_id :state.controllers[state.selectIndex].table_id
                        }
                    }).then(res=>{

                        state.tableField = res.data.filter(item=>{
                            switch (type) {
                                case 1:
                                    return item.grid == 1
                                    break
                                case 2:
                                    return item.form == 1
                                    break
                                case 3:
                                    return item.detail == 1
                                    break
                            }
                        })
                    })
                }
            }
            function addController() {
                state.visible = true
                delete state.form.id
            }
            function addMethod() {
                state.visibleMethod = true
                delete state.formMethod.id
            }
            function editController() {
                state.visible = true
                state.form.name = state.controllers[state.selectIndex].name
                state.form.table_id = state.controllers[state.selectIndex].table_id
                state.form.desc = state.controllers[state.selectIndex].desc
                state.form.id = state.controllers[state.selectIndex].id
            }
            function editMethod() {
                state.visibleMethod = true
                state.formMethod.name = state.methods[state.selectMethodIndex].name
                state.formMethod.desc = state.methods[state.selectMethodIndex].desc
                state.formMethod.id = state.methods[state.selectMethodIndex].id
                state.formMethod.type = state.methods[state.selectMethodIndex].type
            }
            function saveMethod() {
                state.formMethod.controller_id = state.controllers[state.selectIndex].id
                http({
                    url:'rockysView/method',
                    method:'post',
                    data:state.formMethod
                }).then(res=>{
                    if(res.data.result == 1){
                        state.formMethod.id = res.data.id
                        state.methods.push(JSON.parse(JSON.stringify(state.formMethod)))
                        selectMethod(state.methods.length-1)
                    }else{
                        state.methods[state.selectMethodIndex].name = state.formMethod.name
                        state.methods[state.selectMethodIndex].desc = state.formMethod.desc
                        state.methods[state.selectMethodIndex].type = state.formMethod.type
                        state.methods[state.selectMethodIndex].config = state.formMethod.config
                    }
                    state.formMethod.name = ''
                    state.formMethod.desc = ''
                    state.formMethod.type = ''
                    state.visibleMethod = false
                })
            }
            function saveController() {
                http({
                    url:'rockysView/controller',
                    method:'post',
                    data:state.form
                }).then(res=>{
                    if(res.data.result == 1){
                        state.form.id = res.data.id
                        state.controllers.push(JSON.parse(JSON.stringify(state.form)))
                        selectController(state.controllers.length-1)
                    }else{
                        state.controllers[state.selectIndex].name = state.form.name
                        state.controllers[state.selectIndex].desc = state.form.desc
                        state.controllers[state.selectIndex].table_id = state.form.table_id
                    }
                    state.form.name = ''
                    state.form.desc = ''
                    state.form.table_id = 0
                    state.visible = false
                })
            }
            function delController() {
                http({
                    url:'rockysView/controller',
                    method:'delete',
                    data:{
                        id:state.controllers[state.selectIndex].id
                    }
                }).then(res=>{
                    state.controllers.splice(state.selectIndex,1)
                    if(state.controllers.length == 0){
                        state.selectIndex = -1
                    }else{
                        selectController(state.controllers.length-1)
                    }
                })
            }
            function delMethod() {
                http({
                    url:'rockysView/method',
                    method:'delete',
                    data:{
                        id:state.methods[state.selectMethodIndex].id
                    }
                }).then(res=>{
                    state.methods.splice(state.selectMethodIndex,1)
                    if(state.methods.length == 0){
                        state.selectMethodIndex = -1
                    }else{
                        selectMethod(state.methods.length-1)
                    }
                })
            }
            //预览代码
            function previewCode(){
                http({
                    url:'rockysView/code',
                    method:'post',
                    data:{
                        controller:state.controllers[state.selectIndex].name,
                        name:state.methods[state.selectMethodIndex].name,
                        desc:state.methods[state.selectMethodIndex].desc,
                        is_login:state.methods[state.selectMethodIndex].is_login,
                        is_auth:state.methods[state.selectMethodIndex].is_auth,
                        type:state.methods[state.selectMethodIndex].type,
                        config:props.methodConfig
                    }
                }).then(res=>{

                    state.code = res.data.data
                    state.codeButton = res.data.button
                })
            }
            return {
                previewCode,
                loading,
                editMethod,
                addMethod,
                delMethod,
                saveMethod,
                selectMethod,
                addController,
                saveController,
                editController,
                delController,
                selectController,
                ...toRefs(state)
            }
        }
    })
</script>

<style scoped>
    /deep/ .previewCode .el-dialog__body{
        padding: 0 !important;
    }
    .listItem {
        cursor: pointer;
    }

    .listItem:hover {
        background: #409eff;
        color: #FFFFFF;
    }

    .active {
        background: #409eff;
        color: #FFFFFF;
    }
</style>
