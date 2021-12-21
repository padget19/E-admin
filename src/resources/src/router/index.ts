import { createRouter, createWebHashHistory ,RouteLocationNormalized,NavigationGuardNext} from 'vue-router'
import request from '@/utils/axios'
import { action,state } from '@/store'
// @ts-ignore
import md5 from 'js-md5'
import Layout from '@/layout/index.vue'
import Login from '@/layout/login.vue'
import Im from '@/components/im/index.vue'
import Generate from '@/components/generate/index.vue'
import {refresh} from '@/utils'
import app from "@/app";
import {nextTick} from "q";
let asyncCmponent:any
const routes = [
    {
        path: '/admin/login',
        component: Login,
    },
    {
        path: '/generate',
        component: Generate,
    },
    {
        path: '/im',
        component: Im,
    },
    {
        path: '/:pathMatch(.*)',
        component: Layout,
    },

]
const router = createRouter({
    history: createWebHashHistory(),
    routes
})
var formRoute:RouteLocationNormalized
router.beforeEach( async(to:RouteLocationNormalized, from:RouteLocationNormalized,next:NavigationGuardNext) => {
    formRoute = from
    if(to.path == '/generate'){
        return next()
    }
    if(!localStorage.getItem('eadmin_token') && to.path !== '/admin/login'){
        return next('/admin/login?redirect='+to.fullPath)
    }
    if(!state.info.id && localStorage.getItem('eadmin_token')){
        await action.getInfo()
    }
    if(to.path === '/refresh'){
        action.clearComponent(from.fullPath)
        action.loading(true)
        await loadComponent(from.fullPath)
        return next(from.fullPath)
    }
    if(to.fullPath !== '/' && action.getComponentIndex(to.fullPath) === -1){
        await loadComponent(to.fullPath)
    }
    return next()
})
router.afterEach((to:RouteLocationNormalized)=>{
    const styleDoms = document.querySelectorAll('[data-key=eadmin_style_' + md5(formRoute.path)+']')
    if(styleDoms){
        styleDoms.forEach(item=>{
            item.remove()
        })
    }
    if(asyncCmponent && to.fullPath !== '/'){
        action.component(asyncCmponent,to.fullPath)
    }
    if(state.refresh){
        action.refresh(false)
        refresh()
    }
})
function loadComponent(url:string){
    delete app._context.components[url]

    return new Promise((resolve, reject) =>{
        request({
            url:url
        }).then((res:any)=>{
            asyncCmponent = res;
            resolve(res)
        }).catch((res:any)=>{
            asyncCmponent = null
            reject(res)
        }).finally(()=>{
            action.loading(false)
        })
    })
}

export default router

