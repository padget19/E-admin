<template>
    <el-dropdown placement="bottom" trigger="click" @visible-change="noticeShow" :style="['line-height: 1',noticeCount > 0 ? 'margin-right: 5px':'']">
        <div class="right-menu-item hover-effect">
            <i class="el-icon-bell hover-effect" style="font-size: 16px" v-if="noticeCount === 0"/>
            <el-badge :value="noticeCount" :max="99" type="danger" v-else>
                <i class="el-icon-bell hover-effect" style="font-size: 16px"/>
            </el-badge>
        </div>
        <template #dropdown>
            <el-dropdown-menu class="user-dropdown" >
                <div v-infinite-scroll="noticeListsRoll" class="noticeListBox" :infinite-scroll-immediate="true">
                    <el-dropdown-item v-for="item in list" @click.native="readNotice(item.id,item.target_url)">
                        <div class="noticeBox">
                            <el-avatar v-if="item.type == 2" size="medium" :src="item.avatar"/>
                            <div v-else class="avatar" :style="{background:item.color}">
                                <i :class="item.avatar" style="margin-right:0px"/>
                            </div>
                            <el-tooltip :open-delay="1500" effect="dark" :content="item.content" placement="top-start">
                                <div class="content">
                                    <div v-if="item.is_read == 1" class="title">{{ item.content }}</div>
                                    <el-badge v-else is-dot type="danger">
                                        <div class="title">{{ item.content }}</div>
                                    </el-badge>
                                    <div class="time">
                                        {{ item.create_time }}
                                        <el-tag class="tag" :style="{background:item.color,borderColor:item.color}"
                                                effect="dark">{{ item.title }}
                                        </el-tag>
                                    </div>
                                </div>
                            </el-tooltip>
                        </div>
                    </el-dropdown-item>
                </div>
                <div class="noticeClear">
                    <span v-if="list.length == 0">暂无通知</span>
                    <el-popconfirm v-else icon-color="red" title="清空将无法恢复，确认清空？" @confirm="noticeClear">
                        <template #reference>
                            <span>清空通知</span>
                        </template>
                    </el-popconfirm>
                </div>
            </el-dropdown-menu>
        </template>
    </el-dropdown>
    <audio id="eadmin_notice_music" controls="controls" style="display:none">
        <source src="../assets/notice.mp3" type="audio/mpeg">
    </audio>
</template>

<script>
    import {defineComponent, ref} from 'vue'
    import request from '@/utils/axios'
    import {link} from '@/utils'
    import { ElNotification } from 'element-plus'
    export default defineComponent({
        name: "notice",
        setup() {
            const list = ref([])
            const noticeCount = ref(0)
            let page = 1
            const size = 10
            receiveNotification()

            // 获取系统通知列表
            function noticeList() {
                request({
                    url: 'notice/system',
                    method: 'post',
                    data: {
                        page: page,
                        size: size
                    }
                }).then(res => {
                    list.value = page === 1 ? res.data : [...list.value, ...res.data]
                })
            }

            // 清空通知
            function noticeClear() {
                request({
                    url: 'notice/clear',
                    method: 'delete'
                }).then(res => {
                    list.value = []
                    noticeCount.value = 0
                })
            }

            // 点击加载系统通知
            function noticeShow(show) {
                if (show) {
                    list.value = []
                    page = 1
                    noticeList()
                }
            }

            // 触底加载系统通知分页
            function noticeListsRoll() {
                if (list.value.length == (page * size)) {
                    page++
                    noticeList()
                }
            }

            // 读取通知
            function readNotice(id, url) {
                request({
                    url: 'admin/notice/reads',
                    method: 'post',
                    data: {
                        id: id
                    }
                }).then(res => {
                    noticeCount.value = res.data
                })
                if (url) {
                    link(url)
                }
            }

            function receiveNotification() {
                request('notice/notification').then(res => {
                    if (res.data) {
                        noticeCount.value = res.data.count
                        if(res.data.list){
                            res.data.list.forEach(item => {
                                showNotification(item.title, item.content, item.type, item.avatar, item.url)
                            })
                            document.getElementById('eadmin_notice_music').play()
                        }
                    }
                })
                setTimeout(function () {
                    receiveNotification()
                }, 15000)
            }

            function showNotification(title, content, type, avatar, url) {
                if (window.Notification && window.Notification.permission == 'granted') {
                    const options = {
                        body: content,
                        requireInteraction: false //
                    }
                    if (type == 2) {
                        options.icon = avatar
                    }
                    if (link) {
                        options.link = url
                    }
                    var notification = new Notification(title, options)
                    notification.onclick = function () {
                        // 可直接打开通知notification相关联的tab窗口
                        window.focus()
                        link(url)
                    }
                } else {
                    ElNotification({
                        type:'info',
                        title: title,
                        dangerouslyUseHTMLString: true,
                        message: content,
                        position: 'bottom-right'
                    })
                }
            }
            return {
                noticeCount,
                list,
                readNotice,
                noticeListsRoll,
                noticeShow,
                noticeClear
            }
        }
    })
</script>

<style lang="scss" scoped>
    @import '../styles/element-variables.scss';
    .right-menu-item {

        display: inline-block;
        padding: 0 7px;
        vertical-align: text-bottom;

        &.hover-effect {
            padding: 20px 7px;
            cursor: pointer;
            transition: background .3s;

            &:hover {
                background-color: #f9f9f9;
            }
        }
    }
    .noticeListBox{
        max-height: 490px;
        width: 350px;
        overflow:auto
    }
    .noticeClear{
        text-align: center;
        font-size: 14px;
        cursor: pointer;
        padding-top:6px;
        color: #98a6ad;
        &:hover{
            color:$--color-primary;
        }
    }
    .noticeBox{
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e8eaec;
        padding: 12px 0px;
        width: 300px;
        .avatar{
            width: 36px;
            height: 36px;
            line-height: 36px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: $--color-primary;
            i{
                color: #ffffff;
            }
        }
        .content{
            display: flex;
            flex: 1;
            flex-direction: column;
            justify-content: left;
            line-height: 22px;
            margin-left: 10px;

            .title{
                width: 250px;
                font-size: 14px;
                font-weight: 400;
                color: #98a6ad;
                white-space:nowrap;
                overflow:hidden;
                text-overflow:ellipsis;
            }
            .time{
                font-size: 12px;
                color: #98a6ad;
                .tag{
                    height: 20px;padding: 0 5px;line-height: 19px;
                }
            }
        }

    }
</style>
