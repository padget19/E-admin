(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0e5f70"],{9773:function(e,t,l){"use strict";l.r(t);var a=l("f2bf"),r=Object(a["withScopeId"])("data-v-6db96953"),o=r((function(e,t,l,o,n,d){var i=Object(a["resolveComponent"])("el-option"),c=Object(a["resolveComponent"])("el-tree"),u=Object(a["resolveComponent"])("el-select");return Object(a["openBlock"])(),Object(a["createBlock"])(u,{modelValue:e.value,"onUpdate:modelValue":t[1]||(t[1]=function(t){return e.value=t}),ref:"selectEl",onVisibleChange:e.visibleChange},Object(a["createSlots"])({prefix:r((function(){return[Object(a["renderSlot"])(e.$slots,"prefix",{},void 0,!0)]})),default:r((function(){return[!e.multiple&&e.treeLabel?(Object(a["openBlock"])(),Object(a["createBlock"])(i,{key:0,label:e.treeLabel,value:e.treeValue},null,8,["label","value"])):Object(a["createCommentVNode"])("",!0),Object(a["renderSlot"])(e.$slots,"default",{},void 0,!0)]})),_:2},[e.tree?{name:"empty",fn:r((function(){return[Object(a["createVNode"])(c,{class:"eadmin-select-tree","node-key":e.treeProps.value,"current-node-key":e.value,data:e.treeData,"show-checkbox":e.multiple,"default-expand-all":"",props:e.treeProps,expandOnClickNode:!1,onNodeClick:e.handleNodeClick},null,8,["node-key","current-node-key","data","show-checkbox","props","onNodeClick"])]}))}:void 0]),1032,["modelValue","onVisibleChange"])})),n=l("5530"),d=(l("a9e3"),l("4de4"),l("78b1")),i=l("d257"),c=Object(a["defineComponent"])({name:"EadminSelect",props:{tree:Boolean,params:Object,treeProps:{type:Object,default:{value:"id",children:"children",label:"label",pid:"pid"}},modelValue:[Object,Array,String,Number],loadOptionField:[Object,Array,String,Number],loadField:[Object,Array,String,Number],options:{type:[Object,Array,String,Number],default:[]}},emits:["update:modelValue","update:loadField","update:loadOptionField"],setup:function(e,t){var l=Object(a["reactive"])({treeValue:e.modelValue,treeLabel:"",select:null,treeData:[],multiple:t.attrs.multiple||!1}),r=Object(a["ref"])(e.modelValue),o=Object(a["ref"])(),c=e.loadField;if(e.tree){var u=Object(i["i"])(e.options,e.modelValue,"id");u&&(l.treeLabel=u[e.treeProps.label]),e.options&&(l.treeData=Object(i["t"])(e.options,e.treeProps.value,e.treeProps.pid,e.treeProps.children))}function p(){t.attrs.multiple||Object(i["i"])(e.options,r.value,"id")||(r.value="")}function b(l){e.params&&(t.emit("update:loadField",""),t.emit("update:loadOptionField",[]),l&&Object(d["a"])({url:"/eadmin.rest",params:Object.assign(e.params,{eadminSelectLoad:!0,eadmin_id:l})}).then((function(e){t.emit("update:loadOptionField",e.data),Array.isArray(c)?(c=c.filter((function(t){return Object(i["i"])(e.data,t,"id")})),t.emit("update:loadField",c)):Object(i["i"])(e.data,c,"id")?t.emit("update:loadField",c):t.emit("update:loadField","")})))}function s(t){l.treeLabel=t[e.treeProps.label],l.treeValue=t[e.treeProps.value],r.value=t[e.treeProps.value],o.value.blur()}function m(e){e&&(l.treeLabel="")}return Object(a["watch"])((function(){return e.modelValue}),(function(e){r.value=e,p(),b(e)})),Object(a["watch"])((function(){return e.options}),(function(t){e.tree&&(l.treeData=Object(i["t"])(t,e.treeProps.value,e.treeProps.pid,e.treeProps.children)),p()})),Object(a["watch"])(r,(function(e){t.emit("update:modelValue",e)})),p(),b(r.value),Object(n["a"])({visibleChange:m,selectEl:o,handleNodeClick:s,changeHandel:b,value:r},Object(a["toRefs"])(l))}});c.render=o,c.__scopeId="data-v-6db96953";t["default"]=c}}]);