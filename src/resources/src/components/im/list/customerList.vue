<template>
    <div>
        <div class="friendMsgItem" v-for="(item,index) in list">
            <div>
                <el-avatar style="margin-left: 10px;" shape="square"
                           :src="item.avatar"></el-avatar>
            </div>
            <div style="flex:1;margin-left: 10px;">
                <div class="name" style="display: flex;justify-content: space-between">
                    <div>{{item.nickname}}</div>
                    <div>
                        <el-tooltip effect="light" content="接入">
                            <i @click="customerItemTransfer(item,'join')" class="el-icon-phone rightTools"></i>
                        </el-tooltip>
                        <el-tooltip effect="light" content="转接">
                            <i @click="selectTransfer(item)" class="el-icon-refresh rightTools"></i>
                        </el-tooltip>
                    </div>
                </div>
                <div class="content">发起: {{item.create_time}}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs,computed} from "vue";
    import customer from '../customer/customer'
    export default defineComponent({
        name: "ImCustomerList",
        props: {
            search:String,
        },
        setup(props) {
            const list = computed(()=> {
                if (customer.state.customerConnList.length > 0) {
                    return customer.state.customerConnList.filter(item => {
                        return item.nickname.indexOf(props.search) >= 0
                    })
                }
            })
            return {
                list,
                ...customer
            }
        }
    })
</script>

<style scoped>
    .friendMsgItem .name {
        font-size: 14px;
        margin-bottom: 4px;
        color: #000000
    }
    .friendMsgItem .content {
        font-size: 14px;
        color: #666;
        width: 140px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .friendMsgItem {
        height: 60px;
        display: flex;
        align-items: center;
        border-bottom: solid 1px #dadcdf;
    }
    .friendMsgItem:hover {
        background: #dedcda;
        cursor: pointer;
    }


    .rightTools {
        margin: 0 5px;
    }
</style>
