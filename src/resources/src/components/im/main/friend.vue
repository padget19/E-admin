<template>
    <el-scrollbar style="height:100%;margin-top: 30px">
        <div class="friendMsgItem" :style="{padding:'0 100px'}"
             v-for="(item,index) in addFriendList">
            <el-avatar style="margin-left: 10px;" shape="square"
                       :src="item.avatar"></el-avatar>
            <div style="flex:1;margin-left: 10px;">
                <div class="name">{{item.nickname}}</div>
                <div class="content">{{item.desc}}</div>
            </div>
            <div>
                <span v-if="item.pass">已添加</span>
                <el-button type="success" size="small" v-else="item.pass" @click="passFriend(item.id,index)">接受</el-button>
            </div>
        </div>
    </el-scrollbar>
</template>

<script>
    import {defineComponent, reactive, toRefs} from "vue";
    import im from '../websocket/websocket'
    export default defineComponent({
        name: "ImFriend",
        setup(){
            const state = reactive({
                //添加好友列表
                addFriendList: [],
            })
            //同意添加好友
            function passFriend(uid,index){
                im.send('passFriend',{
                    index:index,
                        friend_id:uid

                })
                state.addFriendList[index].pass = true
            }
            im.onMessage((action,data)=>{
                switch (action) {
                    //获取添加好友列表
                    case 'getAddFriend':
                        state.addFriendList = data
                        break;

                }
            })
            return {
                ...toRefs(state),
                passFriend
            }
        }

    })
</script>

<style scoped>
    .friendMsgItem {
        height: 60px;
        display: flex;
        align-items: center;
        border-bottom: solid 1px #EEEEEE;
    }
    .friendMsgItem .name {
        font-size: 14px;
        color: #000000
    }

    .friendMsgItem:hover {
        background: #dedcda;
        cursor: pointer;
    }
    .friendMsgItem .content {
        font-size: 14px;
        color: #666;
        width: 140px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

</style>
