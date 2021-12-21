<template>
    <div style="display: flex;justify-content: space-between;margin: 0 5px">
        <el-input prefix-icon="el-icon-search" v-model="searchRecordKey" clearable placeholder="搜索"></el-input>
        &nbsp;
        <el-date-picker
                v-model="searchDate"
                type="date"
                placeholder="选择日期">
        </el-date-picker> &nbsp;
        <el-button type="primary" @click="searchRecord">搜索</el-button>
    </div>

    <div class="msgContentBox" >
        <div style="position: absolute;top: 0;left:45%" v-loading="scrollMsgRecordLoading"></div>
        <el-scrollbar style="height:100%;" ref="chatMsgHistoryBox" :style="{ height:height}">
            <div v-for="(item,key) in msgListRecord" class="msgItem">
                <div class="msgTime">
                    <el-tag type="info" size="mini">{{item.time}}</el-tag>
                </div>
                <div :ref="e=>{setRef(e,item.msg_id)}" class="rightMsgItem" v-if="item.from_uid == im.id">
                    <el-avatar style="margin-left: 10px;" size="medium" shape="square"
                               :src="im.info.avatar"></el-avatar>
                    <div class="msgItemContent">
                        <div style="width: 20px;height: 20px" v-show="item.sendStatus == 'ing'"><i
                                class="el-icon-loading"></i></div>
                        <div class="rightMsgItemBg" v-if="item.type == 1" v-html="item.content"></div>
                        <!-- 图片 -->
                        <el-image
                                fit="contain"
                                v-else-if="item.type == 2"
                                class="msgImage"
                                :src="item.content"
                                :preview-src-list="[item.content]">
                        </el-image>
                        <!-- 语音 -->
                        <audio controls :src="item.content" v-else-if="item.type == 3"></audio>
                        <div class="rightTriangle" v-if="item.type == 1"></div>
                    </div>
                </div>
                <div :ref="e=>{setRef(e,item.msg_id)}" class="leftMsgItem" v-else>
                    <el-avatar style="margin-right: 10px;" size="medium" shape="square"
                               :src="item.avatar"></el-avatar>
                    <div class="msgItemContent">
                        <div class="leftTriangle" v-if="item.type == 1"></div>
                        <div class="leftMsgItemBg" v-if="item.type == 1" v-html="item.content"></div>
                        <!-- 图片 -->
                        <el-image
                                fit="contain"
                                v-else-if="item.type == 2"
                                class="msgImage"
                                :src="item.content"
                                :preview-src-list="[item.content]">
                        </el-image>
                        <!-- 语音 -->
                        <audio controls :src="item.content" v-else-if="item.type == 3"></audio>
                    </div>
                </div>
            </div>
        </el-scrollbar>
    </div>
</template>

