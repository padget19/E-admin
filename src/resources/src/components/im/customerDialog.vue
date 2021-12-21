<template>
    <el-dialog v-model="customerDialogVisible" title="转接">
        <el-tabs v-model="tabCustomerName">
            <el-tab-pane label="客服" name="customer">
                <el-row class="customerList">
                    <el-col :span="12" :class="['customerItem',selectCustomerId==item.id?'activate':'']" v-for="item in customerList" @click.native="selectCustomer(item.id)">
                        <div>
                            <el-badge is-dot :type="item.online ? 'success':'info'">
                                <el-avatar style="margin-left: 10px;"
                                           :src="item.avatar"></el-avatar>
                            </el-badge>
                        </div>
                        <div style="flex:1;margin-left: 10px;">
                            <div class="name">{{item.nickname}}</div>
                        </div>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <el-tab-pane label="分组" name="customerGroup">
                <el-row class="customerList">
                    <el-col :span="12" :class="['customerItem',selectCustomerId==item.id?'activate':'']" v-for="item in customerList" @click.native="selectCustomer(item.id)">
                        <div>
                            <el-avatar style="margin-left: 10px;"
                                       :src="item.avatar"></el-avatar>
                        </div>
                        <div style="flex:1;margin-left: 10px;">
                            <div class="name">{{item.nickname}}</div>
                        </div>
                    </el-col>
                </el-row>
            </el-tab-pane>
        </el-tabs>
        <template #footer>
            <el-button size="small" @click="customerDialogVisible = false">取消</el-button>
            <el-button size="small" type="primary" @click="customerTransfer">转接</el-button>
        </template>
    </el-dialog>
</template>

<script>
    import customer from './customer/customer'
    export default {
        //选择客服弹窗
        name: "customerDialog",
        setup(){
            return customer
        }
    }
</script>

<style scoped>
    .customerItem {
        height: 60px;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    .customerList .activate {
        background: #1ba8ed;
        color: #ffffff;
    }
    .customerItem:hover {
        background: #1ba8ed;
        cursor: pointer;
        color: #ffffff;
    }
</style>
