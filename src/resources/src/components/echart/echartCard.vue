<template>
    <el-card shadow="hover">
        <template #header v-if="!hideTools || title">
            <el-row style="display: flex;align-items: center">
                <el-col :xs="24" :sm="24" :md="4" :span="4">
                    <div>{{title}}</div>
                </el-col>
                <el-col :xs="24" :sm="24" :md="20" :span="20" style="text-align: right" v-if="!hideTools">
                    <el-button-group >
                        <el-button v-if="params.date_type == 'yesterday'" size="small" type="primary" @click="requestData('yesterday')">
                          {{ trans('echartCard.yesterday') }}</el-button>
                        <el-button v-else plain size="small" @click="requestData('yesterday')">{{ trans('echartCard.yesterday') }}</el-button>

                        <el-button v-if="params.date_type == 'today'" size="small" type="primary" @click="requestData('today')">{{ trans('echartCard.today') }}</el-button>
                        <el-button v-else plain size="small" @click="requestData('today')">{{ trans('echartCard.today') }}</el-button>

                        <el-button v-if="params.date_type == 'week'" size="small" type="primary" @click="requestData('week')">{{ trans('echartCard.week') }}</el-button>
                        <el-button v-else plain size="small" @click="requestData('week')">{{ trans('echartCard.week') }}</el-button>

                        <el-button v-if="params.date_type == 'month'" size="small" type="primary" @click="requestData('month')">{{ trans('echartCard.month') }}</el-button>
                        <el-button v-else plain size="small" @click="requestData('month')">{{ trans('echartCard.month') }}</el-button>

                        <el-button v-if="params.date_type == 'year'" size="small" type="primary" @click="requestData('year')">{{ trans('echartCard.year') }}</el-button>
                        <el-button v-else plain size="small" @click="requestData('year')">{{ trans('echartCard.year') }}</el-button>
                        <el-date-picker
                                size="small"
                                v-model="rangeDate"
                                type="daterange"
                                :range-separator="trans('echartCard.to')"
                                :start-placeholder="trans('echartCard.startDate')"
                                :end-placeholder="trans('echartCard.endDate')"
                        >
                        </el-date-picker>
                    </el-button-group>
                </el-col>
            </el-row>
        </template>
        <render :data="header" v-if="header"></render>
        <render :data="filter" v-if="filter"></render>
        <render :data="chart" v-loading="loading"></render>
        <render :data="footer" v-if="footer"></render>
    </el-card>
</template>

<script>
    import {trans} from '@/utils'
    import {defineComponent, ref, reactive, watch} from 'vue'
    import {useHttp} from '@/hooks'
    import dayjs from "dayjs";
    import {useRoute} from "vue-router";
    export default defineComponent({
        name:'EadminEchartCard',
        props:{
            echart:Object,
            params:Object,
            title:String,
            header: [Object, Boolean],
            footer: [Object, Boolean],
            filter: [Object, Boolean],
            hideTools: Boolean,
            modelValue: Boolean,
            filterField:String,
            proxyData:Object,
            rangeDate:{
              type:Array,
              default:[]
            },
        },
        emits: ['update:modelValue'],
        setup(props,ctx){
            watch(() => props.modelValue, (value) => {
                if(value){
                    requestData(params.date_type)
                }
            })
            const route = useRoute()
            const proxyData = props.proxyData
            const {loading,http} = useHttp()
            const params = reactive(Object.assign({
                date_type:'today'
            },route.query,props.params))
            const chart = ref(props.echart)
            const header = ref(props.header)
            const footer = ref(props.footer)
            const rangeDate = ref(props.rangeDate)
            watch(rangeDate,(value)=>{
                if(value == null){
                    requestData('today')
                }else{
                    params.start_date =  dayjs(value[0]).format('YYYY-MM-DD')
                    params.end_date =  dayjs(value[1]).format('YYYY-MM-DD')
                    requestData('range')
                }
            })
            function requestData(type){
                params.date_type = type
                params.ajax = true
                http({
                    url: '/eadmin.rest',
                    params: Object.assign(proxyData[props.filterField] || {},route.query,params)
                }).then(res=>{
                    header.value = res.header
                    chart.value = res.content
                    footer.value = res.footer
                }).finally(() => {
                    ctx.emit('update:modelValue', false)
                })
            }
            return {
                trans,
                footer,
                params,
                rangeDate,
                loading,
                chart,
                header,
                requestData
            }
        }
    })
</script>

<style scoped>

</style>
