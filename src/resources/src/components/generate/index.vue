<template>
    <el-container style="background: #FFFFFF">
        <el-container>
            <el-header class="header">
                <div>
                    一键CURD
                </div>
                <div>
                    <el-dialog
                            title="数据库"
                            v-model="dialogVisible"
                            width="70%"
                            custom-class="dialogTableClass"
                    >
                        <fieldForm></fieldForm>
                    </el-dialog>
                    <el-button @click="dialogVisible = true">数据库</el-button>
                    <el-button @click="dialogEnumVisible = true">枚举字典</el-button>
                    <el-dialog
                            title="枚举字典"
                            @open="openEnum"
                            v-model="dialogEnumVisible"
                            width="80%"
                            custom-class="dialogTableClass"
                    >
                        <render :data="dialogEnum"></render>
                    </el-dialog>
                    <el-button @click="codeVisible = true">预览代码</el-button>
                    <el-button @click="isSave = true" type="primary">保存</el-button>
                </div>
            </el-header>
             <el-container>
                 <el-aside width="300px">
                     <contoller v-model:field="initField" v-model:method-type="methodType" v-model:method-config="generateData" v-model:codeVisible="codeVisible" v-model:save="isSave"></contoller>
                 </el-aside>
                 <el-aside width="300px" v-if="methodType == 2">
                     <div v-for="group in componentList">
                         <div style="display: flex;align-items: center;margin: 5px">
                             <i class="el-icon-printer"></i><div style="margin-left: 5px">{{group.label}}</div>
                         </div>
                         <draggable
                                 class="components-draggable"
                                 :list="group.options"
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
                     </div>
                 </el-aside>
                 <el-main class="mainDiv">
                     <EadminGrid v-if="methodType == 1" v-bind="generateData.grid"></EadminGrid>
                     <el-form class="drawing" v-else-if="methodType == 2" v-bind="generateData.form">
                         <draggable :list="generateComponentList"
                                    group="componentsGroup"
                                    item-key="id"
                                    animation="300"
                                    class="drawing"
                                    @add="add"
                                    @choose="choose"
                         >
                             <template #item="{element,index}">
                                 <div :class="['drawing-item',chooseIndex==index?'active':'']" @mouseover="itemHover = index" @mouseout="itemHover = -1">
                                     <render :data="element.formItem" :proxy-data="proxyData"></render>
                                     <span title="复制" class="drawing-item-copy" v-show="itemHover==index" @click="copy(element)"><i class="el-icon-copy-document"></i></span>
                                     <span title="删除" class="drawing-item-delete" v-show="itemHover==index" @click="del(index)"><i class="el-icon-delete"></i></span>
                                 </div>
                             </template>
                         </draggable>
                         <el-form-item v-if="!generateData.form.hideAction">
                             <el-button type="primary">保存</el-button>
                             <el-button>重置</el-button>
                         </el-form-item>
                     </el-form>
                 </el-main>
             </el-container>
        </el-container>
        <el-aside width="360px">
           <el-main>
               <el-form size="small" label-width="100px">
                   <el-tabs stretch>
                       <el-tab-pane v-if="methodType == 1" label="列">
                           <draggable
                                   :list="generateData.grid.columns"
                                   :animation="340"
                                   group="selectItem"
                                   handle=".option-drag"
                           >
                               <template #item="{element,index}">
                                   <div class="select-item">
                                       <div class="select-line-icon option-drag">
                                           <i class="el-icon-s-operation" />
                                       </div>
                                       <el-input
                                               placeholder="字段"
                                               size="small"
                                               v-model="element.prop"
                                               @input="element.dataIndex = element.prop"
                                       />
                                       <el-input v-model="element.label" @input="element.title = element.label" placeholder="标题" size="small" />
                                       <div class="close-btn select-line-icon" @click="generateData.grid.columns.splice(index, 1)">
                                           <i class="el-icon-remove-outline" />
                                       </div>
                                   </div>
                               </template>
                           </draggable>
                           <div style="margin-left: 20px;">
                               <el-button
                                       style="padding-bottom: 0"
                                       icon="el-icon-circle-plus-outline"
                                       type="text"
                                       @click="addColumn(generateData.grid.columns)"
                               >
                                   添加列
                               </el-button>
                           </div>
                           <el-divider />
                       </el-tab-pane>
                       <el-tab-pane v-if="methodType == 1" label="属性">
                           <el-form-item label="斑马纹" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.stripe" ></el-switch>
                           </el-form-item>
                           <el-form-item label="边框" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.bordered" ></el-switch>
                           </el-form-item>
                           <el-form-item label="列自撑开" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.fit" ></el-switch>
                           </el-form-item>
                           <el-form-item label="快捷搜索" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.quickSearch" ></el-switch>
                           </el-form-item>
                           <el-form-item label="隐藏工具栏" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.hideTools" ></el-switch>
                           </el-form-item>
                           <el-form-item label="隐藏选择框" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.hideSelection" ></el-switch>
                           </el-form-item>
                           <el-form-item label="展开筛选" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.expandFilter" ></el-switch>
                           </el-form-item>
                           <el-form-item label="隐藏回收站" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.hideTrashed" ></el-switch>
                           </el-form-item>
                           <el-form-item label="隐藏删除选中" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.hideDeleteSelection" ></el-switch>
                           </el-form-item>
                           <el-form-item label="隐藏删除按钮" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.hideDeleteButton" ></el-switch>
                           </el-form-item>
                           <el-form-item label="隐藏操作列" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.hideAction" ></el-switch>
                           </el-form-item>
                           <el-form-item label="关闭分页" v-if="generateData.grid">
                               <el-switch v-model="generateData.grid.hidePage" ></el-switch>
                           </el-form-item>
                           <el-form-item label="分页大小" v-if="!generateData.grid.hidePage">
                               <el-input-number v-model="generateData.grid.setPageLimit" ></el-input-number>
                           </el-form-item>
                       </el-tab-pane>
                       <el-tab-pane v-if="methodType == 2" label="组件属性">
                           <el-form-item label="组件类型">
                               <el-select v-model="componentItem.form_type" @change="changeComponent" size="small" style="width: 100%">
                                   <el-option-group v-for="group in componentList" :label="group.label">
                                       <el-option v-for="item in group.options" :label="item.label" :value="item.value"></el-option>
                                   </el-option-group>
                               </el-select>
                           </el-form-item>
                           <template v-if="isAttr('component',['dateRange','datetimeRange','timeRange'])">
                               <el-form-item label="字段" v-if="componentItem.formItem.attribute">
                                   <el-input v-model="componentItem.formItem.attribute.startField" placeholder="请输入开始字段"></el-input>
                                   <el-input v-model="componentItem.formItem.attribute.endField" placeholder="请输入结束字段"></el-input>

                               </el-form-item>
                           </template>
                           <template v-else>
                               <el-form-item label="字段" v-if="componentItem.formItem.attribute">
                                   <el-input v-model="componentItem.formItem.attribute.prop" placeholder="请输入字段"></el-input>
                               </el-form-item>
                           </template>

                           <el-form-item label="标题" v-if="componentItem.formItem.attribute">
                               <el-input v-model="componentItem.formItem.attribute.label" placeholder="请输入标题"></el-input>
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
                           <template v-if="isComponentName('EadminSelect') || isComponentName('ElRadioGroup') || isComponentName('EadminCheckboxGroup')">
                               <el-divider>选项</el-divider>
                               <div style="text-align: center;margin-bottom: 5px">
                                   <el-radio-group v-model="componentItem.component.attribute.optionsType">
                                       <el-radio label="">字典</el-radio>
                                       <el-radio label="1">自定义</el-radio>
                                   </el-radio-group>
                               </div>

                                <div v-show="componentItem.component.attribute.optionsType == 1">
                                   <draggable

                                           :list="componentItem.component.attribute.options"
                                           :animation="340"
                                           group="selectItem"
                                           handle=".option-drag"
                                   >
                                       <template #item="{element,index}">
                                           <div class="select-item">
                                               <div class="select-line-icon option-drag">
                                                   <i class="el-icon-s-operation" />
                                               </div>
                                               <el-input v-model="element.label" placeholder="选项名" size="small" />
                                               <el-input
                                                       placeholder="选项值"
                                                       size="small"
                                                       v-model="element.value"
                                               />
                                               <div class="close-btn select-line-icon" @click="componentItem.component.attribute.options.splice(index, 1)">
                                                   <i class="el-icon-remove-outline" />
                                               </div>
                                           </div>
                                       </template>
                                   </draggable>
                                   <div style="margin-left: 20px;">
                                       <el-button
                                               style="padding-bottom: 0"
                                               icon="el-icon-circle-plus-outline"
                                               type="text"
                                               @click="addSelectItem(componentItem.component.attribute.options)"
                                       >
                                           添加选项
                                       </el-button>
                                   </div>
                                </div>
                               <div v-show="!componentItem.component.attribute.optionsType" style="text-align: center">
                                   <el-select v-model="componentItem.component.attribute.enum_id" @change="selectEnum">
                                       <el-option v-for="item in enumOptions" :value-key="item.id" :value="item.child" :label="item.name"></el-option>
                                   </el-select>
                               </div>
                               <el-divider />
                           </template>
                           <el-form-item label="长度限制" v-if="isComponentName('ElInput') && isAttr('type',['text','textarea'])" >
                               <el-input-number v-model="componentItem.component.attribute.maxlength" placeholder="请输入"></el-input-number>
                           </el-form-item>
                           <el-form-item label="字数统计" v-if="isComponentName('ElInput') && isAttr('type',['text','textarea'])">
                               <el-switch v-model="componentItem.component.attribute.showWordLimit" ></el-switch>
                           </el-form-item>

                           <el-form-item label="行数" v-if="isAttr('type','textarea')">
                               <el-input-number v-model="componentItem.component.attribute.rows"  placeholder="请输入"></el-input-number>
                           </el-form-item>

                           <el-form-item label="高度" v-if="isComponentName('EadminEditor')">
                               <el-input v-model="componentItem.component.attribute.height"  placeholder="请输入高度"></el-input>
                           </el-form-item>
                           <el-form-item label="图片框宽度" v-if="isAttr('displayType','image')">
                               <el-input-number v-model="componentItem.component.attribute.width"  placeholder="请输入宽度"></el-input-number>
                           </el-form-item>
                           <el-form-item label="图片框高度" v-if="isAttr('displayType','image')">
                               <el-input-number v-model="componentItem.component.attribute.height"  placeholder="请输入高度"></el-input-number>
                           </el-form-item>
                           <el-form-item label="竖向高度" v-if="isComponentName('ElSlider')">
                               <el-input v-model="componentItem.component.attribute.height"  placeholder="请输入高度"></el-input>
                           </el-form-item>

                           <el-form-item label="最大值" v-if="isComponentName('ElInputNumber') || isAttr('type','number') || isComponentName('ElSlider')">
                               <el-input-number v-model="componentItem.component.attribute.max" placeholder="请输入"></el-input-number>
                           </el-form-item>
                           <el-form-item label="最小值" v-if="isComponentName('ElInputNumber') || isAttr('type','number') || isComponentName('ElSlider')">
                               <el-input-number v-model="componentItem.component.attribute.min" placeholder="请输入"></el-input-number>
                           </el-form-item>
                           <el-form-item label="数字间隔" v-if="isComponentName('ElInputNumber') || isAttr('type','number') || isComponentName('ElSlider')">
                               <el-input-number v-model="componentItem.component.attribute.step" placeholder="请输入"></el-input-number>
                           </el-form-item>
                           <el-form-item label="数值精度" v-if="isComponentName('ElInputNumber')">
                               <el-input-number v-model="componentItem.component.attribute.precision" placeholder="请输入" ></el-input-number>
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
                           <!--<el-form-item label="数据源" v-if="isComponentName('EadminSelect') || isComponentName('ElRadioGroup') || isComponentName('EadminCheckboxGroup')">-->
                               <!--<el-switch v-model="componentItem.component.attribute.filterable" ></el-switch>-->
                           <!--</el-form-item>-->
                           <el-form-item label="最大数量" v-if="isComponentName('EadminCheckboxGroup')">
                               <el-input v-model="componentItem.component.attribute.max" placeholder="请输入最大数量" type="number"></el-input>
                           </el-form-item>
                           <el-form-item label="最小数量" v-if="isComponentName('EadminCheckboxGroup')">
                               <el-input v-model="componentItem.component.attribute.min" placeholder="请输入最小数量" type="number"></el-input>
                           </el-form-item>
                           <el-form-item label="按钮位置" v-if="isComponentName('ElInputNumber')">
                               <el-radio-group v-model="componentItem.component.attribute.controlsPosition">
                                   <el-radio-button label="">默认</el-radio-button>
                                   <el-radio-button label="right">右侧</el-radio-button>
                               </el-radio-group>
                           </el-form-item>
                           <el-form-item label="严格步数" v-if="isComponentName('ElInputNumber')">
                               <el-switch v-model="componentItem.component.attribute.stepStrictly" ></el-switch>
                           </el-form-item>
                           <el-form-item label="控制按钮" v-if="isComponentName('ElInputNumber')">
                               <el-switch v-model="componentItem.component.attribute.controls" ></el-switch>
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
                               <el-input-number v-model="componentItem.component.attribute.multipleLimit" placeholder="最多可以选择的项目数"></el-input-number>
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

                           <el-form-item label="取消日期联动" v-if="isAttr('type',['daterange','datetimerange'])">
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
                           <el-form-item label="finer文件" v-if="isComponentName('EadminUpload')">
                               <el-switch v-model="componentItem.component.attribute.finder" ></el-switch>
                           </el-form-item>
                           <el-form-item label="必填" v-if="componentItem.formItem.attribute">
                               <el-switch v-model="componentItem.formItem.attribute.required" ></el-switch>
                           </el-form-item>
                       </el-tab-pane>
                       <el-tab-pane v-if="methodType == 2" label="表单属性">
                           <el-form-item label="表单尺寸">
                               <el-radio-group v-model="generateData.form.size">
                                   <el-radio-button label="medium" >中等</el-radio-button>
                                   <el-radio-button label="small">较小</el-radio-button>
                                   <el-radio-button label="mini" >迷你</el-radio-button>
                               </el-radio-group>
                           </el-form-item>
                           <el-form-item label="对齐方式">
                               <el-radio-group v-model="generateData.form.labelPosition">
                                   <el-radio-button label="left">左对齐</el-radio-button>
                                   <el-radio-button label="right">右对齐</el-radio-button>
                                   <el-radio-button label="top">顶部对齐</el-radio-button>
                               </el-radio-group>
                           </el-form-item>
                           <el-form-item label="标签宽度">
                               <el-input v-model="generateData.form.labelWidth"></el-input>
                           </el-form-item>
                           <el-form-item label="标签的后缀">
                               <el-input v-model="generateData.form.labelSuffix"></el-input>
                           </el-form-item>
                           <el-form-item label="隐藏按钮">
                               <el-switch v-model="generateData.form.hideAction" ></el-switch>
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
    import contoller from './contoller'
    import {defineComponent,reactive,toRefs,watch} from "vue";
    import {randomCoding,forEach} from '@/utils/index'
    import {useHttp} from '@/hooks'
    import {generateComponent,components,getComponent} from './config'
    export default defineComponent({
        name: "index",
        components: {
            draggable,
            contoller,
            fieldForm
        },
        setup(){
            const {http} =  useHttp()
            const proxyData = reactive({})
            proxyData.filterField = {}
            const state = reactive({
                enumOptions:[],
                itemHover:-1,
                codeVisible:false,
                code:null,
                dialogEnum:null,
                dialogVisible:false,
                dialogEnumVisible:false,
                //左边组件
                componentList:components,
                //中间生成组件
                generateComponentList:[],
                //当前选中中间的组件
                componentItem:{
                    formItem:{},
                    component:null,
                    form_type:'',
                },

                //当前选中组件index
                chooseIndex:-1,
                methodType:'',
                initField:[],
                isSave:false,
                generateData:{
                    grid:{
                        filterField:'filterField',
                        setPageLimit:20,
                        columns:[],
                        static:true,
                        proxyData:proxyData,
                    },
                    form:{
                        //表单属性
                        size:'medium',
                        labelPosition:'right',
                        labelWidth:'100px',
                        list:[]
                    },
                },
            })
            http('PlugDictionary/data').then(res=>{
                state.enumOptions = res.data
            })
            watch(()=>state.initField,value=>{
                if(state.methodType == 1){
                    state.generateData.grid.columns.splice(0,state.generateData.grid.columns.length)
                    value.forEach(item=> {
                        state.generateData.grid.columns.push({
                            align: 'center',
                            dataIndex: item.field,
                            title: item.desc,
                            label: item.desc,
                            prop: item.field,
                            slots: {
                                customRender: 'default',
                                title: 'eadmin_' + item.field,
                            }
                        })
                    })
                }else if(state.methodType == 2){
                    state.generateComponentList = []
                    value.forEach(item=>{
                        const component = getComponent(item.form_type)
                        if(item.value == 'NULL' || item.value == 'empty'){
                            item.value = ''
                        }
                        state.generateComponentList.push(componentGenerate(component,item.form_type,item.value,{label:item.desc,prop:item.field}))
                    })
                }
            })
            watch(()=>state.chooseIndex,value=>{
                state.componentItem = state.generateComponentList[value]
            })
            watch(()=>state.generateData.form,value=>{
                state.generateComponentList = value.list
            })
            watch(()=>state.generateComponentList,value=>{
                state.generateComponentList.map(item=>{
                    proxyData[item.component.modelBind.modelValue] = item.component.attribute.modelValue
                    item.formItem.content.default = []
                    componentParseOptions(item.component)
                    item.formItem.content.default.push(item.component)
                })
                state.generateData.form.list = value
            })
            watch(()=>state.componentItem.component,component => {
                componentParseOptions(component)
                state.componentItem.id = randomCoding(20)
            },{deep:true})
            watch(()=>state.componentItem.formItem,value => {
                state.componentItem.id = randomCoding(20)
            },{deep:true})
            function componentParseOptions(component) {
                const name = component.attribute.component
                if(['select','radio','checkbox'].indexOf(name) > -1){
                    const options = []
                    if(name == 'select'){
                        component.attribute.options.forEach(item=>{
                            options.push(generateComponent('ElOption',{label:item.label,value:item.value}))
                        })
                    }else if(name == 'radio'){
                        component.attribute.options.forEach(item=>{
                            const radio = generateComponent('ElRadio',{label:item.value})
                            radio.content.default = []
                            radio.content.default.push(item.label)
                            options.push(radio)
                        })
                    }else if(name == 'checkbox'){
                        component.attribute.options.forEach(item=>{
                            const checkbox = generateComponent('ElCheckbox',{label:item.value})
                            checkbox.content.default = []
                            checkbox.content.default.push(item.label)
                            options.push(checkbox)
                        })
                    }
                    component.content.default = options
                }
            }
            function changeComponent(type){
                if(type >0 ){
                    const component = getComponent(type)
                    const item  = JSON.parse(JSON.stringify(state.componentItem))
                    state.componentItem = componentGenerate(component,type,item.component.attribute.modelValue,item.formItem.attribute)
                    state.generateComponentList[state.chooseIndex] = state.componentItem
                }
            }
            function clone(e) {
                return componentGenerate(e.component,e.value)
            }
            function componentGenerate(component,form_type,value='',formItemAttr={label:'标题'}) {
                const cloneJson = JSON.parse(JSON.stringify(component))
                const modelValue = randomCoding(20)
                if(cloneJson.attribute.component == 'checkbox'){
                    proxyData[modelValue] = []
                }else{
                    proxyData[modelValue] = value
                }

                cloneJson.bindAttribute.modelValue = modelValue
                cloneJson.modelBind.modelValue = modelValue
                const formItem = generateComponent('ElFormItem',formItemAttr)
                formItem.content.default = [cloneJson]
                return {
                    id:modelValue,
                    formItem:formItem,
                    component:cloneJson,
                    form_type:form_type
                }
            }
            //复制
            function copy(element){
                const component = JSON.parse(JSON.stringify(element))
                component.id = randomCoding(20)
                const modelValue = randomCoding(20)
                proxyData[modelValue] = component.component.attribute.modelValue
                component.component.bindAttribute.modelValue = modelValue
                component.component.modelBind.modelValue = modelValue
                component.formItem.content.default = []
                component.formItem.content.default.push(component.component)
                state.generateComponentList.push(component)
            }
            function del(index) {
                state.generateComponentList.splice(index,1)
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
            function addSelectItem(options) {
                options.push({
                    label: '',
                    value: ''
                })
            }
            function openEnum() {
                http('plugDictionary').then(res=>{
                    state.dialogEnum = res
                })
            }
            function addColumn(arr) {
                arr.push({
                    align: 'center',
                    dataIndex: '',
                    title: '',
                    prop: '',
                    slots: {
                        customRender: 'default',
                    }
                })
            }
            //切换字典选项
            function selectEnum(value){
                state.componentItem.component.attribute.options = value.map(item=>{
                    return {
                        label:item.name,
                        value:item.value,
                    }
                })
            }
            return {
                selectEnum,
                openEnum,
                addColumn,
                addSelectItem,
                changeComponent,
                del,
                copy,
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

<style lang="scss" scoped>
    .header{
        border-bottom: 1px solid #cccccc;
        border-right: 1px solid #cccccc;
        display: flex;
        justify-content: space-between;
        height: 40px !important;
        align-items: center;
    }
    .mainDiv{
        border-left: 1px solid #cccccc;
        border-right: 1px solid #cccccc;
    }
    .drawing{
        position: relative;
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
    .drawing-item-copy{
        position: absolute;
        right: 56px;
        color: #409eff;
        background: #fff;
        border: 1px solid #409eff;
        top: -10px;
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        border-radius: 50%;
        font-size: 12px;
        cursor: pointer;
        z-index: 1;

    }
   .drawing-item-delete{
       position: absolute;
       right: 24px;
       color: #f56c6c;
       background: #fff;
       border: 1px solid #f56c6c;
       top: -10px;
       width: 22px;
       height: 22px;
       line-height: 22px;
       text-align: center;
       border-radius: 50%;
       font-size: 12px;
       cursor: pointer;
       z-index: 1;
   }
    .drawing-item-copy:hover{
        background: #409eff;
        color: #ffffff;
    }
    .drawing-item-delete:hover{
        background: #f56c6c;
        color: #ffffff;
    }
    .drawing-item:hover{
        cursor: move;
        background: #f6f7ff;
    }
    .select-item {
        display: flex;
        align-items: center;
        border: 1px dashed #fff;
        box-sizing: border-box;
    & .close-btn {
          cursor: pointer;
          color: #f56c6c;
      }
    & .el-input + .el-input {
          margin-left: 4px;
      }
    }
    .select-item + .select-item {
        margin-top: 4px;
    }
    .select-item.sortable-chosen {
        border: 1px dashed #409eff;
    }
    .select-line-icon {
        line-height: 32px;
        font-size: 22px;
        padding: 0 4px;
        color: #777;
    }
    .option-drag {
        cursor: move;
    }
</style>
