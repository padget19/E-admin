<template>
    <div>
        <div class="header">{{recentTitle}}</div>
        <!-- 内容区 -->
        <div class="msgContentBox">
            <div style="position: absolute;top: 0;left:45%" v-loading="scrollMsgLoading"></div>
            <el-scrollbar style="height:100%;" ref="chatMsgBox">
                <div v-for="(item,key) in msgList" :key="key" class="msgItem">
                    <div v-if="!isDelMsg(item.msg_id)">
                        <div class="msgTime" v-if="key == 0">
                            <el-tag type="info" size="mini">{{item.time}}</el-tag>
                        </div>
                        <div class="msgTime" v-else-if="item.time != msgList[key-1].time">
                            <el-tag type="info" size="mini">{{item.time}}</el-tag>
                        </div>
                        <div :ref="e=>{setRef(e,item.msg_id)}" class="rightMsgItem" v-if="item.from_uid == im.id">
                            <el-avatar style="margin-left: 10px;" size="medium" shape="square"
                                       :src="im.info.avatar"></el-avatar>
                            <!-- 文字 -->
                            <div class="msgItemContent">
                                <div style="width: 20px;height: 20px" v-show="item.sendStatus == 'ing'">
                                    <i class="el-icon-loading"></i></div>
                                <i class="el-icon-warning"
                                   style="color: red;margin-right: 5px;cursor:pointer;"
                                   v-show="item.sendStatus == 'error'" @click="resend(item,key)"></i>
                                <el-popover
                                        popper-class="recallClass"
                                        placement="bottom"
                                        trigger="manual"
                                        v-model:visible="item.popoverVisible">
                                    <div style="display: flex;align-items: center;justify-content: space-between;">
                                        <span v-if="item.recall_id"
                                                 @click="recallMsg(item)"><i
                                                class="el-icon-refresh-left blue"></i> <span style="cursor: pointer">撤回</span>
                                        </span>
                                        <span @click="delMsg(item.msg_id,key)"><i
                                                class="el-icon-error red"></i> <span style="cursor: pointer">删除</span>
                                        </span>
                                    </div>
                                    <template #reference>
                                        <div>
                                            <div class="rightMsgItemBg" v-if="item.type == 1" @contextmenu.prevent="openMenu(key)" v-html="item.content"></div>
                                            <!-- 图片 -->
                                            <el-image
                                                    fit="contain"
                                                    v-else-if="item.type == 2"
                                                    class="msgImage"
                                                    :src="item.content"
                                                    :preview-src-list="[item.content]" @contextmenu.prevent="openMenu(key)">
                                            </el-image>

                                            <!-- 语音 -->
                                            <audio controls :src="item.content" v-else-if="item.type == 3" @contextmenu.prevent="openMenu(key)"></audio>
                                        </div>
                                    </template>

                                </el-popover>
                                <div class="rightTriangle" v-if="item.type == 1"></div>
                            </div>

                        </div>
                        <div :ref="e=>{setRef(e,item.msg_id)}" class="leftMsgItem" v-else>
                            <el-avatar style="margin-right: 10px;" size="medium" shape="square"
                                       :src="item.avatar"></el-avatar>
                            <!-- 文字 -->
                            <div class="msgItemContent">
                                <div class="leftTriangle" v-if="item.type == 1"></div>
                                <el-popover
                                        popper-class="recallClass"
                                        placement="bottom"
                                        trigger="manual"
                                        v-model:visible="item.popoverVisible">
                                    <div style="display: flex;align-items: center;justify-content: space-between;">
                                        <el-link :underline="false" type="danger" @click="delMsg(item.msg_id,key)"><i
                                                class="el-icon-error"></i> 删除
                                        </el-link>
                                    </div>
                                    <template #reference>
                                        <div>
                                            <div class="leftMsgItemBg" v-if="item.type == 1" @contextmenu.prevent="openMenu(key)" v-html="item.content"></div>
                                            <!-- 图片 -->
                                            <el-image
                                                    fit="contain"
                                                    @contextmenu.prevent="openMenu(key)"
                                                    v-else-if="item.type == 2"
                                                    class="msgImage"
                                                    :src="item.content"
                                                    :preview-src-list="[item.content]">

                                            </el-image>
                                            <!-- 语音 -->
                                            <audio controls :src="item.content" v-else-if="item.type == 3" @contextmenu.prevent="openMenu(key)"></audio>
                                        </div>
                                    </template>
                                </el-popover>
                            </div>

                        </div>
                    </div>
                </div>
            </el-scrollbar>
        </div>
        <!-- 发送区 -->
        <div class="sendBox" v-show="recentId > -1">
            <div class="tools">
                <el-popover placement="top" width="200" trigger="click">
                    <div style="display: flex;width: 410px;flex-wrap: wrap;justify-content: space-between">
                        <div v-for="item in emojiArr" class="emoji" @click="sendEmoji(item)">
                            {{item}}
                        </div>
                    </div>
                    <template #reference>
                        <i class="fa fa-smile-o item" style="font-size: 20px"></i>
                    </template>

                </el-popover>
                <i class="el-icon-chat-dot-round item" @click="recordDrawer = true" style="float: right"></i>
                <el-drawer :title="im.state.recentTitle" v-model="recordDrawer" direction="rtl" :destroy-on-close="true">
                    <message-record></message-record>
                </el-drawer>
            </div>
            <el-scrollbar style="height:105px">
                <div
                        ref="sendInput"
                        contenteditable="true"
                        class="sendTextarea"
                        @keydown.enter="enterClear"
                        @keyup.enter.exact="sendMsg"
                        @keyup.enter.ctrl="sendMsgWrap"
                        @paste="pasteSendInput"
                        @input="sendContentChange"
                        @blur="getCursorSelection">
                </div>
            </el-scrollbar>
            <el-popover placement="top-start" content="发送内容不能为空" v-model:visible="sendTipvisible" trigger="manual">
                <template #reference>
                    <el-button size="mini" style="float: right;margin-right: 15px" @click="sendMsg">发送</el-button>
                </template>

            </el-popover>
        </div>
        <audio id="eadmin_notice_music" controls="controls" style="display:none">
            <source src="../../../assets/notice.mp3" type="audio/mpeg">
        </audio>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs,nextTick,watch,onBeforeUpdate} from "vue";
    import {ElNotification,ElMessage} from "element-plus";
    import im from '../websocket/websocket'
    import {findArrKey,findTree,genId} from '@/utils'
    import messageRecord from "./messageRecord";
    export default defineComponent({
        name: "ImMessage",
        components:{
            messageRecord,
        },
        setup(props){
            let msgRefs = []

            const state = reactive({
                chatMsgBox:'',
                //滚动位置聊天msg_id
                scrollMsgId:'',
                //聊天内容滚动加载状态
                scrollMsgLoading:false,
                //内容输入框
                sendInput:'',
                //发送内容
                sendContent:'',
                //发送内容光标位置
                msgSelection:null,
                sendTipvisible:false,
                //表情
                emojiArr:['😀','😁','😂','😃','😄','😅','😆','😉','😊','😋','😎','😍','😘','😗','😙','😚','😇','😐','😑','😶','😏','😣','😥','😮','😯','😪','😫','😴','😌','😛','😜','😝','😒','😓','😔','😕','😲','😷','😖','😞','😟','😤','😢','😭','😦','😧','😨','😬','😰','😱','😳','😵','😡','😠','🌹','🍀','🍎','💰','📱','🌙','🍁','🍂','🍃','🌷','💎','🔪','🔫','🏀','👄','👍','🔥','💪','👈','👉','👆','👇','👌','👍','👎','✊'],
                //聊天记录抽屉
                recordDrawer:false
            })

            im.onMessage((action,data)=>{
               let recent = null
               switch (action) {
                   //发送信息结果
                   case 'msgResult':
                       const key = findArrKey(im.state.msgList, data.msg_id, 'msg_id')
                       if(key !== null && key > -1){
                           im.state.msgList[key].sendStatus = 'ok'
                           im.state.msgList[key].time = data.time
                           //撤回id
                           im.state.msgList[key].recall_id = data.msg_id
                           setTimeout(() => {
                               if(im.state.msgList[key]){
                                   im.state.msgList[key].recall_id = false
                               }
                           }, 120000)
                       }

                       if(data.msg_type === 'msg'){
                           recent = findTree(im.state.recentList, data.to_uid, 'from_uid')
                       }else if(data.msg_type === 'customerMsg'){
                           recent = findTree(im.state.recentList, data.group_id, 'group_id')
                       }
                       if(recent){
                           recent.time = data.time
                           recent.content = getTypeContent(data)
                       }
                       break;
                   //收到信息
                   case 'msg':
                       if(data.msg_type === 'msg'){
                           recent = findTree(im.state.recentList, data.from_uid, 'from_uid')
                       }else if(data.msg_type === 'customerMsg'){
                           recent = findTree(im.state.recentList, data.group_id, 'group_id')
                       }
                       if (recent === null) {
                           im.state.recentList.push(data)
                       } else {
                           if (im.isSelectUser(data)) {
                               im.state.msgList.push(data)
                               im.readMsg(data)
                           } else {
                               showNotification(data.nickname,data.content,data.avatar)
                               recent.unReadNum = data.unReadNum
                               document.getElementById('eadmin_notice_music').play()
                           }
                           recent.content = getTypeContent(data)
                           recent.time = data.time
                       }
                       scrollToBottom('chatMsgBox')
                       break;
                   //撤回成功
                   case 'recall':
                       if(data.code == 0){
                           let index = findArrKey(im.state.msgList, data.msg_id, 'msg_id')
                           im.state.msgList.splice(index, 1)
                           if(data.msg_type === 'msg'){
                               recent = findTree(im.state.recentList, data.from_uid, 'from_uid')
                           }else if(data.msg_type === 'customerMsg'){
                               recent = findTree(im.state.recentList, data.from_uid, 'group_id')
                           }
                           recent.content = data.content
                           recent.time = data.time
                       }
                       break;
                   //聊天记录
                   case 'msgRecord':
                       const length = im.state.msgList.length
                       im.state.msgList = data.concat(im.state.msgList)
                       if (length == 0) {
                           scrollToBottom('chatMsgBox')
                       } else {
                           nextTick(() => {
                               state.scrollMsgLoading = false
                               setTimeout(()=>{
                                   const ref = findTree(msgRefs,state.scrollMsgId,'msgId')
                                   if(ref && state.chatMsgBox){
                                       const div = state.chatMsgBox.wrap
                                       const scrollHeight = div.scrollHeight

                                       const msgScrollTop = ref.dom.offsetTop
                                       const msgScrollHeight = msgScrollTop - 90;
                                       if(scrollHeight > msgScrollHeight){
                                           div.scrollTop = msgScrollHeight;
                                       }else{
                                           div.scrollTop = msgScrollTop
                                       }

                                   }
                               })
                           })
                       }
                       break;
               }
            })
            //聊天记录滚动
            nextTick(() => {
                const scrollbar = state.chatMsgBox.wrap
                scrollbar.onscroll = () => {
                    if (scrollbar.scrollTop == 0 && im.state.msgList.length > 0) {
                        state.scrollMsgLoading = true
                        state.scrollMsgId = im.state.msgList[0].msg_id
                        im.send('msgRecord',{
                            msg_type:im.state.recentType,
                            to_uid: im.state.recentId,
                            msg_id: im.state.msgList[0].msg_id,
                            size: im.state.recordSize,
                            date: null,
                            keyword: null,
                        })
                    }
                }
            })

            function getTypeContent(data){
                let content = ''
                switch (data.type) {
                    case 2:
                        content = '[图片]'
                        break
                    case 3:
                        content = '[语音]'
                        break
                    default:
                        content = data.content
                        break;
                }
                return content
            }
            //插入表情
            function sendEmoji(emoji){
                if (state.msgSelection && state.sendInput.innerHTML != '') {
                    var textNode = document.createTextNode(emoji);
                    state.msgSelection.insertNode(textNode);
                    state.msgSelection.setStartAfter(textNode);
                }else{
                    state.sendInput.focus()
                    document.execCommand("insertHTML",false,emoji)
                }
            }
            //换行并光标定位末尾
            function sendMsgWrap() {
                 //解决ff不获取焦点无法定位问题
                let range;
                state.sendInput.innerHTML += '<br><br>'
                if (window.getSelection) { //ie11 10 9 ff safari
                    state.sendInput.focus();
                    range = window.getSelection(); //创建range
                    range.selectAllChildren(state.sendInput); //range 选择obj下所有子内容
                    range.collapseToEnd(); //光标移至最后
                } else if (document.selection) { //ie10 9 8 7 6 5
                    range = document.selection.createRange(); //创建选择对象
                    range.moveToElementText(obj); //range定位到obj
                    range.collapse(false); //光标移至最后
                    range.select();
                }
            }
            //禁止textarea回车换行
            function enterClear(event) {
                event.preventDefault()
            }
            //保存光标位置
            function getCursorSelection(){
                state.msgSelection = window.getSelection().getRangeAt(0)
            }
            //发送内容改变
            function sendContentChange() {
                state.sendContent = state.sendInput.innerHTML
            }
            //粘贴图片处理
            function pasteSendInput(event) {

                let data = (event.clipboardData || window.clipboardData);
                let items = data.items;
                if (items && items.length) {
                    // 检索剪切板items
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf("image") !== -1) {
                            event.preventDefault()
                            uploadFile(items[i].getAsFile()).then(function(ret){
                                document.execCommand("insertHTML",false,"<img class='pasteImage' src='"+ret+"' width='150' height='80'/>")
                            }).catch(function(ret){});
                        }
                    }
                }
            }
            //上传
            function uploadFile(file) {
                var filename = file.name
                var index = filename.lastIndexOf('.')
                var suffix = filename.substring(index + 1, filename.length)
                filename = genId() + 'filerand.' + suffix
                return new Promise(function(resolve,reject){
                    const xhr = new XMLHttpRequest()
                    xhr.withCredentials = false
                    xhr.open('POST', '/eadmin/upload')

                    xhr.onload = function() {
                        var json
                        if (xhr.status != 200) {
                            ElMessage.error('上传失败')
                            return
                        }
                        try {
                            json = JSON.parse(xhr.responseText)
                            if (json.code !== 200) {
                                ElMessage.error('上传失败')
                                return
                            }
                            resolve(json.data)
                        } catch (e) {
                            ElMessage.error('上传失败')
                        }
                    }
                    const formData = new FormData()
                    formData.append('file', file, file.name)
                    formData.append('filename', filename)
                    xhr.setRequestHeader('Authorization', localStorage.getItem('eadmin_token'))
                    xhr.send(formData)
                })
            }
            /**
             * 消息通知
             * @param title 标题
             * @param content 内容
             * @param avatar 头像
             */
            function showNotification(title, content, avatar ) {
                if (window.Notification) {
                    if (window.Notification.permission == 'granted') {
                        const options = {
                            body: content,
                            requireInteraction: false,
                            icon: avatar
                        }
                        var notification = new Notification(title, options)
                        notification.onclick = function() {
                            // 可直接打开通知notification相关联的tab窗口
                            window.focus()
                        }
                    } else {
                        window.Notification.requestPermission()
                        ElNotification({
                            type:'info',
                            title: title,
                            message: content,
                        })
                    }
                } else {
                    ElNotification({
                        type:'info',
                        title: title,
                        message: content,
                    })
                }
            }
            //发送消息
            function sendMsg() {
                state.sendContent = state.sendInput.innerHTML.replace('<br>','\n')
                state.sendTipvisible = false
                if (!state.sendContent) {
                    state.sendTipvisible = true
                    setTimeout(() => {
                        state.sendTipvisible = false
                    }, 2000)
                    return false
                }
                /*查找图片出来单独发送图片*/
                var reg=/<img (class="pasteImage" src=".*?)" width="150" height="80">/g;

                var array = state.sendContent.split(reg)
                array.forEach(item=>{
                    if(item.indexOf('class="pasteImage" src="') > -1){
                        //发送图片
                        send(item.replace('class="pasteImage" src="',''),2)
                    }else if(item != ''){
                        //发送文字
                        send(item,1)
                    }
                })
                state.sendContent = ''
                state.sendInput.innerHTML = ''
                scrollToBottom('chatMsgBox')
            }
            //聊天记录滚动条置底
            function scrollToBottom(ref) {
                nextTick(() => {
                    let div = state[ref]
                    if(div){
                        div.wrap.scrollTop = div.wrap.scrollHeight
                    }
                })
            }
            /**
             * 发送信息
             * @param content 内容
             * @param msg_type 类型 msg customer
             * @param type 类型 1文字,2图片
             * @param ext 扩展消息
             */
            function send(content,type,ext = null){
                const msg_id = genId()
                let recent = findTree(im.state.recentList,im.state.recentId, 'from_uid')
                if(im.state.recentType == 'customerMsg'){
                    recent = findTree(im.state.recentList,im.state.recentId, 'group_id')
                }
                const data = {
                    action: recent.msg_type || 'msg',
                    data: {
                        msg_type:recent.msg_type || 'msg',
                        type: type,
                        customer_group:recent.customer_group || '',
                        from_uid: im.id,
                        msg_id: msg_id,
                        content: content,
                        to_uid: im.state.recentId,
                        ext: ext,
                    }
                }
                data.data.sendStatus = 'ing'
                im.state.msgList.push(data.data)
                sendWait(msg_id)
                im.send(data.action,data.data)
            }
            //信息发送中
            function sendWait(msg_id) {
                setTimeout(() => {
                    const key = findArrKey(im.state.msgList, msg_id, 'msg_id')
                    if(key != null){
                        if (im.state.msgList[key].sendStatus == 'ing') {
                            im.state.msgList[key].sendStatus = 'error'
                        }
                    }
                }, 5000)
            }

            //重发
            function resend(item, key) {
                im.state.msgList[key].sendStatus = 'ing'
                sendWait(item.msg_id)
                im.send('recall',{
                    action: 'msg',
                    data: item
                })
            }
            //撤回
            function recallMsg(item) {
                im.send('recall',{
                    type:item.msg_type,
                    to_uid: im.state.recentId,
                    recall_id: item.recall_id,
                })
            }
            //关闭文字菜单
            function popoverVisibleClose() {
                im.state.msgList.forEach(item => {
                    item.popoverVisible = false
                })
            }
            //右键打开文字菜单
            function openMenu(index) {
                popoverVisibleClose()
                im.state.msgList[index].popoverVisible = true
            }

            //判断本地是否已删除记录
            function isDelMsg(msg_id){
                let delMsg = localStorage.getItem('eadmin_del_msg'+im.id)
                if(delMsg){
                    delMsg = JSON.parse(delMsg)
                }else{
                    delMsg = []
                }
                return delMsg.indexOf(msg_id) > -1
            }
            //删除信息
            function delMsg(msg_id, index) {
                let delMsg = localStorage.getItem('eadmin_del_msg')
                if(delMsg){
                    delMsg = JSON.parse(delMsg)
                }else{
                    delMsg = []
                }
                delMsg.push(msg_id)
                localStorage.setItem('eadmin_del_msg'+im.id, JSON.stringify(delMsg))
                im.state.msgList.splice(index, 1)
            }

            function setRef (el,msgId){
                msgRefs.push({
                    msgId:msgId,
                    dom:el
                })
            }
            onBeforeUpdate(() => {
                msgRefs = []
            })
            // watch(()=>im.state.msgList,val=>{
            //     msgRefs = []
            // })
            return {
                setRef,
                im,
                ...toRefs(state),
                ...toRefs(im.state),
                sendMsgWrap,
                sendContentChange,
                enterClear,
                sendEmoji,
                getCursorSelection,
                pasteSendInput,
                sendMsg,
                isDelMsg,
                openMenu,
                delMsg,
                recallMsg,
                resend
            }
        }
    })
