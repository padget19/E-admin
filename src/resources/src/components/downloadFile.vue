<template>
  <a target="_blank" :href="url">
    <div style="display: flex;align-items:center;height: 35px" @mouseover="showDownload=true" @mouseout="showDownload=false">
      <i v-show="showDownload" class="el-icon-download down" />
      <el-image v-show="!showDownload" :src="fileIcon(url)" style="width: 32px;height: 32px;">
        <template #error style="display: flex; align-items: center;"> <i class="el-icon-document" style="font-size: 32px" /></template>
      </el-image>
      &nbsp;&nbsp;{{ lastName(url,filename) }}
    </div>
  </a>
</template>

<script>
import {defineComponent,ref} from "vue";
import {lastName,fileIcon} from '@/utils'
export default defineComponent({
  name: 'EadminDownloadFile',
  props: {
    filename: String,
    url: String
  },
  setup(props){
    const showDownload = ref(false)
    return {
      lastName,
      fileIcon,
      showDownload
    }
  }
})
</script>

<style lang="scss" scoped>
  @import "../styles/element-variables.scss";
  .down{
    color: $--color-primary;
  }
</style>
