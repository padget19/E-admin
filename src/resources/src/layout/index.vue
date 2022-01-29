<template>
    <div :class="['app-wrapper',state.theme,state.device === 'mobile' ? 'mobile':'']">
        <sidebar v-if="sidebar.visible"></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <tags-view v-if="state.tagMenuMode"></tags-view>
            <a-spin wrapperClassName="main-content" :spinning="state.mainLoading">
              <div class="header-title" v-if="state.mainTitle">
                <div>
                  <span class="title">{{state.mainTitle}}</span>
                  <span class="desc" v-if="state.mainDescription">{{state.mainDescription}}</span>
                </div>
                <breadcrumb class="indexBreadcrumb" style="margin-right: 5px" v-if="state.topMenuMode && state.device != 'mobile'"></breadcrumb>
                <el-button style="margin-right: 0" v-if="!state.topMenuMode && state.device != 'mobile'" size="mini" @click="back">
                  {{ trans('back') }}</el-button>
              </div>
              <el-backtop target=".main-content"></el-backtop>
              <keep-alive :include="cacheKeys">
                <component :is="mainComponent"></component>
              </keep-alive>
              <render :data="state.component"></render>
            </a-spin>
        </div>
        <render v-for="init in state.info.init" :data="init"></render>
    </div>
</template>

<script>
    import variables  from '../styles/theme.scss';
    import {useRoute, useRouter} from 'vue-router'
    import {defineComponent, inject,computed,h,getCurrentInstance,defineAsyncComponent,resolveComponent,nextTick,watch} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar/sidebar.vue'
    import breadcrumb from '@/components/breadcrumb.vue'
    import tagsView from './tagsView.vue'
    import { store,action} from '@/store'
    import {trans} from '@/utils'
    export default defineComponent({
        name: "index",
        components: {
            Sidebar,
            headerTop,
            breadcrumb,
            tagsView
        },
        setup(){
            const route = useRoute()
            const router = useRouter()
            const state = inject(store)
            let sidebar = state.sidebar
            const cacheKeys = computed(()=>{
                return state.mainComponent.map(item=>{
                    return item.url
                })
            })
            const mainComponent = computed(()=>{
                const index = action.getComponentIndex(route.fullPath)
                if(index > -1){
                    let AsyncComp = defineAsyncComponent(
                        () =>
                            new Promise((resolve, reject) => {
                                resolve(h(resolveComponent('render'),{
                                    data:state.mainComponent[index].component
                                }))
                            })
                    )
                    if(!getCurrentInstance().appContext.components[route.fullPath]){
                        AsyncComp.name = route.fullPath
                        getCurrentInstance().appContext.app.component(route.fullPath,AsyncComp)
                    }
                    return route.fullPath
                }else{
                    return ''
                }

            })
            function back() {
                router.back()
            }
            return {
                variables,
                cacheKeys,
                mainComponent,
                route,
                state,
                sidebar,
                back,
                trans
            }
        }
    })
</script>

<style scoped>

    .header-title .title{
        font-weight: 500;
        font-size: 20px;
        color: #17233d;

    }
    .header-title .desc{
        font-size: 14px;
        display: inline-block;
        padding-left: 5px;
        color: #777;
        border-left: #dcdfe6 solid 1px;
        margin-left: 8px;
        text-indent: 3px;
    }

    .header-title{
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 400;
        font-size: 24px;
        font-family: Montserrat,Nunito,sans-serif;
        margin-bottom: 10px;
    }
    .indexBreadcrumb /deep/ .el-breadcrumb__separator{
        color: #777777 !important;
    }
    .indexBreadcrumb /deep/ .el-breadcrumb__inner{
        color: #777777 !important;
    }
</style>
