<template>
    <div :class="[sidebar.opend ? '':'collapse','sidebar-container']">
        <logo :collapse="sidebar.opend"></logo>
        <el-scrollbar style="height: calc(100vh - 60px)">
            <el-menu :default-active="activeIndex"
                     text-color="#FFFFFF"
                     :collapse="!sidebar.opend"
                     mode="vertical"
                     background-color="#000000"
                     @select="select"
                     :default-openeds="defaultOpeneds"
            >
                <menu-item v-for="item in menus" :menu="item" :key="item.id"></menu-item>
            </el-menu>
        </el-scrollbar>
    </div>
    <div class="mark" v-show="state.device === 'mobile' && sidebar.opend" @click="collapse"></div>
</template>

<script>
    import {useRoute} from 'vue-router'
    import {link, findTree} from '@/utils'
    import Logo from '../logo.vue'
    import menuItem from './menuItem.vue'
    import {defineComponent, inject, computed,watchEffect,ref} from 'vue'
    import {store, action} from '@/store'

    export default defineComponent({
        name: "sidebar",
        components: {
            Logo,
            menuItem,
        },
        setup() {
            const route = useRoute()
            const state = inject(store)
            const sidebar = state.sidebar
            const defaultOpeneds = ref([])
            //默认展开子菜单
            state.menus.forEach(res => {
                if(res.children){
                    res.children.forEach(item=>{
                        defaultOpeneds.value.push(item.id+'')
                    })
                }
            })
            //侧边栏菜单渲染
            const menus = computed(() => {
                let menu = null
                if(state.topMenuMode && state.device === 'desktop'){
                    state.menus.forEach(res => {
                        if (res.id == state.menuModule && res.children) {
                            menu = res.children
                        }
                    })
                }else{
                    menu = state.menus
                }
                return menu
            })
            //当前激活菜单
            const activeIndex = computed(() => {
                let menu = findTree(state.menus, route.fullPath.substr(1), 'url')
                if (menu) {
                    return menu.id + ''
                } else {
                    return ''
                }
            })
            //选择菜单
            function select(id, index) {
                if(state.device === 'mobile'){
                    action.sidebarOpen(!sidebar.opend)
                }
                let menu = findTree(state.menus, id, 'id')
                link(menu.url)
            }

            //侧边栏展开收缩
            function collapse() {
                action.sidebarOpen(!sidebar.opend)
            }
            return {
                defaultOpeneds,
                collapse,
                state,
                select,
                menus,
                sidebar,
                activeIndex,
            }
        }
    })
</script>

<style scoped>

    .collapse {
        width: 64px;
    }
    .mobile .collapse {
        width: 0;
    }
    .mobile .collapse .el-menu{
        display: none;
    }
    .mark {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 9998;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        background: #000;
        opacity: .5;
    }
</style>
