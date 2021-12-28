import ElementPlus from "element-plus";
import 'font-awesome/css/font-awesome.css'
import router from './router'
import {store,state,action} from './store'
import './styles/index.scss'
import app from  './app'
import './component'
import './directive'
import zhLocale from 'element-plus/lib/locale/lang/zh-cn'
import request from '@/utils/axios'
import { Switch ,Table ,Dropdown,Menu,Steps,Result,List,Popover,Spin} from "ant-design-vue";
import VueMarkdownEditor from '@kangc/v-md-editor';
import '@kangc/v-md-editor/lib/style/base-editor.css';
import vuepressTheme from '@kangc/v-md-editor/lib/theme/vuepress.js';
import '@kangc/v-md-editor/lib/theme/style/vuepress.css';

import Prism from 'prismjs';

VueMarkdownEditor.use(vuepressTheme, {
    Prism
})
app.use(VueMarkdownEditor)
app.use(Spin)
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
