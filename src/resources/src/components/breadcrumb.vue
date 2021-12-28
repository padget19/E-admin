<template>
    <el-breadcrumb separator-class="el-icon-arrow-right" class="breadcrumb">
        <el-breadcrumb-item v-for="item in state.breadcrumb">{{item.name}}</el-breadcrumb-item>
    </el-breadcrumb>
</template>

<script>
    import { store } from '@/store'
    import { defineComponent,inject} from 'vue'
    export default defineComponent({
        name: "EadminBreadcrumb",
        setup(){
            const state = inject(store)
            return {
                state
            }
        }
    })
</script>

<style scoped>
    .breadcrumb{
        white-space: nowrap;
    }
    .breadcrumb /deep/ .el-breadcrumb__separator{
       color: #ffffff !important;
    }
    .breadcrumb /deep/ .el-breadcrumb__inner{
       color: #ffffff !important;
    }
</style>