<script>
    import {defineComponent, nextTick, reactive, toRefs, watch,onBeforeUnmount} from "vue";
    import im from "../websocket/websocket";
    import {findTree} from "../../../utils";

    export default defineComponent({
        name: "ImMessageRecord",
        setup(){
            let msgRefs = []
            const state = reactive({
                chatMsgHistoryBox:'',
                scrollMsgRecordLoading: false,
                //聊天记录搜索关键字
                searchRecordKey: '',
                //聊天记录搜索日期
                searchDate: '',
                //聊天记录
                msgListRecord:[],
                //滚动位置聊天msg_id
                scrollMsgId:'',
            })
            im.send('msgRecordHistory',{
                msg_type:im.state.recentType,
                to_uid: im.state.recentId,
                msg_id: null,
                size: im.state.recordSize,
                date: null,
                keyword: null,
            })
            const messageHandel = im.onMessage((action,data)=>{
                switch (action) {
                    //聊天历史记录
                    case 'msgRecordHistory':
                        const length =state.msgListRecord.length
                        state.msgListRecord = data.concat(state.msgListRecord)
                        if (length === 0) {
                            scrollToBottom('chatMsgHistoryBox')
                        } else {
                            nextTick(() => {
                                state.scrollMsgRecordLoading = false
                                const ref = findTree(msgRefs,state.scrollMsgId,'msgId')
                                if(ref){
                                    const div = state.chatMsgHistoryBox.wrap
                                    const scrollHeight = div.scrollHeight
                                    const msgScrollTop = ref.dom.offsetTop
                                    let msgScrollHeight = msgScrollTop + 90;
                                    if(data.length < im.state.recordSize){
                                        msgScrollHeight = 0
                                    }
                                    if(scrollHeight > msgScrollHeight){
                                        div.scrollTop = msgScrollHeight;
                                    }else{
                                        div.scrollTop = msgScrollTop
                                    }
                                }
                            })
                        }
                        break;
                }
            })

            onBeforeUnmount(()=>{
                im.removeMessage(messageHandel)
            })
            //聊天记录滚动
            nextTick(() => {
                const scrollbar = state.chatMsgHistoryBox.wrap
                scrollbar.onscroll = () => {
                    if (scrollbar.scrollTop === 0 && state.msgListRecord.length > 0) {
                        state.scrollMsgRecordLoading = true
                        state.scrollMsgId = state.msgListRecord[0].msg_id
                        im.send('msgRecordHistory',{
                            msg_type:im.state.recentType,
                            to_uid: im.state.recentId,
                            msg_id: state.msgListRecord[0].msg_id,
                            size: im.state.recordSize,
                            date: state.searchDate,
                            keyword: state.searchRecordKey,
                        })
                    }
                }
            })

            //聊天记录滚动条置底
            function scrollToBottom(ref) {
                nextTick(() => {
                    let div = state[ref].wrap
                    div.scrollTop = div.scrollHeight
                })
            }
            //搜索聊天记录
            function searchRecord() {
                state.msgListRecord = []
                im.send('msgRecordHistory',{
                    to_uid: im.state.recentId,
                    msg_id: null,
                    size: im.state.recordSize,
                    date: state.searchDate,
                    keyword: state.searchRecordKey,
                })
            }
            function setRef (el,msgId){
                msgRefs.push({
                    msgId:msgId,
                    dom:el
                })
            }
            watch(()=>im.state.msgListRecord,val=>{
                msgRefs = []
            })
            const height=(window.innerHeight-135) +'px'
            return {
                im,
                height,
                setRef,
                searchRecord,
                ...toRefs(state),
            }
        }
    })
</script>

<style scoped>
    .msgContentBox {
        position: relative;

    }

    .msgTime {
        margin-top: 5px;
        text-align: center;
    }


    .leftTriangle {
        width: 0;
        height: 0;
        margin-left: 5px;
        margin-top: 12px;
        border-top: 5px solid transparent;
        border-bottom: 5px solid transparent;
        border-right: 5px solid #FFFFFF;
    }
    .rightTriangle {
        width: 0;
        height: 0;
        margin-right: 5px;
        margin-top: 12px;
        border-top: 5px solid transparent;
        border-bottom: 5px solid transparent;
        border-left: 5px solid #409EFF;
    }
    .leftMsgItem {
        display: flex;
        padding-top: 10px;
        margin-bottom: 10px;
        margin-left: 15px;
        margin-right: 85px;
    }

    .rightMsgItem {
        display: flex;
        padding-top: 10px;
        flex-direction: row-reverse;
        margin-bottom: 10px;
        margin-left: 72px;
        margin-right: 30px;
    }

    .msgItemContent {
        display: flex;
        max-width: 450px;
    }

    .rightMsgItemBg {
        background: #409EFF;
        color: #ffffff;
        padding: 10px 10px;
        font-size: 14px;
        border-radius: 5px;
        white-space: pre-line;
    }
    .msgImage{
        width: 120px; height: 100px;border-radius: 5px;border: 1px solid #ededed
    }
    .leftMsgItemBg {
        border-radius: 5px;
        background: #f0f0f0;
        padding: 10px 10px;
        font-size: 14px;
        white-space: pre-line;
    }
</style>
