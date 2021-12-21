<template>
    <div class="tagsView">
        <div class="tabs">
            <i class="el-icon-arrow-left tabMove" v-if="isScroll" @click="leftMove"></i>
            <ul ref="tabsTag">
                <li v-for="item in state.mainComponent" @mouseover="selectTag(item.url)" @mouseout="selectTag('')" @click="clickHandel(item.url)" :class="[route.fullPath ===item.url ? 'activte':'']">
                    <span>{{item.title}}</span>
                    <i class="el-icon-close close" v-show="route.fullPath === item.url || select === item.url" @click.stop="close(item.url)"></i>
                </li>

            </ul>
            <i class="el-icon-arrow-right tabMove" v-if="isScroll" @click="rightMove"></i>
        </div>
        <div class="breadcrumb">
            <i class="el-icon-back back" @click="back"></i>
            <el-dropdown @command="handleCommand">
                <i class="el-icon-close back"></i>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item icon="el-icon-close" command="other">关闭其他</el-dropdown-item>
                        <el-dropdown-item icon="el-icon-back" command="left">关闭左侧</el-dropdown-item>
                        <el-dropdown-item icon="el-icon-right" command="right">关闭右侧</el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>
    </div>
</template>

<script>
    import {useRoute,useRouter} from 'vue-router'
    import {defineComponent, inject,ref,onMounted,onBeforeUnmount,watch} from 'vue'
    import {store,action} from '@/store'
    import breadcrumb from '@/components/breadcrumb.vue'
    export default defineComponent({
        name: "TagsView.vue",
        components: {
            breadcrumb,
        },
        setup() {
            const route = useRoute()
            const router = useRouter()
            const state = inject(store)
            const select = ref('')
            const isScroll = ref(false)
            const tabsTag = ref('')
            function clickHandel(url) {
                router.push(url)
            }
            function selectTag(url) {
                select.value = url
            }
            function close(url) {
                if(state.mainComponent.length > 1){
                    action.clearComponent(url)
                    if(route.fullPath === url){
                        const component = state.mainComponent[state.mainComponent.length-1]
                        router.push(component.url)
                    }
                }
            }
            function back() {
                router.back()
            }
            function handleCommand(command) {
                if(command === 'left'){
                    const index = action.getComponentIndex(route.fullPath)
                    state.mainComponent.splice(0,index)
                    state.componentVariable.splice(0,index)
                }else if(command === 'right'){
                    const index = action.getComponentIndex(route.fullPath)
                    state.mainComponent.splice(index+1)
                    state.componentVariable.splice(index+1)
                }else if(command === 'other'){
                    state.mainComponent = []
                    state.componentVariable = []
                    router.push(route.fullPath)
                }
            }
            onMounted(()=>{
                window.addEventListener('resize', hasScrolled)
            })
            onBeforeUnmount(()=>{
                window.removeEventListener('resize', hasScrolled)
            })
            watch(state.mainComponent,val=>{
                hasScrolled()
            })
            //是否有滚动条
            function hasScrolled() {
                isScroll.value =  tabsTag.value.scrollWidth > tabsTag.value.clientWidth
            }
            function leftMove() {
                if(tabsTag.value.scrollLeft < tabsTag.value.clientWidth){
                    tabsTag.value.scrollLeft = 0
                }else{
                    tabsTag.value.scrollLeft = tabsTag.value.scrollLeft - tabsTag.value.clientWidth
                }
            }
            function rightMove() {
                const width = tabsTag.value.scrollWidth - tabsTag.value.scrollLeft
                if(width >=  tabsTag.value.clientWidth){
                    tabsTag.value.scrollLeft = tabsTag.value.scrollLeft + tabsTag.value.clientWidth
                }else{
                    tabsTag.value.scrollLeft = tabsTag.value.scrollWidth
                }
            }
            return {
                handleCommand,
                back,
                close,
                selectTag,
                route,
                state,
                clickHandel,
                select,
                tabsTag,
                isScroll,
                leftMove,
                rightMove
            }
        }
    })
</script>
<style lang="scss" scoped>
    @import '../styles/element-variables.scss';
    .tagsView {
        z-index: 1;
        display: flex;
        align-items: center;
        height: 40px;
        background: #FFFFFF;
        border-top: 1px solid #f6f6f6;
        box-shadow: rgba(0, 21, 41, 0.08) 0px 1px 4px;
    }
    .tagsView .tabs{
        display: flex;
        align-items:center;
        flex: 1;
        overflow: auto;
    }
    .tagsView .tabs .tabMove{
        cursor: pointer;
        padding: 0 10px;
    }
    .scroll-container {
        white-space: nowrap;
        flex: 1;

    }
    .tagsView ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: flex-end;
        height: 40px;
        flex: 1;
        overflow-x: auto;
    }
    .tagsView ul::-webkit-scrollbar {
        display:none

    }
    .tagsView .tabs::-webkit-scrollbar {
        display:none

    }
    .tagsView li {
        white-space: nowrap;
        display: flex;
        align-items: center;
        font-size: 14px;
        height: 36px;
        margin-right: -18px;
        padding: 0 25px;
        -webkit-mask-size: 100% 100%;
        mask-size: 100% 100%;
        -webkit-mask-image:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANoAAAAkBAMAAAAdqzmBAAAAMFBMVEVHcEwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlTPQ5AAAAD3RSTlMAr3DvEM8wgCBA379gj5//tJBPAAAAnUlEQVRIx2NgAAM27fj/tAO/xBsYkIHyf9qCT8iWMf6nNQhAsk2f5rYheY7Dnua2/U+A28ZEe8v+F9Ax2v7/F4DbxkUH2wzgtvHTwbYPo7aN2jZq26hto7aN2jZq25Cy7Qvctnw62PYNbls9HWz7S8/G6//PsI6H4396gAUQy1je08W2jxDbpv6nD4gB2uWp+J9eYPsEhv/0BPS1DQBvoBLVZ3BppgAAAABJRU5ErkJggg==');
        mask-image:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANoAAAAkBAMAAAAdqzmBAAAAMFBMVEVHcEwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlTPQ5AAAAD3RSTlMAr3DvEM8wgCBA379gj5//tJBPAAAAnUlEQVRIx2NgAAM27fj/tAO/xBsYkIHyf9qCT8iWMf6nNQhAsk2f5rYheY7Dnua2/U+A28ZEe8v+F9Ax2v7/F4DbxkUH2wzgtvHTwbYPo7aN2jZq26hto7aN2jZq25Cy7Qvctnw62PYNbls9HWz7S8/G6//PsI6H4396gAUQy1je08W2jxDbpv6nD4gB2uWp+J9eYPsEhv/0BPS1DQBvoBLVZ3BppgAAAABJRU5ErkJggg==');
        transition: all 0.3s;
    }
    .tagsView li:hover{
        padding-left:20px;
        padding-right:25px;
        cursor: pointer;
        color: #515a6e;
        background: #dee1e6;
    }
    .close{
        margin-left: 10px;
        margin-top: 3px;
        font-size: 12px;
    }
    .close:hover{
        padding: 1px;
        border-radius: 50%;
        color: #FFFFFF;
        background: #c0c4cc;
    }
    .activte{
        color: $--color-primary !important;
        background: #e8f4ff !important;
    }
    .breadcrumb{
        display: flex;
        align-items: center;
        margin-right: auto;
        justify-content: flex-end;
    }
    .back{
        display: flex;
        justify-content: center;
        align-items: center;
        border-left: 1px solid #ededed;
        height: 38px;
        width: 40px;
        cursor: pointer
    }
    .back:hover{
        background: #EFEFEF;
    }
</style>
