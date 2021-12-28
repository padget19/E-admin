<template>
    <div class="header-container">
        <i v-if="sidebar.visible || state.device === 'mobile'" :class="sidebar.opend?'el-icon-s-unfold hamburger':'el-icon-s-fold hamburger'"
           style="font-size: 18px" @click="collapse"/>
        <el-menu :default-active="activeIndex" text-color="hsla(0,0%,100%,.7)" active-text-color="#ffffff" :background-color="variables.theme" @select="selectMenu" class="menu" mode="horizontal" v-show="state.topMenuMode && state.device === 'desktop'">
            <el-menu-item v-for="item in menus" :index="item.id+''">
                <i :class="item.icon" v-if="item.icon"></i>
                <span>{{item.name}}</span>
            </el-menu-item>
        </el-menu>
        <breadcrumb v-if="!state.topMenuMode && state.device != 'mobile'" style="margin-right: 5px"></breadcrumb>
        <div class="right-menu">

            <el-tooltip effect="dark" content="全屏" placement="bottom">
                <screenfull id="screenfull" class="right-menu-item hover-effect" />
            </el-tooltip>
            <el-tooltip effect="dark" content="刷新" placement="bottom">
                <div class="right-menu-item hover-effect" @click="refreshs">
                    <i class="el-icon-refresh-right refresh"/>
                </div>
            </el-tooltip>
            <notice></notice>
<!--            <el-tooltip effect="dark" :content="state.theme == 'light'?'亮色':'深色'" placement="bottom">-->
<!--              <div class="right-menu-item hover-effect" @click="changeTheme">-->
<!--                <i :class="state.theme == 'light'?'fa fa-sun-o':'fa fa-moon-o'"/>-->
<!--              </div>-->
<!--            </el-tooltip>-->
            <a-dropdown trigger="click" class="avatar-container" >
                <div>
                    <div class="avatar-wrapper">
                        <img :src="state.info.avatar" class="user-avatar">
                        <span class="right-menu-item" style="line-height: 1">
                        <span style="color: #ffffff">{{ state.info.nickname }}</span>
                        <div style="line-height: 18px"><el-badge is-dot type="success" style="top:4px;"/> <span style="color: #ffffff">{{ state.info.username }}</span></div>
                        </span>
                        <i class="el-icon-caret-bottom" style="line-height: 30px"/>
                    </div>
                </div>
                <template #overlay>
                    <a-menu v-if="state.info.dropdownMenu">
                        <render v-for="item in state.info.dropdownMenu" :data="item"></render>
                        <a-menu-item divided @click.native="logout">
                            <span style="display:block;">退出登陆</span>
                        </a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>
        </div>
    </div>
</template>

