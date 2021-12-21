<template>
  <div>
    <el-input v-model="mapAddress">
      <template #append><el-button icon="el-icon-map-location" @click="drawer = true">地图</el-button></template>
    </el-input>
    <el-drawer
      custom-class="el-drawers"
      :append-to-body="true"
      v-model="drawer"
      direction="ttb"
      size="50%"
      :destroy-on-close="true"
      :with-header="false"
      @opened="init"
    >
      <div ref="container" style="width: 100%;height: 100%" />
      <div style="position: absolute;left: 80px;top: 10px">
        <el-card :body-style="{ padding: '10px'}">
          <el-input id="tipinput" v-model="searchText" placeholder="请输入关键字" size="mini" prefix-icon="el-icon-search" />
        </el-card>
      </div>
      <div style="position: absolute;right: 10px;bottom: 10px">
        <el-card :body-style="{ padding: '10px',}">
          <div class="info"><b>当前选择地址: </b>{{ mapAddress }}</div>
        </el-card>
      </div>
    </el-drawer>
  </div>
</template>
<script>
import {defineComponent, ref,watch,onMounted} from "vue";
export default defineComponent({
  name: 'EadminAmap',
  props: {
    modelValue: String,
    lat: [Number, String],
    lng: [Number, String],
    apiKey: {
      type: String,
      default: ''
    }
  },
  emits:['update:modelValue','update:lat','update:lng'],
  setup(props,ctx){
    const container = ref('')
    const searchText = ref('')
    const drawer = ref(false)
    const mapAddress = ref(props.modelValue)

    function loadScript(url, id) {
      const jsapi = document.createElement('script')
      jsapi.charset = 'utf-8'
      jsapi.id = id
      jsapi.src = url
      document.head.appendChild(jsapi)
    }
    onMounted(()=>{
      if (document.getElementById('amapscript') == null) {
        const url = 'https://webapi.amap.com/maps?v=1.4.15&key=' + props.apiKey + '&plugin=AMap.Autocomplete,AMap.PlaceSearch,AMap.Geocoder,AMap.Geolocation'
        loadScript(url, 'amapscript')
      }
    })
    watch(mapAddress,(val)=>{
      ctx.emit('update:modelValue',val)
    })

    let map = null
    let geolocation = null
    let placeSearch = null
    let marker = null
    let geocoder = null

    let mapLng = props.lng
    let mapLat = props.lat
    function onComplete(data) {
      // data是具体的定位信息
      mapLat = data.position.lat
      mapLng = data.position.lng
      ctx.emit('update:modelValue',data.formattedAddress)
      ctx.emit('update:lat',mapLat)
      ctx.emit('update:lng',mapLng)
      position()
    }
    function position() {
      map.setCenter([mapLng, mapLat])
      marker = new AMap.Marker({
        position: [mapLng, mapLat],
        map: map
      })
      const info = []
      info.push("<div class='input-card content-window-card'> ")
      info.push("<p class='input-item'>" + props.modelValue + '</p></div></div>')
      const infoWindow = new AMap.InfoWindow({
        content: info.join(''),
        offset: new AMap.Pixel(0, -35)
      })
      infoWindow.open(map, [mapLng, mapLat])
      map.setFitView([marker])
    }
    function clickMap(e) {
      geocoder.getAddress(e.lnglat, (status, result) => {
        if (status === 'complete' && result.regeocode) {
          updateEmit(result.regeocode.formattedAddress,e.lnglat.lat,e.lnglat.lng)
          if (marker) {
            map.remove(marker)
          }
          position()
        }
      })
    }
    function init() {
      geocoder = new AMap.Geocoder({
        extensions: 'all'
      })

      map = new AMap.Map(container.value, {
        resizeEnable: true
      })

      if (props.modelValue) {
        position()
      } else {
        AMap.plugin('AMap.Geolocation', () => {
          geolocation = new AMap.Geolocation({
            // 是否使用高精度定位，默认：true
            enableHighAccuracy: true,
            // 设置定位超时时间，默认：无穷大
            timeout: 10000,
            // 定位按钮的停靠位置的偏移量，默认：Pixel(10, 20)
            buttonOffset: new AMap.Pixel(10, 20),
            //  定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
            zoomToAccuracy: true,
            //  定位按钮的排放位置,  RB表示右下
            buttonPosition: 'RB'
          })
        })
        geolocation.getCurrentPosition()
        AMap.event.addListener(geolocation, 'complete', onComplete)
      }
      map.on('click', clickMap)
      // 输入提示
      const auto = new AMap.Autocomplete({
        input: 'tipinput'
      })
      placeSearch = new AMap.PlaceSearch({
        map: map
      })
      map.plugin(['AMap.ToolBar'], () => {
        map.addControl(new AMap.ToolBar())
      })
      if (location.href.indexOf('&guide=1') !== -1) {
        map.setStatus({ scrollWheel: false })
      }
      AMap.event.addListener(auto, 'select', select)// 注册监听，当选中某条记录时会触发
      AMap.event.addListener(placeSearch, 'markerClick', e => {
        updateEmit(e.data.address,e.data.location.lat,e.data.location.lng)
      })
    }
    function updateEmit(address,lat,lng) {
      mapLat = lat
      mapLng = lng
      mapAddress.value = address
      ctx.emit('update:modelValue',address)
      ctx.emit('update:lat',lat)
      ctx.emit('update:lng',lng)
    }
    function select(e) {
      const poi = e.poi
      placeSearch.setCity(poi.adcode)
      placeSearch.search(poi.name) // 关键字查询查询
      updateEmit(poi.district + poi.address,poi.location.lat,poi.location.lng)
    }
    return {
      init,
      searchText,
      drawer,
      mapAddress,
      container
    }
  },
})
</script>
<style >
  .amap-sug-result {
    z-index: 10000 !important;
  }
  .amap-sug-result .auto-item {
    font-size: 14px;
    padding: 5px 0px;
    text-indent: 10px;
  }
</style>
