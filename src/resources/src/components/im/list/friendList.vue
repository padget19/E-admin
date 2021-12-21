<template>
    <div>
        <div>
            <p class="letter">新的朋友</p>
            <div class="friendMsgItem">
                <div>
                    <el-avatar class="newFriendImage" shape="square" icon="fa fa-user-circle-o"></el-avatar>
                </div>
                <div style="flex:1;margin-left: 10px;">
                    <div class="name">新的朋友</div>
                </div>
                <el-popover
                        placement="bottom"
                        title="新的朋友"
                        width="300"
                        trigger="click">
                    <div style="display: flex;margin-bottom: 10px">
                        <el-input prefix-icon="el-icon-search" @change="searchNewFriend" v-model="searchFriendKey" clearable
                                  placeholder="好友昵称、账号"></el-input>&nbsp;&nbsp;
                        <el-button size="small" @click="searchNewFriend">查找</el-button>
                    </div>
                    <div class="friendMsgItem"
                         v-for="(item,index) in findFriendList">
                        <div>
                            <el-avatar style="margin-left: 10px;" shape="square"
                                       :src="item.avatar"></el-avatar>
                        </div>
                        <div style="flex:1;margin-left: 10px;">
                            <div class="name">{{item.nickname}}</div>
                            <div class="content">{{item.username}}</div>
                        </div>
                        <el-popover
                                placement="right"
                                width="200"
                                trigger="manual"
                                v-model:visible="item.visable">
                            <div style="margin-bottom: 5px">添加好友 <i class="el-icon-close"
                                                                    style="float: right;cursor: pointer"
                                                                    @click="item.visable=false"></i></div>
                            <el-input type="textarea" :row="3" placeholder="备注" resize="none"
                                      v-model="newFriendDesc"></el-input>
                            <br><br>
                            <el-button type="primary" size="small" @click="sendAddFriend(item.id,index)">确认发送</el-button>
                            <template #reference>
                                <el-button type="primary" size="small" style="margin-right: 10px" v-if="!isFriend(item.id)" @click="addFriend(index)">添加</el-button>
                            </template>
                        </el-popover>
                    </div>
                    <template #reference>
                        <i class="el-icon-plus addButton" ></i>
                    </template>

                </el-popover>

            </div>
        </div>
        <div v-for="(item,index) in friendListFilter">
            <div class="friendMsgItem" @click="selectFriendUser(item,index)">
                <div>
                    <el-avatar style="margin-left: 10px;" shape="square"
                               :src="item.avatar"></el-avatar>
                </div>
                <div style="flex:1;margin-left: 10px;">
                    <div class="name">{{item.nickname}}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs,computed} from "vue";
    import im from '../websocket/websocket'
    import {findArrKey} from '@/utils'
    export default defineComponent({
        name: "ImFriendList",
        props: {
            search:String,
        },
        setup(props) {
            const state = reactive({
                //查找新朋友列表
                findFriendList: [],
                //添加好友备注
                newFriendDesc: '',
                //新朋友搜索关键字
                searchFriendKey:'',
            })
            im.onMessage((action,data)=>{
                switch (action) {
                    case 'login':
                        if(data.code === 0){
                            im.state.friendList = []
                            //获取好友列表
                            im.send('friendList')
                        }
                        break;
                    //查找新朋友
                    case 'findNewFriend':
                        state.findFriendList = data
                        break;
                    //成功添加好友
                    case 'passFriend':
                        im.state.friendList = []
                        im.send('friendList')
                        break;
                    //好友列表
                    case 'friendList':
                        im.state.friendList = im.state.friendList.concat(data)
                        break;
                }
            })
            //好友列表
            const friendListFilter = computed(()=> {
                if (im.state.friendList.length > 0) {
                    return im.state.friendList.filter(item => {
                        return item.nickname.indexOf(props.search) >= 0
                    })
                }
            })
            //发送添加好友请求
            function sendAddFriend(uid, index) {
                im.send('addFriend', {
                    uid: uid,
                    desc: state.newFriendDesc
                })
                state.findFriendList[index].visable = false
            }
            //搜索新朋友
            function searchNewFriend(val){
                if(val){
                    im.send('findNewFriend',{
                        keyword: val
                    })
                }else{
                    state.findFriendList = []
                }
            }
            //是否是朋友
            function isFriend(uid) {
                if (uid == im.uid) {
                    return true
                }
                for (let i = 0; i < im.state.friendList.length; i++) {
                    if (im.state.friendList[i].id === uid) {
                        return true
                    }

                }
                return false
            }
            //添加好友确认
            function addFriend(index){
                state.findFriendList.forEach(item=>{
                    item.visable = false
                })
                state.findFriendList[index].visable = true
            }
            //选择好友
            function selectFriendUser(item, index) {
                im.state.leftTool = 'message'
                index = findArrKey(im.state.recentList, item.id, 'from_uid')
                item.from_uid = item.id
                if (index === null) {
                    item.msg_type = 'msg'
                    index = im.state.recentList.push(item)
                    index--
                }else{
                    item = im.state.recentList[index]
                }
                im.selectUser(item, index)
            }
            return {
                ...toRefs(state),
                ...toRefs(im.state),
                friendListFilter,
                sendAddFriend,
                isFriend,
                addFriend,
                searchNewFriend,
                selectFriendUser
            }
        }
    })
</script>

<style scoped>
    .addButton {
        margin-right: 20px;
        background: #ededed;
        color: #409EFF;padding: 5px;
        cursor: pointer;
    }
    .friendMsgItem {
        height: 60px;
        display: flex;
        align-items: center;
        border-bottom: solid 1px #dadcdf;
    }
    .friendMsgItem:hover {
        background: #dedcda;
        cursor: pointer;
    }
    .newFriendImage{
        margin-left: 10px;color: #409EFF;background: #fff;border: 1px solid #ededed
    }
    .letter {
        margin: 11px;
        color: #999999;
    }
</style>
