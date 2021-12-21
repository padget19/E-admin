import ElementPlus from "element-plus";
import 'element-plus/lib/theme-chalk/index.css';
import 'font-awesome/css/font-awesome.css'
import router from './router'
import {store,state,action} from './store'
import './styles/index.scss'
import app from  './app'
import './component'
import './directive'
import zhLocale from 'element-plus/lib/locale/lang/zh-cn'
import request from '@/utils/axios'
import { Switch ,Table ,Dropdown,Menu,Steps,Result,List,Popover} from "ant-design-vue";
app.use(Popover)
app.use(List)
app.use(Result)
app.use(Steps)
app.use(Dropdown)
app.use(Menu)
app.use(Table)
app.use(Switch)
app.use(ElementPlus,{size: 'medium', locale :zhLocale})
app.use(router)
app.provide(store, state)
app.config.globalProperties.$request = request
app.config.globalProperties.$action = action
app.mount('#app')
