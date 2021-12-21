import { reactive, toRefs,watch} from "vue";
import {findArrKey, findTree, genId} from "../../../utils";
import im from '../websocket/websocket'
import {ElMessage} from "element-plus";
const state = reactive({
    //客服列表
    customerList:[],
    //当前选择客服id
    selectCustomerId: 0,

    tabCustomerName:'customer',
    //列表
    customerConnList: [],
    //新朋友搜索关键字
    searchFriendKey:'',
    //当前选择转接用户
    selectTransferData:null,

})
export const customerDialogVisible = state.customerDialogVisible
im.onMessage((action,data)=>{
    switch (action) {
        //获取待接入用户
        case 'customerConnList':
            state.customerConnList = data
            break;
        //获取客服列表
        case 'getCustomerList':
            state.customerList = data
            break;
        //获取客服分组列表
        case 'getCustomerGroupList':
            state.customerList = data
            break;
    }
})
watch(()=>im.state.customerDialogVisible,function (val) {
    if(val){
        getCustomerList(state.tabCustomerName)
    }
})
watch(()=>state.tabCustomerName,value=>{
    getCustomerList(value)
})
//选择客服
function selectCustomer(id){
    state.selectCustomerId = id
}
//获取客服列表
function getCustomerList(value) {
    state.selectCustomerId = null
    if(value == 'customer'){
        im.send('getCustomerList')
    }else{
        im.send('getCustomerGroupList')
    }
}
//待接入的转接客服
function customerItemTransfer(data,type){
    im.send('customerTransfer',{
        transferType:type,
        msg_type:'customerMsg',
        type: 1,
        customer_group:data.customer_group,
        from_uid: im.id,
        msg_id: genId(),
        to_uid: data.group_id,
        ext: '',
        transfer_uid:state.selectCustomerId,
        recent_id:''
    })

    im.state.customerDialogVisible = false
    if(type === 'join'){
        im.state.leftTool = 'message'
    }
}
//选择
function selectTransfer(data){
    state.selectTransferData = data
    im.state.customerDialogVisible = true
}
//转接客服
function customerTransfer(){
    if(!state.selectCustomerId){
        return ElMessage.error('请选择客服')
    }
    let transferType = 'transfer'
    if(state.tabCustomerName == 'customerGroup'){
        transferType = 'transferGroup'
    }
    if(state.selectTransferData){
        return customerItemTransfer(state.selectTransferData,transferType)
    }
    const index = findArrKey(im.state.recentList, im.state.recentId, 'group_id')
    const recent_id = im.state.recentList[index].recent_id
    im.send('customerTransfer',{
        transferType:transferType,
        msg_type:'customerMsg',
        type: 1,
        customer_group:im.state.recentList[index].customer_group,
        from_uid: im.id,
        msg_id: genId(),
        to_uid: im.state.recentId,
        ext: '',
        transfer_uid:state.selectCustomerId,
        recent_id:recent_id
    })
    im.state.recentList.splice(index,1)
    im.state.customerDialogVisible = false
    im.state.msgList = []
}
//关闭会话
export function closeCustomer(groupId) {
    const msg_id = genId()
    let recent = findTree(im.state.recentList,im.state.recentId, 'from_uid')
    if(im.state.recentType == 'customerMsg'){
        recent = findTree(im.state.recentList,im.state.recentId, 'group_id')
    }
    const data = {
        action: recent.msg_type || 'msg',
        data: {
            msg_type:recent.msg_type || 'msg',
            type: 1,
            customer_group:recent.customer_group || '',
            from_uid: im.id,
            msg_id: msg_id,
            content: '会话已结束',
            to_uid: im.state.recentId,
            ext: '',
        }
    }
    data.data.sendStatus = 'ing'
    im.state.msgList = []
    im.state.recentType = 'msg'
    im.send(data.action,data.data)
    const index = findArrKey(im.state.recentList, groupId, 'group_id')
    const recent_id = im.state.recentList[index].recent_id || ''
    im.state.recentList.splice(index,1)

    setTimeout(()=>{
        im.send('removeRecent', {
            recent_id:recent_id,
        })
        im.state.recentId = -1
    },1000)
}
export default {
    selectCustomer,
    customerTransfer,
    selectTransfer,
    customerItemTransfer,
    state,
    ...toRefs(state),
    ...toRefs(im.state),
}
