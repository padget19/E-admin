export function generateComponent(name,attribute={},modelBind ={},content=[]) {
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
export function getComponent(value){
    let component = null
    components.forEach(item=>{
        item.options.forEach(option=>{
            if(option.value == value){
                component = option.component
            }
        })
    })
    return component
}
export const components = [
    {
        label:'输入型',
        options:[
            {
                value:1,
                label: "单行文本",
                component:generateComponent('ElInput',{type:'text',component:'text'})
            },
            {
                value:2,
                label: "数字输入框",
                component:generateComponent('ElInputNumber',{component:'number',controls:false})
            },
            {
                value:24,
                label: "计算器",
                component:generateComponent('ElInputNumber',{component:'number'})
            },
            {
                value:3,
                label: "密码框",
                component:generateComponent('ElInput',{type:'password',component:'password'})
            },
            {
                value:4,
                label: "多行文本",
                component:generateComponent('ElInput',{type:'textarea',component:'textarea'})
            },
            {
                value:5,
                label: "编辑器",
                component:generateComponent('EadminEditor',{component:'editor'})
            },
        ]
    },
    {
        label:'选择型',
        options:[
            {
                value:6,
                label: "下拉框",
                component:generateComponent('EadminSelect',{clearable:true,filterable:true,component:'select',options:[{label:'选项1',value:1},{label:'选项2',value:2}]})
            },
            {
                value:7,
                label: "单选框",
                component:generateComponent('ElRadioGroup',{component:'radio',options:[{label:'选项1',value:1},{label:'选项2',value:2}]})
            },
            {
                value:8,
                label: "多选框",
                component:generateComponent('EadminCheckboxGroup',{component:'checkbox',options:[{label:'选项1',value:1},{label:'选项2',value:2}]})
            },
            {
                value:9,
                label: "开关",
                component:generateComponent('EadminSwitch',{component:'switch',checkedChildren:'打开',unCheckedChildren:'关闭',activeValue:1,inactiveValue:0})
            },
            {
                value:10,
                label: "滑块",
                component:generateComponent('ElSlider',{component:'slider'})
            },
            {
                value:11,
                label: "评分",
                component:generateComponent('ElRate',{component:'rate'})
            },
            {
                value:12,
                label: "颜色选择器",
                component:generateComponent('ElColorPicker',{component:'color'})
            },
        ]
    },
    {
        label:'时间型',
        options:[
            {
                value:13,
                label: "日期",
                component:generateComponent('ElDatePicker',{component:'date',clearable:true,type:'date',valueFormat:'YYYY-MM-DD'})
            },
            {
                value:14,
                label: "年",
                component:generateComponent('ElDatePicker',{component:'year',clearable:true,type:'year',valueFormat:'YYYY'})
            },
            {
                value:15,
                label: "月",
                component:generateComponent('ElDatePicker',{component:'month',clearable:true,type:'month',valueFormat:'YYYY-MM'})
            },
            {
                value:16,
                label: "日期时间",
                component:generateComponent('ElDatePicker',{component:'datetime',clearable:true,type:'datetime',valueFormat:'YYYY-MM-DD HH:mm:ss'})
            },
            {
                value:17,
                label: "日期范围",
                component:generateComponent('ElDatePicker',{component:'dateRange',clearable:true,type:'daterange',valueFormat:'YYYY-MM-DD'})
            },
            {
                value:18,
                label: "日期时间范围",
                component:generateComponent('ElDatePicker',{component:'datetimeRange',clearable:true,type:'datetimerange',valueFormat:'YYYY-MM-DD HH:mm:ss'})
            },
            {
                value:19,
                label: "时间",
                component:generateComponent('ElTimePicker',{component:'time',clearable:true,isRange:false,valueFormat:'HH:mm:ss'})
            },
            {
                value:20,
                label: "时间范围",
                component:generateComponent('ElTimePicker',{component:'timeRange',clearable:true,isRange:true,valueFormat:'YHH:mm:ss'})
            },
            {
                value:21,
                label: "多选日期",
                component:generateComponent('ElDatePicker',{component:'dates',clearable:true,type:'dates',valueFormat:'YYYY-MM-DD'})
            },
        ]
    },
    {
        label: '上传型',
        options: [
            {
                value:22,
                label: "图片上传",
                component:generateComponent('EadminUpload',{component:'image',displayType:'image',width:120,height:120,finder:true,isUniqidmd5:true})
            },
            {
                value:23,
                label: "文件上传",
                component:generateComponent('EadminUpload',{component:'file',displayType:'file',finder:true,isUniqidmd5:true})
            },
        ]
    },
]