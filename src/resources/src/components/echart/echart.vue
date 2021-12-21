<template>
  <div ref='echart' :style="{height:height,width:width}" />
</template>

<script>
import * as echarts from 'echarts'
import {defineComponent,ref,onUpdated,onMounted,onBeforeUnmount,nextTick,onActivated} from 'vue'

export default defineComponent({
  name: 'EadminChart',
  props: {
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '350px'
    },
    options: {
      type: Object,
      required: true
    }
  },
  setup(props){
    const echart = ref('')
    let chart = null
    function initChart() {
      setTimeout(()=>{
        if(!chart){
          chart = echarts.init(echart.value)
        }
        chart.setOption(props.options)
        chart.resize()
      },10)
    }
    window.addEventListener("resize", () => {
      if(chart){
        chart.resize()
      }
    })
    onMounted(()=>{
      initChart()
    })
    onUpdated(()=>{
      initChart()
    })
    onBeforeUnmount(()=>{
      if(chart){
        chart.dispose()
        chart = null
      }
    })
    return {
      echart
    }
  }
})
</script>