</script>

<style scoped>

    .header {
        height: 50px;
        text-indent: 20px;
        line-height: 50px;
        border-bottom: #dadcdf solid 1px;
        font-size: 18px;
        color: #000000;
    }
    .msgContentBox {
        height: 470px;
        position: relative;
        line-height: normal;
    }

    .msgTime {
        margin-top: 5px;
        text-align: center;
    }

    .sendBox {
        border-top: solid 1px #e9e7e6;
        background: #ffffff;
        height: 165px;
        overflow: hidden;
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

    .leftMsgItemBg {
        border-radius: 5px;
        background: #f0f0f0;
        padding: 10px 10px;
        font-size: 14px;
        white-space: pre-line;
    }
    .tools {
        display: flex;
        align-items: center;
        height: 30px;
    }

    .tools .item {
        margin: 0 10px;
        color: #999999;
        font-size: 18px;
        cursor: pointer;
    }
    .emoji{
        cursor: pointer;
        margin: 5px;
    }
    .sendBox {
        border-top: solid 1px #e9e7e6;
        background: #ffffff;
        height: 165px;
        overflow: hidden;
    }
    .sendTextarea {
        border: none;
        resize: none;
        padding-left: 10px;
        width: 100%;
        min-height: 105px;
    }
    .blue{
        color: #2d8cf0;
    }
    .red{
        color: red;
    }
    .msgImage{
        width: 120px; height: 100px;border-radius: 5px;border: 1px solid #ededed
    }
    .sendTextarea:focus{outline: none;}
</style>
