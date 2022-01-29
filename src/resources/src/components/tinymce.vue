<template>
  <div class="tags" v-if="tags.length > 0">
    <div>点击插入：</div>
    <div class="tags-group">
      <span v-for="item in tags" @click="insertTag" class="eadmin-tag">{{ item }}</span>
    </div>
  </div>
  <editor
      v-model="myValue"
      :init="init"
      :id="elementId"
  />

</template>
<script>
import tinymce from 'tinymce/tinymce'
import Editor from '@tinymce/tinymce-vue'
import 'tinymce/themes/silver'
// 编辑器插件plugins
// 更多插件参考：https://www.tiny.cloud/docs/plugins/

import 'tinymce/plugins/advlist'
import 'tinymce/plugins/anchor'
import 'tinymce/plugins/autolink'
import 'tinymce/plugins/autosave'
import 'tinymce/plugins/code'
import 'tinymce/plugins/codesample'
import 'tinymce/plugins/colorpicker'
import 'tinymce/plugins/contextmenu'
import 'tinymce/plugins/directionality'
import 'tinymce/plugins/fullscreen'
import 'tinymce/plugins/hr'
import 'tinymce/plugins/image'
import 'tinymce/plugins/imagetools'
import 'tinymce/plugins/insertdatetime'
import 'tinymce/plugins/link'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/media'
import 'tinymce/plugins/nonbreaking'
import 'tinymce/plugins/noneditable'
import 'tinymce/plugins/pagebreak'
import 'tinymce/plugins/preview'
import 'tinymce/plugins/save'
import 'tinymce/plugins/searchreplace'
import 'tinymce/plugins/spellchecker'
import 'tinymce/plugins/tabfocus'
import 'tinymce/plugins/table'
import 'tinymce/plugins/template'
import 'tinymce/plugins/textcolor'
import 'tinymce/plugins/textpattern'
import 'tinymce/plugins/visualblocks'
import 'tinymce/plugins/visualchars'
import 'tinymce/plugins/wordcount'
import 'tinymce/plugins/print'
import 'tinymce/icons/default'
import OSS from 'ali-oss'
import * as qiniu from 'qiniu-js'
import {uniqidMd5} from '@/utils'
import {defineComponent, reactive, watch, onMounted,toRefs} from "vue";
import {ElMessage,ElLoading} from 'element-plus';
import variables  from '../styles/theme.scss';
export default defineComponent({
  name: 'EadminEditor',
  components: {
    Editor
  },
  props: {
    url: {
      type: String,
      default: ''
    },
    token: {
      type: String,
      default: ''
    },
    modelValue: {
      type: String,
      default: ''
    },
    height: {
      type: [String, Number],
      default: 500
    },
    width: {
      type: [String, Number],
      default: 'auto'
    },
    accessKey: {
      type: String,
      default: ''
    },
    secretKey: {
      type: String,
      default: ''
    },
    bucket: {
      type: String,
      default: ''
    },
    region: {
      type: String,
      default: ''
    },
    domain: {
      type: String,
      default: ''
    },
    uploadToken: {
      type: String,
      default: ''
    },
    upType: {
      type: String,
      default: 'local'
    },
    toolbar: {
      type: [String, Array],
      default: 'bold italic underline strikethrough | fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | undo redo | link unlink image axupimgs media code | removeformat fullscreen'
    },
    options: {
      type: [Object, Array],
      default: {}
    },
    tags: {
      type: Array,
      default: []
    }
  },
  emits: ['update:modelValue'],
  setup(props, ctx) {
    const state = reactive({
      init: {
        base_url: '/eadmin/tinymce',
        language_url: `/eadmin/tinymce/langs/zh_CN.js`,
        language: 'zh_CN',
        skin_url: `/eadmin/tinymce/skins/ui/oxide`,
        content_css: `/eadmin/tinymce/skins/content/default/content.css`,
        height: props.height,
        width: props.width,
        menubar: false,
        plugins: 'axupimgs advlist anchor autolink autosave code codesample colorpicker  contextmenu directionality fullscreen hr image imagetools insertdatetime link lists media nonbreaking noneditable pagebreak preview print save searchreplace spellchecker tabfocus table template textcolor textpattern visualblocks visualchars wordcount',
        toolbar: props.toolbar,
        file_picker_types: 'media',
        video_template_callback: (data) => {
          return '<video width="' + data.width + '" height="' + data.height + '"' + (data.poster ? ' poster="' + data.poster + '"' : '') + ' controls="controls" src="' + data.source + '"></video>';
        },
        file_picker_callback: (callback, value, meta) => {
          if (meta.filetype == 'media') {
            let input = document.createElement('input');//创建一个隐藏的input
            input.setAttribute('type', 'file');
            input.onchange = function () {
              let file = this.files[0];//选取第一个文件
              upload(file).then(res => {
                callback(res, {title: file.name})
              }).catch(res => {

                ElMessage({
                  type: 'error',
                  message: res
                })
              })
            }
            //触发点击
            input.click();
          }
        },
        branding: false,
        convert_urls: false,
        content_style: 'img {max-width:100% !important } .eadmin-tag{background-color:#ecf5ff;border-color:#d9ecff;color:#409eff;display:inline-block;height:32px;padding:0 10px;line-height:30px;font-size:12px;color:'+variables.theme+';border-width:1px;border-style:solid;border-radius:4px;box-sizing:border-box;white-space:nowrap} .eadmin-tag +.eadmin-tag{margin-left:8px}',
        external_plugins: {
          'powerpaste': '/eadmin/tinymce/plugins/powerpaste/plugin.min.js'
        },
        images_upload_handler: (blobInfo, succFun, failFun) => {
          upload(blobInfo.blob()).then(res => {
            succFun(res)
          }).catch(res => {
            ElMessage({
              type: 'error',
              message: res
            })
          })
        }
      },
      oss: null,
      myValue: props.modelValue,
      loading: null,
      tinymce: null,
      elementId: uniqidMd5(),
    })
    state.elementId = uniqidMd5()
    state.init = Object.assign(state.init, props.options)
    watch(() => state.myValue, value => {
      ctx.emit('update:modelValue', value)
    })
    onMounted(() => {
      tinymce.init({})
    })

    function insertTag(e) {
      const ed = tinymce.get(state.elementId);                // get editor instance
      ed.execCommand('mceInsertContent', false, '<span class="eadmin-tag" contenteditable="false">' + e.target.innerText + '</span>')
      console.log(ed.getContent())
      state.myValue = ed.getContent()
    }

    function upload(file) {
      return new Promise((resolve, reject) => {
        if (file instanceof File) {
          // 是文件对象不做处理
        } else {
          // 转换成文件对象
          file = new File([file], file.name)
        }

        var filename = file.name
        var index = filename.lastIndexOf('.')
        var suffix = filename.substring(index + 1, filename.length)
        filename = uniqidMd5() + '.' + suffix
        if (props.upType == 'oss') {
          state.oss = new OSS({
            accessKeyId: props.accessKey,
            accessKeySecret: props.secretKey,
            bucket: props.bucket,
            region: props.region
          })
          state.oss.multipartUpload(filename, file, {
            progress: percentage => {
              loadingText(parseInt(percentage * 100))
            }
          }).then(result => {

            if (result.res.requestUrls) {
              state.loading.close()
              resolve(`${props.domain}/${filename}`)
            }
          }).catch(err => {
            state.loading.close()
            reject('上传失败: ' + err)
            return
          })
        } else if (props.upType == 'qiniu') {
          var observable = qiniu.upload(file, filename, props.uploadToken, {
            fname: filename,
            params: {}
          })
          observable.subscribe({
            next: res => {
              loadingText(parseInt(res.total.percent))
            },
            error: err => {
              state.loading.close()
              reject('上传失败: ' + err)
              return
            },
            complete: res => {
              state.loading.close()
              resolve(`${props.domain}/${filename}`)
            }
          })
        } else {
          var xhr, formData
          xhr = new XMLHttpRequest()
          xhr.withCredentials = false
          xhr.open('POST', props.url)
          xhr.onerror = evt => {
            reject('上传失败')
          }
          xhr.upload.onprogress = evt => {
            const progress = Math.round(evt.loaded / evt.total * 100) + "%";
            loadingText(progress)
          }
          xhr.onload = () => {
            state.loading.close()
            var json
            if (xhr.status != 200) {
              reject('上传失败: ' + xhr.status)
              return
            }
            try {
              json = JSON.parse(xhr.responseText)
              if (json.code !== 200) {
                reject('Invalid JSON: ' + xhr.responseText)
                return
              }
              resolve(json.data)
            } catch (e) {
              reject('上传失败')
            }
          }
          formData = new FormData()
          formData.append('file', file, file.name)
          formData.append('filename', filename)
          xhr.setRequestHeader('Authorization', props.token)
          xhr.send(formData)
        }
      })
    }

    function loadingText(text) {
      if (state.loading) {
        state.loading.text = '上传中 ' + text
      } else {
        state.loading = ElLoading.service({
          target: '.tox-dialog__footer',
          text: '上传中 ' + text,
        })
      }
    }
    return {
      insertTag,
      ...toRefs(state),
    }
  }
})
</script>
<style lang="scss" scoped>
@import '@/styles/theme.scss';
.tags {
  display: flex;
  margin-bottom: 5px;
}

.eadmin-tag {
  background-color: #ecf5ff;
  border-color: #d9ecff;
  color: $theme;
  display: inline-block;
  height: 32px;
  padding: 0 10px;
  line-height: 30px;
  font-size: 12px;
  border-width: 1px;
  border-style: solid;
  border-radius: 4px;
  box-sizing: border-box;
  white-space: nowrap;
  cursor: pointer;
}

.eadmin-tag + .eadmin-tag {
  margin-left: 8px
}
</style>

