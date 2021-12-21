<template>
    <el-container style="height: 100%;background: #FFFFFF">
        <el-aside width="300px">
            <el-button @click="dialogVisible = true">数据库</el-button>
            <el-dialog
                    title="数据库"
                    v-model="dialogVisible"
                    width="60%"
                    custom-class="dialogTableClass"
            >
                <fieldForm></fieldForm>
            </el-dialog>
            <draggable
                    class="components-draggable"
                    :list="componentList"
                    :group="{ name: 'componentsGroup', pull: 'clone', put: false }"
                    :sort="false"
                    :clone="clone"
                    item-key="label"
            >
                <template #item="{element}">
                    <div class="components-item">
                        <div class="components-body">
                            {{ element.label }}
                        </div>
                    </div>
                </template>
            </draggable>
        </el-aside>
        <el-main class="mainDiv">
            <el-form class="drawing" v-bind="formAttr">
                <draggable :list="generateComponentList"
                           group="componentsGroup"
                           item-key="id"
                           animation="300"
                           class="drawing"
                           @add="add"
                           @choose="choose"
                >
                    <template #item="{element,index}">
                        <div :class="['drawing-item',chooseIndex==index?'active':'']">
                            <render :data="element.formItem" :proxy-data="proxyData"></render>
                        </div>
                    </template>
                </draggable>
            </el-form>
        </el-main>
        <el-aside width="350px">
           <el-main>
               <el-form size="small">
                   <el-tabs stretch>
                       <el-tab-pane label="组件属性">
                           <el-form-item label="标题" v-if="componentItem.formItem.attribute">
                               <el-input v-model="componentItem.formItem.attribute.label" placeholder="请输入标题"></el-input>
                           </el-form-item>
                           <el-form-item label="字段" v-if="componentItem.formItem.attribute">
                               <el-input v-model="componentItem.formItem.attribute.prop" placeholder="请输入字段"></el-input>
                           </el-form-item>
                           <el-form-item label="占位提示" v-if="isAttr()">
                               <el-input v-model="componentItem.component.attribute.placeholder" placeholder="请输入占位提示"></el-input>
                           </el-form-item>
                           <el-form-item label="默认值" v-if="isAttr() && !isComponentName('ElDatePicker') && !isComponentName('ElTimePicker')">
                               <el-input v-model="proxyData[componentItem.component.modelBind.modelValue]" placeholder="请输入默认值"></el-input>
                           </el-form-item>
                           <el-form-item label="默认值" v-if="isComponentName('ElDatePicker')">
                               <el-date-picker v-model="proxyData[componentItem.component.modelBind.modelValue]" :type="componentItem.component.attribute.type"></el-date-picker>
                           </el-form-item>
                           <el-form-item label="默认值" v-if="isComponentName('ElTimePicker')">
                               <el-time-picker v-model="proxyData[componentItem.component.modelBind.modelValue]" :is-range="componentItem.component.attribute.isRange"></el-time-picker>
                           </el-form-item>

                           <el-form-item label="长度限制" v-if="isComponentName('ElInput') && isAttr('type',['text','textarea'])" >
                               <el-input v-model="componentItem.component.attribute.maxlength" placeholder="请输入长度限制"></el-input>
                           </el-form-item>
                           <el-form-item label="字数统计" v-if="isComponentName('ElInput') && isAttr('type',['text','textarea'])">
                               <el-switch v-model="componentItem.component.attribute.showWordLimit" ></el-switch>
                           </el-form-item>

                           <el-form-item label="行数" v-if="isAttr('type','textarea')">
                               <el-input v-model="componentItem.component.attribute.rows"  placeholder="请输入行数" type="number"></el-input>
                           </el-form-item>

                           <el-form-item label="高度" v-if="isComponentName('EadminEditor')">
                               <el-input v-model="componentItem.component.attribute.height"  placeholder="请输入高度"></el-input>
                           </el-form-item>
                           <el-form-item label="图片框大小" v-if="isAttr('displayType','image')">
                               <el-input v-model="componentItem.component.attribute.height"  placeholder="请输入大小"></el-input>
                           </el-form-item>
                           <el-form-item label="竖向高度" v-if="isComponentName('ElSlider')">
                               <el-input v-model="componentItem.component.attribute.height"  placeholder="请输入高度"></el-input>
                           </el-form-item>

                           <el-form-item label="最大值" v-if="isAttr('type','number') || isComponentName('ElSlider')">
                               <el-input v-model="componentItem.component.attribute.max" placeholder="请输入设置最大值" type="number"></el-input>
                           </el-form-item>
                           <el-form-item label="最小值" v-if="isAttr('type','number') || isComponentName('ElSlider')">
                               <el-input v-model="componentItem.component.attribute.min" placeholder="请输入设置最小值" type="number"></el-input>
                           </el-form-item>
                           <el-form-item label="数字间隔" v-if="isAttr('type','number') || isComponentName('ElSlider')">
                               <el-input v-model="componentItem.component.attribute.step" placeholder="请输入设置间隔" type="number"></el-input>
                           </el-form-item>
                           <el-form-item label="禁用" v-if="isAttr()">
                               <el-switch v-model="componentItem.component.attribute.disabled" ></el-switch>
                           </el-form-item>
                           <el-form-item label="只读" v-if="isComponentName('ElInput')">
                               <el-switch v-model="componentItem.component.attribute.readonly" ></el-switch>
                           </el-form-item>
                           <el-form-item label="可清空" v-if="isComponentName('ElInput') || isComponentName('EadminSelect') || isComponentName('ElDatePicker') || isComponentName('ElTimePicker')">
                               <el-switch v-model="componentItem.component.attribute.clearable" ></el-switch>
                           </el-form-item>
                           <el-form-item label="数据源" v-if="isComponentName('EadminSelect') || isComponentName('ElRadioGroup') || isComponentName('EadminCheckboxGroup')">
                               <el-switch v-model="componentItem.component.attribute.filterable" ></el-switch>
                           </el-form-item>
                           <el-form-item label="最大数量" v-if="isComponentName('EadminCheckboxGroup')">
                               <el-input v-model="componentItem.component.attribute.max" placeholder="请输入最大数量" type="number"></el-input>
                           </el-form-item>
                           <el-form-item label="最小数量" v-if="isComponentName('EadminCheckboxGroup')">
                               <el-input v-model="componentItem.component.attribute.min" placeholder="请输入最小数量" type="number"></el-input>
                           </el-form-item>
                           <el-form-item label="默认全选" v-if="isComponentName('EadminCheckboxGroup')">
                               <el-switch v-model="componentItem.component.attribute.checkAll" ></el-switch>
                           </el-form-item>
                           <el-form-item label="开启全选" v-if="isComponentName('EadminCheckboxGroup')">
                               <el-switch v-model="componentItem.component.attribute.onCheckAll" ></el-switch>
                           </el-form-item>
                           <el-form-item label="边框" v-if="isComponentName('ElRadioGroup')">
                               <el-switch v-model="componentItem.component.attribute.border" ></el-switch>
                           </el-form-item>
                           <el-form-item label="可搜索" v-if="isComponentName('EadminSelect')">
                               <el-switch v-model="componentItem.component.attribute.filterable" ></el-switch>
                           </el-form-item>
                           <el-form-item label="多选" v-if="isComponentName('EadminSelect') || isComponentName('EadminUpload')">
                               <el-switch v-model="componentItem.component.attribute.multiple" ></el-switch>
                           </el-form-item>
                           <el-form-item label="多选最大数量" v-if="isComponentName('EadminSelect')">
                               <el-input type="number" v-model="componentItem.component.attribute.multipleLimit" placeholder="最多可以选择的项目数"></el-input>
                           </el-form-item>

                           <el-form-item label="打开的文字" v-if="isComponentName('EadminSwitch')">
                               <el-input v-model="componentItem.component.attribute.checkedChildren" placeholder="请输入"></el-input>
                           </el-form-item>
                           <el-form-item label="关闭的文字" v-if="isComponentName('EadminSwitch')">
                               <el-input v-model="componentItem.component.attribute.unCheckedChildren" placeholder="请输入"></el-input>
                           </el-form-item>
                           <el-form-item label="打开的值" v-if="isComponentName('EadminSwitch')">
                               <el-input v-model="componentItem.component.attribute.activeValue" placeholder="请输入"></el-input>
                           </el-form-item>
                           <el-form-item label="关闭的值" v-if="isComponentName('EadminSwitch')">
                               <el-input v-model="componentItem.component.attribute.inactiveValue" placeholder="请输入"></el-input>
                           </el-form-item>

                           <el-form-item label="取消两个日期联动" v-if="isAttr('type',['daterange','datetimerange'])">
                               <el-switch v-model="componentItem.component.attribute.unlinkPanels"></el-switch>
                           </el-form-item>
                           <el-form-item label="显示输入框" v-if="isComponentName('ElSlider')">
                               <el-switch v-model="componentItem.component.attribute.showInput" ></el-switch>
                           </el-form-item>
                           <el-form-item label="控制按钮" v-if="isComponentName('ElSlider')">
                               <el-switch v-model="componentItem.component.attribute.showInputControls" ></el-switch>
                           </el-form-item>
                           <el-form-item label="间断点" v-if="isComponentName('ElSlider')">
                               <el-switch v-model="componentItem.component.attribute.showStops" ></el-switch>
                           </el-form-item>
                           <el-form-item label="tooltip" v-if="isComponentName('ElSlider')">
                               <el-switch v-model="componentItem.component.attribute.showTooltip" ></el-switch>
                           </el-form-item>
                           <el-form-item label="范围选择" v-if="isComponentName('ElSlider')">
                               <el-switch v-model="componentItem.component.attribute.range" ></el-switch>
                           </el-form-item>
                           <el-form-item label="竖向模式" v-if="isComponentName('ElSlider')">
                               <el-switch v-model="componentItem.component.attribute.vertical" ></el-switch>
                           </el-form-item>
                           <el-form-item label="允许半选" v-if="isComponentName('ElRate')">
                               <el-switch v-model="componentItem.component.attribute.allowHalf" ></el-switch>
                           </el-form-item>
                           <el-form-item label="拖拽" v-if="isComponentName('EadminUpload')">
                               <el-switch v-model="componentItem.component.attribute.drag" ></el-switch>
                           </el-form-item>
                           <el-form-item label="唯一文件名" v-if="isComponentName('EadminUpload')">
                               <el-switch v-model="componentItem.component.attribute.isUniqidmd5" ></el-switch>
                           </el-form-item>
                           <el-form-item label="finer文件管理" v-if="isComponentName('EadminUpload')">
                               <el-switch v-model="componentItem.component.attribute.finder" ></el-switch>
                           </el-form-item>
                       </el-tab-pane>
                       <el-tab-pane label="表单属性">
                           <el-form-item label="表单尺寸">
                               <el-radio-group v-model="formAttr.size">
                                   <el-radio-button label="medium" >中等</el-radio-button>
                                   <el-radio-button label="small">较小</el-radio-button>
                                   <el-radio-button label="mini" >迷你</el-radio-button>
                               </el-radio-group>
                           </el-form-item>
                           <el-form-item label="对齐方式">
                               <el-radio-group v-model="formAttr.labelPosition">
                                   <el-radio-button label="left">左对齐</el-radio-button>
                                   <el-radio-button label="right">右对齐</el-radio-button>
                                   <el-radio-button label="top">顶部对齐</el-radio-button>
                               </el-radio-group>
                           </el-form-item>
                           <el-form-item label="标签宽度">
                               <el-input v-model="formAttr.labelWidth"></el-input>
                           </el-form-item>
                       </el-tab-pane>
                   </el-tabs>
               </el-form>
           </el-main>
        </el-aside>
    </el-container>
