<template>
    <div class="container">
        <div class="searchBox">
            <el-input class="searchInput" prefix-icon="el-icon-search" v-model="searchKeyWord" clearable
                      placeholder="搜索"></el-input>
        </div>
        <div class="list">
            <el-scrollbar style="height:100%;">
            <recent-list v-show="leftTool == 'message'" :search="searchKeyWord"></recent-list>
            <friend-list v-show="leftTool == 'friend'" :search="searchKeyWord"></friend-list>
            <customer-list v-show="leftTool == 'customer'" :search="searchKeyWord"></customer-list>
            </el-scrollbar>
        </div>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs,watch} from "vue";
    import recentList from './recentList.vue'
    import friendList from './friendList.vue'
    import customerList from './customerList.vue'
    import im from '../websocket/websocket'
    export default defineComponent({
        name: "ImList",
        components: {
            recentList,
            friendList,
            customerList,
        },
        setup() {
            const state = reactive({
                //搜索关键字
                searchKeyWord: '',
            })
            return {
                ...toRefs(state),
                ...toRefs(im.state)
            }
        }
    })
</script>

<style scoped>
    .container {
        width: 250px;
        height: 700px;
        display: flex;
        flex-direction: column;
        background: #ebebeb;
        border-right: #dadcdf solid 1px;
    }

    .searchBox {
        padding: 5px;
        height: 50px;
        border-bottom: #dadcdf solid 1px;
    }

    .list {
        flex: 1;
        height: 650px;
    }
</style>
