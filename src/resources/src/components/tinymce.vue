<template>
  <editor
    v-model="myValue"
    :init="init"
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
import md5 from 'js-md5'
import * as qiniu from 'qiniu-js'
export default {
  name:'EadminEditor',
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
    valueModel: {
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
    }
  },
  data() {
    return {
      init: {
        base_url:'/eadmin/tinymce',
        language_url: `/eadmin/tinymce/langs/zh_CN.js`,
        language: 'zh_CN',
        skin_url: `/eadmin/tinymce/skins/ui/oxide`,
        content_css: `/eadmin/tinymce/skins/content/default/content.css`,
        height: this.height,
        width: this.width,
        menubar: false,
        plugins: 'axupimgs advlist anchor autolink autosave code codesample colorpicker  contextmenu directionality fullscreen hr image imagetools insertdatetime link lists media nonbreaking noneditable pagebreak preview print save searchreplace spellchecker tabfocus table template textcolor textpattern visualblocks visualchars wordcount',
        toolbar: this.toolbar,
        file_picker_types: 'media',
        video_template_callback:(data)=>{
          return '<video width="' + data.width + '" height="' + data.height + '"' + (data.poster ? ' poster="' + data.poster + '"' : '') + ' controls="controls" src="' + data.source +'"></video>';
        },
        file_picker_callback: (callback, value, meta)=> {
          if (meta.filetype == 'media') {
            let input = document.createElement('input');//创建一个隐藏的input
            input.setAttribute('type', 'file');
            let that = this;
            input.onchange = function () {
              let file = this.files[0];//选取第一个文件
              that.upload(file).then(res=>{
                callback(res, {title: file.name})
              }).catch(res=>{
                that.$message({
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
        content_style: 'img {max-width:100% !important }',
        external_plugins: {
          'powerpaste': '/eadmin/tinymce/plugins/powerpaste/plugin.min.js'
        },
        images_upload_handler: (blobInfo, succFun, failFun) => {
          this.upload(blobInfo.blob()).then(res=>{
            succFun(res)
          }).catch(res=>{
            this.$message({
              type: 'error',
              message: res
            })
          })
        }
      },
      oss: null,
      myValue: this.valueModel,
      loading:null,
    }
  },
  watch: {
    value(newValue) {
      this.myValue = newValue
    },
    myValue(newValue) {
      this.$emit('input', newValue)
    }
  },
  mounted() {
    tinymce.init({})
  },
  methods: {
    upload: function (file) {
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
        filename = this.uniqidMd5() + '.' + suffix
        if (this.upType == 'oss') {
          this.oss = new OSS({
            accessKeyId: this.accessKey,
            accessKeySecret: this.secretKey,
            bucket: this.bucket,
            region: this.region
          })
          this.oss.multipartUpload(filename, file, {
            progress: percentage => {
              this.loadingText(parseInt(percentage * 100))
            }
          }).then(result => {

            if (result.res.requestUrls) {
              this.loading.close()
              resolve(`${this.domain}/${filename}`)
            }
          }).catch(err => {
            this.loading.close()
            reject('上传失败: ' + err)
            return
          })
        } else if (this.upType == 'qiniu') {
          var observable = qiniu.upload(file, filename, this.uploadToken, {
            fname: filename,
            params: {}
          })
          observable.subscribe({
            next: res => {
              this.loadingText(parseInt(res.total.percent))
            },
            error: err => {
              this.loading.close()
              reject('上传失败: ' + err)
              return
            },
            complete: res => {
              this.loading.close()
              resolve(`${this.domain}/${filename}`)
            }
          })
        } else {
          var xhr, formData
          xhr = new XMLHttpRequest()
          xhr.withCredentials = false
          xhr.open('POST', this.url)
          xhr.onerror= evt => {
            reject('上传失败')
          }
          xhr.upload.onprogress = evt => {
            const progress = Math.round(evt.loaded / evt.total * 100) + "%";
            this.loadingText(progress)
          }
          xhr.onload = () => {
            this.loading.close()
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
          xhr.setRequestHeader('Authorization', this.token)
          xhr.send(formData)
        }
      })
    },
    loadingText(text){
      if(this.loading ){
        this.loading.text = '上传中 '+text
      }else{
        this.loading = this.$loading({
          target:'.tox-dialog__footer',
          text:'上传中 '+text,
        })
      }
    },
    uniqidMd5() {
      const rand = ('0000' + (Math.random() * Math.pow(36, 4) << 0).toString(36)).slice(-4)
      return md5(rand)
    }
  }
}
</script>

