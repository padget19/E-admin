import OSS from "ali-oss";
import * as qiniu from "qiniu-js";
import {uniqidMd5} from "./index";
import {ElLoading} from "element-plus";
import request from '@/utils/axios'
export default function upload() {
    return {
        config:{},
        loading: null,
        loadingText(text) {
            const loadText = '上传中 ' + text
            if (!this.loading) {
                this.loading = ElLoading.service({fullscreen: true, text: loadText});
            }else{
                this.loading.setText(loadText)
            }
        },
        upload:async function (file) {
            await request({
                url: 'eadmin/uploadConfig'
            }).then(res => {
                this.config = res
            })
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
                if (this.config.type == 'oss') {
                    this.oss = new OSS({
                        accessKeyId: this.config.accessKey,
                        accessKeySecret: this.config.secretKey,
                        bucket: this.config.bucket,
                        region: this.config.region
                    })
                    this.oss.multipartUpload(filename, file, {
                        progress: percentage => {
                            this.loadingText(parseInt(percentage * 100))
                        }
                    }).then(result => {

                        if (result.res.requestUrls) {
                            this.loading.close()
                            resolve(`${this.config.domain}/${filename}`)
                        }
                    }).catch(err => {
                        this.loading.close()
                        reject('上传失败: ' + err)
                        return
                    })
                } else if (this.config.type == 'qiniu') {
                    var observable = qiniu.upload(file, filename, this.config.uploadToken, {
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
                            resolve(`${this.config.domain}/${filename}`)
                        }
                    })
                } else {
                    var xhr, formData
                    xhr = new XMLHttpRequest()
                    xhr.withCredentials = false
                    xhr.open('POST', this.config.url)
                    xhr.onerror = evt => {
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
                    xhr.setRequestHeader('Authorization', localStorage.getItem('eadmin_token'))
                    xhr.send(formData)
                }
            })
        }
    }
}
