//连接通讯
import {ElMessage} from "element-plus";
import { reactive ,watchEffect} from "vue";
const im = {
    //当前用户id
    id:0,
    info:{
        nickname:'',
        avatar:'',
    },
    state :reactive({
        //左侧工具栏
        leftTool:'message',
        //好友
        friendList:[],
        //会话
        recentList:[],
        //会话类型
        recentType:'msg',
        //当前会话id
        recentId:-1,
        //当前会话标题
        recentTitle:'',
        //聊天记录分页大小
        recordSize: 20,
        //当前会话内容
        msgList:[],
        //未读消息数量
        unReadNum:0,
        //选择客服弹窗dialog
        customerDialogVisible:false
    }),
    webSocket:null,
    //心跳定时器
    pongHealthTimer: null,
    //监听回调
    listens:[],
    //监听关闭
    closes:[],
    connect: function (websocket,username,password) {
        this.webSocket = new WebSocket(websocket + "/?username="+username+"&password="+password);
        this.webSocket.onmessage = e=>{
            const receive = JSON.parse(e.data)
            const action = receive.action
            const data = receive.data
            if(action === 'login'){
                this.id = data.info.id
                this.pongHealthTimer = setInterval(()=>{
                    this.send('ping')
                },30000)
            }
            this.listens.forEach(callback=>{
                callback(action,data)
            })
        }
        this.webSocket.onclose = (e) => {
            this.closes.forEach(callback=>{
                callback(e)
            })
            clearInterval(this.pongHealthTimer)
            if(e.code != 1005){
                ElMessage.error('客服通讯连接失败')
                setTimeout(() => {
                    this.connect(websocket ,username,password)
                }, 3000)
            }
        }
    },
    //关闭
    close:function(){
      this.webSocket.close()
      this.webSocket = null
      this.pongHealthTimer= null;
      this.listens= []
      this.closes= []
    },
    //是否当前选中对象
    isSelectUser(item){
        if(item.msg_type === 'msg'){
            return this.state.recentId == item.from_uid
        }else if(item.msg_type === 'customerMsg'){
            return this.state.recentId == item.group_id
        }
        return false
    },
    //选择会话
    selectUser(item,index){
        this.state.msgList = []
        this.state.leftTool = 'message'
        this.state.recentTitle = item.msg_type === 'customerMsg' ? item.user_nickname : item.nickname
        this.state.recentType = item.msg_type
        if(item.msg_type === 'msg'){
            this.state.recentId = item.from_uid
        }else if(item.msg_type === 'customerMsg'){
            this.state.recentId = item.group_id
        }
        this.state.recentList[index].unReadNum = 0
        this.readMsg(item)
        this.send('msgRecord',{
            msg_type:this.state.recentType,
            to_uid:  this.state.recentId,
            msg_id: null,
            size: this.state.recordSize,
            date: null,
            keyword: null,
        })
    },
    //读消息
     readMsg:function(item){
        let readAction
        if(item.msg_type === 'msg'){
            im.state.recentId = item.from_uid
            readAction = 'readMsg'
        }else if(item.msg_type === 'customerMsg'){
            im.state.recentId = item.group_id
            readAction = 'readGroupMsg'
        }
        im.send(readAction,{
            uid:  im.state.recentId
        })
    },
    //发送
    send: function (action, data = []) {
        this.webSocket.send(JSON.stringify({
            action: action,
            data: data
        }))
    },
    //添加监听接收
    onMessage:function (callback) {
        const length =  this.listens.push(callback)
        return length - 1
    },
    //添加监听回调
    onClose:function (callback) {
        const length =  this.closes.push(callback)
        return length - 1
    },
    //移除监听
    removeMessage(index){
        this.listens.splice(index,1)
    }
}
watchEffect(()=>{
    im.state.unReadNum = 0
    im.state.recentList.forEach(item => {
        im.state.unReadNum  += parseInt(item.unReadNum)
    })
})
export default im


