<template>
    <!-- 头像消息框 -->
    <div class="miniBox" @click="openIm">
        <el-avatar :src="im.info.avatar" style="margin-right: 10px"></el-avatar>
        <el-badge :value="unReadNum" type="danger" :max="99" v-if="unReadNum > 0">{{im.info.nickname}}</el-badge>
        <el-badge :type="online" is-dot v-else>{{im.info.nickname}}</el-badge>
    </div>
    <div v-show="dialogShow" @click.native="popoverVisibleClose">
        <el-dialog
                v-model="dialogVisible"
                width="900px"
                :close-on-click-modal="false"
                :show-close="false"
                :fullscreen="fullscreen"
        >
            <div class="main" @click="popoverVisibleClose">
                <left-tools :avatar="im.info.avatar"></left-tools>
                <list></list>
                <div class="mainContent">
                    <div style="position: absolute;right: 5px;top:5px;cursor: pointer;">
                        <el-tooltip effect="light" content="转接" v-if="recentType == 'customerMsg'">
                            <i @click="customerDialogVisible = true" class="el-icon-refresh rightTools"></i>
                        </el-tooltip>
                        <el-tooltip effect="light" content="结束会话" v-if="recentType == 'customerMsg'">
                            <i @click="closeCustomer(recentId)" class="el-icon-switch-button rightTools"></i>
                        </el-tooltip>
<!--                        <i class="el-icon-full-screen rightTools" @click="fullscreen = !fullscreen"></i>-->
                        <i class="el-icon-close rightTools" @click="dialogVisible=false"></i>
                    </div>
                    <im-message v-show="leftTool === 'message'"></im-message>
                    <im-friend v-show="leftTool === 'friend'"></im-friend>
                </div>
            </div>
        </el-dialog>
        <customer-dialog></customer-dialog>
    </div>
</template>

<script>
    import {defineComponent,reactive,toRefs,nextTick,watch,onBeforeUnmount} from "vue";
    import leftTools from './leftTools.vue'
    import list from './list/list.vue'
    import ImMessage from './main/message.vue'
    import ImFriend from './main/friend.vue'
    import customerDialog from './customerDialog.vue'
    import im from './websocket/websocket'
    import {closeCustomer} from './customer/customer'
    export default defineComponent({
        name: "EadminIm",
        components:{
            leftTools,
            list,
            ImMessage,
            ImFriend,
            customerDialog
        },
        props:{
            username:String,
            password:String,
            websocket:String,
        },
        setup(props){
            const state = reactive({
                dialogShow:false,
                dialogVisible:true,
                fullscreen:false,
                online: 'danger',
            })
            nextTick(()=>{
                im.connect(props.websocket,props.username,props.password)
                state.dialogVisible = false
            })
            im.onMessage((action,data)=>{
                switch (action) {
                    //登录成功
                    case 'login':
                        if(data.code === 0){
                            state.online = 'success'
                            im.info = data.info
                        }
                        break;
                }
            })
            im.onClose(e=>{
                state.online = 'danger'
            })
            //监听工具栏切换
            watch(()=>im.state.leftTool,val=>{
                if(val === 'message'){

                }else if(val === 'friend'){
                    im.send('getAddFriend')
                }else if(val === 'customer'){
                    im.send('getCustomerConnList')
                }
            })

            function openIm(){
                state.dialogShow = true
                state.dialogVisible = true
                // if(this.nowMsgUid > -1){
                //     let index = this.getMsgIdKey(this.recentList,this.nowMsgUid, 'from_uid')
                //     this.selectMsgUser(this.recentList[index], index)
                // }
            }
            function popoverVisibleClose() {
                im.state.msgList.forEach(item => {
                    item.popoverVisible = false
                })
            }
            onBeforeUnmount(()=>{
                im.close()
            })
            return {
                im,
                closeCustomer,
                ...toRefs(state),
                ...toRefs(im.state),
                openIm,
                popoverVisibleClose
            }
        }
    })
</script>

<style scoped>
    .main{

        top: 0;
        left: 0;
        position: absolute;
        min-width: 900px;
        height: 700px;
        display: flex;
    }
    .miniBox {
        padding: 0 20px;
        height: 60px;
        background: #ffffff;
        position: fixed;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 1px 1px 50px #cccccc;
        border-radius: 5px;
        cursor: pointer;
        z-index: 100;
    }
    .rightTools {
        margin: 0 5px;
    }
    .mainContent{
        height: 100%;
        background: #ffffff;
        flex: 1;
    }

</style>
