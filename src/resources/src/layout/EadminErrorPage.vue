<template>
    <el-dialog
            v-model="visable"
            width="80%"
            :fullscreen="true"
            :before-close="close"
    >
        <div v-if="errorData != null" class="main">
            <div v-for="item in errorData.traces">
                <h1 style="color: #000000;">{{ item.message }}</h1>
                <div style="color: rgba(30, 20, 70, 0.5)">
                    <b>错误异常:</b> {{ item.name }}&nbsp;<el-tag type="info" size="mini">{{ item.code }}</el-tag>
                    <p>
                        <b>错误文件:</b> {{ item.file }}
                        <el-tag size="mini">{{ item.line }} 行</el-tag>
                    </p>
                </div>
                <div class="source-code">
                    <pre><ol :start="item.source.first"><li v-for="(sourceItem,key) in item.source.source" :style="{backgroundColor:(item.source.first+key == item.line? '#f8cbcb':'')}"><code>{{ sourceItem }}</code></li></ol></pre>
                </div>
                <div class="exception_card">
                    <el-tabs>
                        <el-tab-pane label="异常跟踪">

                            <el-container>
                                <div class="exception">
                                    <ul>
                                        <li v-for="(trace,index) in item.trace" @click="selectTrace=index" :class="selectTrace === index?'active':''">
                                            <div v-if="trace.file" class="title">{{trace.file}}</div>
                                            <div class="desc">{{trace.class}}</div>
                                        </li>
                                    </ul>
                                </div>
                                <el-main>
                                    <div>
                                        <b v-if="item.trace[selectTrace].class">{{ getClass(item.trace[selectTrace].class) }}{{ item.trace[selectTrace].type }}{{ item.trace[selectTrace].function }}</b>
                                        <p v-if="item.trace[selectTrace].file">
                                            <b>错误文件:</b> {{ item.trace[selectTrace].file }}
                                            <el-tag size="mini">{{ item.trace[selectTrace].line }} 行</el-tag>
                                        </p>
                                    </div>
                                    <div class="source-code">
                                        <pre><ol :start="item.trace[selectTrace].source.first"><li v-for="(sourceItem,key) in item.trace[selectTrace].source.source" :style="{backgroundColor:(item.trace[selectTrace].source.first+key == item.trace[selectTrace].line? '#f8cbcb':'')}"><code>{{ sourceItem }}</code></li></ol></pre>
                                    </div>
                                </el-main>
                            </el-container>


                        </el-tab-pane>
                        <el-tab-pane label="Server/Request Data">
                            <div v-for="(item,key) in errorData.tables['Server/Request Data']"
                                 style="display: flex;line-height: 25px">
                                <b style="width: 250px">{{ key }}</b>
                                <div style="flex: 1;">{{ item }}</div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="POST Data">
                            <div v-for="(item,key) in errorData.tables['POST Data']" style="display: flex;line-height: 25px">
                                <b style="width: 250px">{{ key }}</b>
                                <div style="flex: 1;">{{ item }}</div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="GET Data">
                            <div v-for="(item,key) in errorData.tables['GET Data']" style="display: flex;line-height: 25px">
                                <b style="width: 250px">{{ key }}</b>
                                <div style="flex: 1;">{{ item }}</div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="Session">
                            <div v-for="(item,key) in errorData.tables['Session']" style="display: flex;line-height: 25px">
                                <b style="width: 250px">{{ key }}</b>
                                <div style="flex: 1;">{{ item }}</div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="Cookies">
                            <div v-for="(item,key) in errorData.tables['Cookies']" style="display: flex;line-height: 25px">
                                <b style="width: 250px">{{ key }}</b>
                                <div style="flex: 1;">{{ item }}</div>
                            </div>
                        </el-tab-pane>

                    </el-tabs>
                </div>

            </div>
        </div>
    </el-dialog>
</template>

<script>
    import {store, action} from '@/store'
    import {defineComponent, inject, computed, reactive, watch,ref} from 'vue'

    export default defineComponent({
        name: 'EadminErrorPage',
        setup() {
            const visable = ref(false)
            const selectTrace = ref(0)
            const state = inject(store)
            let errorData = computed(() => {
                return state.errorPage.data
            })
            watch(()=>state.errorPage.visable,(val)=>{
                visable.value = val
            })
            function close() {
                action.errorPageClose()
            }

            function getFile(str) {
                const index = str.lastIndexOf('/')
                return str.substring(index + 1, str.length)
            }

            function getClass(str) {
                const index = str.lastIndexOf('\\')
                return str.substring(index + 1, str.length)
            }

            return {
                selectTrace,
                visable,
                errorData,
                close,
                getFile,
                getClass
            }
        }
    })
</script>

<style scoped>
    .exception_card{
        margin-top: 10px;
    }
    .exception ul{
        list-style-type:none;
        overflow:auto;
        white-space:nowrap;
        width: 300px;
        height: 600px;
        padding: 0;
        margin: 0;
        border: 1px solid #cccccc;
        background: #ffffff;
    }
    .exception ul .title{
        line-height: 25px;
        font-weight: bold;
    }
    .exception ul .desc{
        line-height: 25px;
    }
    .exception li{
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        cursor: pointer;
        padding: 10px;
        border-bottom: 1px solid #cccccc;
    }
    .exception .active{
        background: #409eff;
        color: #ffffff;
    }
    .exception li:hover{
        background: #409eff;
        color: #ffffff;
    }
    ol {
        display: block;
        list-style-type: decimal;
        margin-block-start: 1em;
        margin-block-end: 1em;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
        padding-inline-start: 40px;
    }

    .trace ol li {
        padding: 2px 4px;
    }

    .trace {
        padding: 6px;
        border: 1px solid #ddd;
        border-top: 0 none;
        line-height: 16px;
        font-size: 14px;
        font-family: Consolas, "Liberation Mono", Courier, Verdana, "微软雅黑";
    }

    pre {
        display: block;
        font-family: monospace;
        white-space: pre;
        margin: 1em 0px;
    }

    .source-code pre {
        margin: 0;
    }

    .source-code {
        border: 1px solid #ddd;
        background: rgb(247, 247, 252);
        overflow-x: auto;
    }
    .source-code pre ol {
        margin: 0;
        color: rgba(30, 20, 70, 0.5);
        min-width: 100%;
        box-sizing: border-box;
        font-size: 14px;
        font-family: "Century Gothic", Consolas, "Liberation Mono", Courier, Verdana;
    }

    .source-code pre li {
        margin: 0;
        padding: 0;
        border-left: 1px solid #ddd;
        font-size: 14px;
        height: 22px;
        line-height: 22px;
        display: list-item;
        text-align: -webkit-match-parent;
        background: #ffffff;
    }

    .source-code pre code {
        color: #333;
        height: 100%;
        display: inline-block;
        border-left: 1px solid #fff;
        font-size: 12px;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
</style>
