<template>
    <el-row :gutter="10" >
        <el-col :span="19">
            <el-card :body-style="{ padding: '0px 0px' }" shadow="always">
                <template #header>
                    <div class="logHeader">
                        <div>日志信息</div>
                        <div>
                            <el-input v-model="logKeyWord" placeholder="请输入关键内容" style="width: 150px" size="mini" clearable></el-input>
                            <el-date-picker
                                    value-format="yyyy-MM-dd HH:mm:ss"
                                    size="mini"
                                    v-model="dateKeyWord"
                                    type="datetimerange"
                                    range-separator="至"
                                    start-placeholder="开始时间"
                                    end-placeholder="结束时间">
                            </el-date-picker>
                            <el-button size="mini" @click="request" icon="el-icon-search" type="primary">搜索</el-button>
                            <el-button size="mini" @click="prev" v-if="logData.prev !== false">上一页</el-button>
                            <el-button size="mini" @click="next" v-if="logData.next !== false">下一页</el-button>
                        </div>
                    </div>
                </template>
                <pre class="code-view"><ol start="1"><li v-for="item in logData.list"><el-tag size="mini">{{item.time}}</el-tag> <el-tag size="mini" :type="item.type == 'error' ? 'danger':'info'">{{item.type}}</el-tag><br><br><div>{{item.msg}}</div><br></li></ol></pre>
            </el-card>
        </el-col>
        <el-col :span="5">
            <el-card :body-style="{ padding: '0px 0px' }" shadow="always">
                <template #header>
                    <div class="clearfix">
                        <span>日志文件</span>
                    </div>
                </template>
                <div>
                    <ul class="fileListBox">
                        <li v-for="item in logData.files" class="fileItem" @click="selectFile(item.path)" :style="{backgroundColor:(logData.file == item.path?'#ededed':'')}">
                            <div style="flex:1;">
                                <span style="color: #000;font-weight: bold">{{item.file_name}}</span><br>{{item.size}}
                            </div>
                            <div style="text-align: right">
                                <span style="color: #999999">{{item.update_time}}</span><br>
                                <div @click.stop>
                                    <el-popconfirm
                                            title="确认删除？"
                                            @confirm="del(item.path)"
                                    >
                                        <template #reference>
                                            <el-button size="mini" type="danger">删除</el-button>
                                        </template>
                                    </el-popconfirm>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </el-card>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        name: "EadminLog",
        data(){
            return {
                dateKeyWord:'',
                logKeyWord:'',
                logData:{
                    file:'',
                    list:[],
                },
                offset:0,
            }
        },
        created() {
            this.request()
        },
        methods:{
            request(){
                this.$request({
                    url:'log/logData',
                    method:'post',
                    data:{
                        file:this.logData.file,
                        offset:this.offset,
                        limit:10,
                        content:this.logKeyWord,
                        log_time:this.dateKeyWord,
                        page:this.logData.page
                    }
                }).then(res=>{
                    this.logData = res.data
                })
            },
            selectFile(file){
                this.logData.file = file
                this.request()
            },
            prev(){
                this.offset = this.logData.prev
                this.request()
            },
            next(){
                this.offset = this.logData.next
                this.request()
            },
            del(path){
                this.$request({
                    url:'log/remove',
                    method:'post',
                    data:{
                        path : path
                    }
                })
            },
        }
    }
</script>

<style scoped>
    .logHeader{
        display: flex;
        justify-content: space-between
    }
    .code-view {
        display: block;
        padding: 0;
        border-left-width: 6px;
        background-color: #F2F2F2;
        color: #333;
        margin: 0;
        font-family: Courier New;
    }
    .code-view ol {
        padding: 0;
        margin: 0;
        display: block;
        list-style-type: decimal;
        margin-block-end: 0;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
        padding-inline-start: 40px;
        overflow-x: auto;
    }
    .code-view li{
        position: relative;

        padding: 0 5px;
        border-left: 1px solid #e2e2e2;
        list-style-type: decimal-leading-zero;
        font-size: 12px;
        background-color: #fff;


    }
    .fileListBox{
        padding:0;margin:0;list-style-type:none;overflow: auto
    }
    .fileItem {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        line-height: 30px;
        cursor: pointer;
        border-bottom: 1px solid #ededed;
        padding: 5px 10px;
        margin: 0;
    }
    .fileItem:hover{
        background-color: #ededed;
    }
</style>
