<template>
    <el-popover
            placement="top-start"
            :width="500"
            trigger="hover"
    >
        <template #reference>
            <div style="text-align: center">{{title}}</div>
        </template>
        <pre>{{queue}}</pre>
    </el-popover>
    <div class="queueProgress">
        <el-progress :text-inside="true" :stroke-width="15" :percentage="progress" :indeterminate="true"
                     :status="status"></el-progress>
    </div>
    <div class="queueBack">
        <el-scrollbar style="height: 300px">
            <div class="text" v-for="item in history" v-html="item.message"></div>
        </el-scrollbar>
    </div>
</template>

<script>
    export default {
        name: "queue",
        data() {
            return {
                timer: null,
                progress: 0,
                history: [],
                status: ''
            }
        },
        props: {
            id: [String, Number],
            title: String,
            queue:String,
        },
        mounted() {
            this.progressHandel()
            this.timer = setInterval(() => {
                this.progressHandel()
            }, 1000)
        },
        unmounted() {
            clearInterval(this.timer)
        },
        methods: {
            progressHandel() {
                this.$request({
                    url: 'queue/progress',
                    params: {
                        id: this.id
                    }
                }).then(res => {
                    this.history = res.data.history
                    this.progress = res.data.progress
                    if (res.data.status == 3) {
                        this.status = 'success'
                    }
                    if (res.data.status == 4) {
                        this.status = 'exception'
                    }
                    if (res.data.status == 3 || res.data.status == 4) {
                        clearInterval(this.timer)
                    }
                })
            }
        }
    }
</script>

<style scoped>
    .queueProgress {
        margin-bottom: 10px;
    }
    .queueBack {
        background: #000000;
        padding: 10px 10px;
        border-radius: 5px;
    }

    .queueBack .text {
        line-height: 25px;
        white-space: pre-line;
        color: #ffffff;
    }
</style>
