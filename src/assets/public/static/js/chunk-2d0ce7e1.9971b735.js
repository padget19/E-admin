(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0ce7e1"],{"604e":function(e,t,n){"use strict";n.r(t);var c=n("f2bf"),o=Object(c["withScopeId"])("data-v-14cb2989"),a=o((function(e,t,n,o,a,r){var p=Object(c["resolveComponent"])("render");return Object(c["openBlock"])(),Object(c["createBlock"])(p,{data:e.component},null,8,["data"])})),r=n("7996"),p=n("d257"),d=Object(c["defineComponent"])({name:"watchComponent",props:{field:String,params:Object,watchComponent:Object,proxyData:Object},setup:function(e){var t=Object(r["b"])(),n=t.loading,o=t.http,a=Object(c["ref"])(e.watchComponent),d=Object(c["ref"])(!1),i=Object(p["c"])((function(t){o({url:"eadmin.rest",params:Object.assign(e.params,{value:t,field:e.field})}).then((function(e){a.value=e.content.default[0]}))}),300);return Object(c["watch"])((function(){return e.proxyData[e.field]}),(function(t){i(t,e.field)})),{loading:n,show:d,component:a}}});d.render=a,d.__scopeId="data-v-14cb2989";t["default"]=d}}]);