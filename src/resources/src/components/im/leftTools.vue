<template>
    <div class="leftTools">
        <div class="item">
            <el-avatar size="medium" shape="square" :src="avatar"></el-avatar>
        </div>
        <div class="item" v-for="item in leftToos">
            <el-tooltip effect="light" :content="item.tip" placement="right">
                <el-badge type="danger" :value="item.num" :max="99" v-if="item.num > 0">
                    <i :class="item.icon" @click="selectTool(item.mark)"
                       :style="{color:(leftTool == item.mark?'#409EFF':'')}"></i>
                </el-badge>
                <i v-else :class="item.icon" @click="selectTool(item.mark)"
                   :style="{color:(leftTool == item.mark?'#409EFF':'')}"></i>
            </el-tooltip>
        </div>
    </div>
</template>

<script>
    import {defineComponent,reactive,toRefs,watch} from "vue";
    import im from "./websocket/websocket";

    export default defineComponent({
        name: "ImLeftTools",
        props:{
            modelValue:String,
            //头像
            avatar:String,
        },
        emits:['update:modelValue'],
        setup(props,ctx){

            const state = reactive({
                //工具栏
                leftToos: [
                    {
                        icon: 'el-icon-s-comment',
                        tip: '会话',
                        mark: 'message',
                        num:im.unReadNum,
                    },
                    {
                        icon: 'el-icon-user-solid',
                        tip: '通讯录',
                        mark: 'friend',
                        num:0,
                    },
                    {
                        icon: 'el-icon-place',
                        tip: '待接入',
                        mark: 'customer',
                        num:0,
                    },
                ],
            })
            im.onMessage((action,data)=>{
                switch (action) {
                    //登录成功
                    case 'login':
                        if(data.code === 0){
                            state.leftToos[1].num = data.init.unReadFriendNum
                            state.leftToos[2].num = data.init.customerConnNum
                        }
                        break;
                    //获取添加好友列表
                    case 'getAddFriend':
                        state.leftToos[1].num  = 0
                        break;
                    //获取待接入用户
                    case 'customerConnList':
                        state.leftToos[2].num = data.length
                        break;
                    //收到好友请求
                    case 'addFriend':
                        if(im.state.leftTool == 'friend'){
                            im.send('getAddFriend')
                        }
                        state.leftToos[1].num = data.num
                        break;
                    //成功添加好友
                    case 'passFriend':
                        state.leftToos[1].num  = data.num
                        break;
                }
            })
            function selectTool(tool) {
                im.state.leftTool = tool
            }
            return {
                selectTool,
                ...toRefs(state),
                ...toRefs(im.state)
            }
        }
    })
</script>

<style scoped>

    .leftTools {
        background-color: #1a1a1a;
        width: 70px;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .leftTools .item {
        margin-top: 20px;
    }

    .leftTools .item i {
        font-size: 22px;
        cursor: pointer;
    }
</style>