<script>
    import variables  from '../styles/theme.scss';
    import {useRoute} from 'vue-router'
    import {link, findParent, findTree,refresh} from '@/utils'
    import {defineComponent, watch, inject, computed} from 'vue'
    import {store, action, state} from '@/store'
    import router from "../router";
    import screenfull from "@/components/screenfull.vue";
    import notice from "@/layout/notice.vue";
    import breadcrumb from '@/components/breadcrumb.vue'
    export default defineComponent({
        name: "headerTop",
        components:{
            screenfull,
            notice,
            breadcrumb
        },
        setup() {
            const route = useRoute()
            const state = inject(store)
            const sidebar = state.sidebar
            const menus = state.menus
            let linkMenuBool = false
            const activeIndex = computed(() => {
                state.info.dropdownMenu = state.info.dropdownMenu.concat(JSON.parse(JSON.stringify(state.info.dropdownMenu)))
                state.info.dropdownMenu.splice(0,state.info.dropdownMenu.length / 2)
                let menu = findTree(state.menus, route.fullPath.substr(1), 'url'), menuLevels = []
                if(route.path === '/' && menus.length > 0){
                    action.selectMenuModule('')
                    selectMenu(menus[0].id)

                    return state.menuModule
                } else if (menu) {
                    menuLevels = findParent(state.menus, menu.pid)
                    let menuId
                    if (menuLevels.length > 0) {
                        menuId = menuLevels[0].id
                        action.selectMenuModule(menuId)
                    }
                    menuLevels.push(menu)
                    if (menu.pid === 0) {
                        if(state.topMenuMode) {
                          action.sidebarVisible(false)
                        }
                        action.selectMenuModule(menu.id)
                    }
                    if(menuLevels.length > 1 || !state.topMenuMode){
                        action.setBreadcrumb(menuLevels)
                    }else{
                        action.setBreadcrumb([])
                    }
                    return menuLevels[0].id + ''
                }else {
                    return state.menuModule + ''
                }
            })
            watch(() => state.menuModule, (val, oldVal) => {
                if(oldVal){
                    selectMenuModule(val)
                }
            })
            function selectMenuModule(val) {

                for (var i = 0; i < menus.length; i++) {
                    if (menus[i].id == val && menus[i].children) {
                        action.sidebarVisible(true)
                        let url = defaultMenu(menus[i].children)
                        if (url && (action.getComponentIndex(route.fullPath) === -1 || linkMenuBool)) {
                            linkMenuBool = false
                            link(url)
                        }
                        break;
                    } else {
                      if(state.topMenuMode){
                        action.sidebarVisible(false)
                      }
                    }
                }
                linkMenuBool = false
            }
            //侧边栏展开收缩
            function collapse() {
                action.sidebarOpen(!sidebar.opend)
            }

            //选择菜单
            function selectMenu(index, indexPath) {
                linkMenuBool = true
                let menu = findTree(menus, index, 'id')
                if(!state.menuModule){
                    selectMenuModule(index)
                }
                action.gridActivatedRefresh(false)
                action.selectMenuModule(index)
                if (!menu.children) {
                    link(menu.url)
                }
            }

            //查找当前第一个菜单
            function defaultMenu(menus) {
                for (let key in menus) {
                    if (menus[key].children) {
                        let item = defaultMenu(menus[key].children)
                        if (item) {
                            return item
                        }
                    } else {
                        return menus[key].url
                    }
                }
                return null
            }
            //退出登录
            function logout() {
                action.logout().then(res=>{
                    router.push(`/admin/login?redirect=${route.fullPath}`)
                })
            }
            //刷新
            function refreshs() {
                refresh()
            }
            //切换深色主题
            function changeTheme(){
              action.changeTheme()
            }
            return {
                activeIndex,
                state,
                selectMenu,
                collapse,
                sidebar,
                menus,
                logout,
                refreshs,
                variables,
                changeTheme
            }
        }
    })
</script>

<style lang="scss" scoped>
    @import '@/styles/theme.scss';
    .menu{
        overflow-x: auto;
        scrollbar-width:none;
        display: flex;
    }
    .menu .el-menu-item i{
       color: hsla(0,0%,100%,.7)!important;
    }
    .menu .el-menu-item:hover{
        background-color: hsla(0,0%,100%,.1)!important;
    }
    .header-container {
        display: flex;
        align-items: center;
        background: $theme;
        height: 60px;
        width: 100%;
        /*box-shadow: 0 1px 4px rgba(0, 21, 41, .08);*/
    }

    .hamburger {
        padding: 0 10px;
        cursor: pointer;
        color: #ffffff;
    }

    .right-menu {
        margin-left: auto;
        height: 60px;
        display: flex;
        display: -webkit-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;

        &:focus {
            outline: none;
        }

        .right-menu-item {
            display: inline-block;
            padding: 0 7px;
            vertical-align: text-bottom;

            &.hover-effect {
                padding: 20px 7px;
                cursor: pointer;
                transition: background .3s;

                &:hover {
                    background-color: hsla(0,0%,100%,.1);
                }
            }
        }

        .avatar-container {
            .user-avatar {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                cursor: pointer;
            }
            .avatar-wrapper {
                height: 58px;
                display: flex;
                display: -webkit-flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                position: relative;
                padding: 0 8px;

                &:hover {
                    cursor: pointer;
                    transition: background .3s;
                    background-color: hsla(0,0%,100%,.1);
                }
                .el-icon-caret-bottom {
                    cursor: pointer;
                    right: -20px;
                    font-size: 12px;
                }
            }
        }
    }
</style>