</template>

<script>
    import draggable from 'vuedraggable'
    import fieldForm from './fieldForm'
    import {defineComponent,reactive,toRefs,watch} from "vue";
    import {randomCoding,forEach} from '@/utils/index'
    export default defineComponent({
        name: "index",
        components: {
            draggable,
            fieldForm
        },
        setup(){
            const proxyData = reactive({})
            const state = reactive({
                dialogVisible:false,
                //左边组件
                componentList:[
                    {
                        label: "单行文本",
                        component:generateComponent('ElInput',{type:'text'})
                    },
                    {
                        label: "数字输入框",
                        component:generateComponent('ElInput',{type:'number'})
                    },
                    {
                        label: "密码框",
                        component:generateComponent('ElInput',{type:'password'})
                    },
                    {
                        label: "多行文本",
                        component:generateComponent('ElInput',{type:'textarea'})
                    },
                    {
                        label: "编辑器",
                        component:generateComponent('EadminEditor')
                    },
                    {
                        label: "下拉框",
                        component:generateComponent('EadminSelect',{clearable:true,filterable:true})
                    },
                    {
                        label: "单选框",
                        component:generateComponent('ElRadioGroup')
                    },
                    {
                        label: "多选框",
                        component:generateComponent('EadminCheckboxGroup')
                    },
                    {
                        label: "开关",
                        component:generateComponent('EadminSwitch',{checkedChildren:'打开',unCheckedChildren:'关闭',activeValue:1,inactiveValue:0})
                    },
                    {
                        label: "日期",
                        component:generateComponent('ElDatePicker',{clearable:true,type:'date',valueFormat:'YYYY-MM-DD'})
                    },
                    {
                        label: "年",
                        component:generateComponent('ElDatePicker',{clearable:true,type:'year',valueFormat:'YYYY'})
                    },
                    {
                        label: "月",
                        component:generateComponent('ElDatePicker',{clearable:true,type:'month',valueFormat:'YYYY-MM'})
                    },
                    {
                        label: "多选日期",
                        component:generateComponent('ElDatePicker',{clearable:true,type:'dates',valueFormat:'YYYY-MM-DD'})
                    },
                    {
                        label: "日期时间",
                        component:generateComponent('ElDatePicker',{clearable:true,type:'datetime',valueFormat:'YYYY-MM-DD HH:mm:ss'})
                    },
                    {
                        label: "日期范围",
                        component:generateComponent('ElDatePicker',{clearable:true,type:'daterange',valueFormat:'YYYY-MM-DD'})
                    },
                    {
                        label: "日期时间范围时间",
                        component:generateComponent('ElDatePicker',{clearable:true,type:'datetimerange',valueFormat:'YYYY-MM-DD HH:mm:ss'})
                    },
                    {
                        label: "时间",
                        component:generateComponent('ElTimePicker',{clearable:true,isRange:false,valueFormat:'HH:mm:ss'})
                    },
                    {
                        label: "时间范围",
                        component:generateComponent('ElTimePicker',{clearable:true,isRange:true,valueFormat:'YHH:mm:ss'})
                    },
                    {
                        label: "滑块",
                        component:generateComponent('ElSlider')
                    },
                    {
                        label: "颜色选择器",
                        component:generateComponent('ElColorPicker')
                    },
                    {
                        label: "评分",
                        component:generateComponent('ElRate')
                    },
                    {
                        label: "图片上传",
                        component:generateComponent('EadminUpload',{displayType:'image',height:120,finder:true,isUniqidmd5:true})
                    },
                    {
                        label: "文件上传",
                        component:generateComponent('EadminUpload',{displayType:'file',finder:true,isUniqidmd5:true})
                    },
                ],
                //中间生成组件
                generateComponentList:[],
                //当前选中中间的组件
                componentItem:{
                    formItem:{},
                    component:null,
                },
                //表单属性
                formAttr:{
                    size:'medium',
                    labelPosition:'right',
                    labelWidth:'100px',
                },
                //当前选中组件index
                chooseIndex:-1,
            })
            watch(()=>state.chooseIndex,value=>{
                state.componentItem = state.generateComponentList[value]
            })
            watch(()=>state.componentItem.component,value => {
                state.componentItem.id = randomCoding(20)
            },{deep:true})
            watch(()=>state.componentItem.formItem,value => {
                state.componentItem.id = randomCoding(20)
            },{deep:true})
            function generateComponent(name,attribute={},modelBind ={},content=[]) {
                return {
                    name: name,
                    attribute: attribute,
                    bindAttribute:modelBind,
                    modelBind:modelBind,
                    bind:[],
                    where:{
                        AND:[],
                        OR:[],
                    },
                    map:{
                        attribute: []
                    },
                    content:content,
                    directive:[],
                    event: []
                }
            }
            function clone(e) {
                const cloneJson = JSON.parse(JSON.stringify(e.component))
                const modelValue = randomCoding(20)

                cloneJson.bindAttribute.modelValue = modelValue
                cloneJson.modelBind.modelValue = modelValue
                const formItem = generateComponent('ElFormItem',{label:'标题'})
                formItem.content.default = [cloneJson]
                return {
                    id:modelValue,
                    formItem:formItem,
                    component:cloneJson
                };
            }
            function choose(e) {
                state.chooseIndex = e.oldIndex
            }
            function add(e) {
                state.chooseIndex = e.newIndex
            }
            function isComponentName(name) {
                if(!state.componentItem.component){
                    return false
                }
                if(state.componentItem.component.name != name){
                    return false
                }
                return true
            }
            function isAttr(attr='',value='') {
                if(!state.componentItem.component){
                    return false
                }
                if(attr){
                    if(Array.isArray(value)){
                        if(value.indexOf(state.componentItem.component.attribute[attr]) == -1){
                            return false
                        }
                    }else{
                        if(state.componentItem.component.attribute[attr] != value){
                            return false
                        }
                    }
                }
                return true
            }
            return {
                ...toRefs(state),
                proxyData,
                add,
                clone,
                choose,
                isAttr,
                isComponentName
            }
        }
    })
</script>

<style scoped>
    .mainDiv{
        border-left: 1px solid #cccccc;
        border-right: 1px solid #cccccc;
    }
    .drawing{
        height:100%;
    }
    .components-draggable .components-item {
        display: inline-block;
        width: 48%;
        margin: 1%;
    }
    .components-body {
        padding: 8px 10px;
        background: #f6f7ff;
        font-size: 12px;
        cursor: move;
        border: 1px dashed #f6f7ff;
        border-radius: 3px;
    }
    .drawing-item{
        cursor: move;
        position: relative;
        padding: 7.5px;

    }
    .active{
        background: #f6f7ff;
    }
    .drawing-item:hover{
        cursor: move;
        background: #f6f7ff;
    }
</style>
