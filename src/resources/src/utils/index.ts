import {isExternal} from "./validate";
import router from '@/router'


import { store,action} from '@/store'
import { t,use } from 'element-plus/lib/locale'
// @ts-ignore
import md5 from 'js-md5'
import {inject} from "vue";
export function treeData(source, id, parentId, children){
    let cloneData = JSON.parse(JSON.stringify(source))
    return cloneData.filter(father=>{
        let branchArr = cloneData.filter(child => father[id] == child[parentId]);
        branchArr.length>0 ? father[children] = branchArr : ''
        return father[parentId] == 0    // 如果第一层不是parentId=0，请自行修改
    })
}
var lang = {}
export function setLang(data){
    lang = data
}
export function trans(name){
    // @ts-ignore
    use(lang.element)
    return t(name)
}
export function findParent(datas: Array<any>, pid: string) {
    let list = [], find
    do {

        find = findTree(datas, pid, 'id')
        if (find) {
            // @ts-ignore
            list.unshift(find)
            pid = find.pid
        }
    } while (find)
    return list
}

export function findArrKey(arr, uid, field) {
    var index = null;
    arr.forEach(function (val, i) {
        if (uid == val[field]) {
            index = i;
            return;
        }
    });
    return index;
}
export function genId() {
    return Number(Math.random().toString().substr(3, 10) + Date.now()).toString(36);
}
export function treeMap(datas: Array<any>,  field: string) {
    let keys = [];
    const mapKey = datas.map(item => {
        if (item.children) {
            // @ts-ignore
            keys = keys.concat(treeMap(item.children, field))
        }
        return item[field]
    });
    // @ts-ignore
    return keys = keys.concat(mapKey)
}
export function findTree(datas: Array<any>, id: any, field: string) {
    for (let key in datas) {
        if (datas[key][field] == id) {
            return datas[key]
        }
        if (datas[key].children) {
            let item: any = findTree(datas[key].children, id, field)
            if (item) {
                return item
            }
        }
    }
    return null
}

export function deleteArr(arr, value) {
    for (var i = arr.length; i > 0; i--) {
        if (arr[i - 1] == value) {
            arr.splice(i - 1, 1)
        }
    }
}

//数字去重
export function unique(arrs) {
    return arrs.filter((value, index, arr) => arr.indexOf(value) === index)
}

//刷新
export function refresh() {
    router.push({path: '/refresh', replace: true})
}

//跳转
export function link(url: string) {
    if (isExternal(url)) {
        window.open(url)
    } else {
        if(url.indexOf('/') === 0){
            router.push(url)
        }else{
            router.push('/' + url)
        }
    }
}

export function lastName(path: string, filename: string = '') {
    if (filename) {
        return filename
    } else {
        var index = path.lastIndexOf('\/')
        return path.substring(index + 1, path.length)
    }
}

export function fileIcon(path: string) {
    var index = path.lastIndexOf('\.')
    var ext = path.substring(index + 1, path.length)
    try {
        return require('@/assets/file_icon/' + ext + '.png')
    } catch (e) {
        return ''
    }
}
export function appendCss(url,css,cache) {
    const style = document.createElement('style')
    style.type = 'text/css'
    style.innerHTML = css
    if(cache){
        action.cacheCss(url,css)
    }
    style.setAttribute('data-key', 'eadmin_style_' + md5(url))
    document.getElementsByTagName('head')[0].appendChild(style)
}
export function setObjectValue(obj, path, value) {
    const arr = path.split('.')
    const len = arr.length - 1
    arr.reduce((cur, key, index) => {
        if (!cur.hasOwnProperty(key))
            throw `${key} 不存在!`
        if (index === len) {
            cur[key] = value
        }
        return cur[key]
    }, obj)
}
export function encode(val) {
    return encodeURIComponent(val).
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}
export function forEach(obj, fn) {
    // Don't bother if no value provided
    if (obj === null || typeof obj === 'undefined') {
        return;
    }

    // Force an array if not already something iterable
    if (typeof obj !== 'object') {
        /*eslint no-param-reassign:0*/
        obj = [obj];
    }

    if (Array.isArray(obj)) {
        // Iterate over array values
        for (var i = 0, l = obj.length; i < l; i++) {
            fn.call(null, obj[i], i, obj);
        }
    } else {
        // Iterate over object keys
        for (var key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                fn.call(null, obj[key], key, obj);
            }
        }
    }
}
export function buildURL(url, params) {
    /*eslint no-param-reassign:0*/
    if (!params) {
        return url;
    }
    var serializedParams;
    var parts = [];
    forEach(params,function serialize(val, key) {
        if (val === null || typeof val === 'undefined') {
            return;
        }
        if (Array.isArray(val)) {
            key = key + '[]';
        } else {
            val = [val];
        }
        forEach(val,function parseValue(v) {
            if (toString.call(v) === '[object Date]') {
                v = v.toISOString();
            } else if (v !== null && typeof v === 'object') {
                v = JSON.stringify(v);
            }
            // @ts-ignore
            parts.push(encode(key) + '=' + encode(v));
        });
    })
    serializedParams = parts.join('&');
    if (serializedParams) {
        var hashmarkIndex = url.indexOf('#');
        if (hashmarkIndex !== -1) {
            url = url.slice(0, hashmarkIndex);
        }
        url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
    }
    return url;
}

export function uniqidMd5() {
    const rand = ('0000' + (Math.random() * Math.pow(36, 4) << 0).toString(36)).slice(-4)
    return md5(rand)
}
//节流防抖  mark传入标识自动判断开启节流
export function debounce(fun, delay) {
    return (args,mark) => {
        // @ts-ignore
        let that = this
        //传入的参数
        let _args = args
        if(!fun.throttle){
            //默认不开启节流
            fun.throttle = false
        }
        //传入标识
        mark = md5(mark)
        if(mark){
            if(fun[mark]){
                let now = new Date().getTime()
                // @ts-ignore 判断是否开启节流
                if(now - fun[mark] < delay){
                    fun.throttle = true
                }
            }
            //标记最新调用时间用于判断是否开启节流
            fun[mark] = new Date().getTime()
        }
        if(fun.throttle){
            //节流执行
            clearTimeout(fun.id)
            fun.id = setTimeout(function () {
                fun.call(that, _args)
            }, delay)
        }else{
            //默认不执行节流
            fun.call(that, _args)
        }
    }
}
//生成随机字母
export function randomCoding(length){
    //创建26个字母数组
    var arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    var idvalue ='';
    for(var i=0;i<length;i++){
        idvalue+=arr[Math.floor(Math.random()*26)];
    }
    return idvalue;
}
export function empty(value) {
    if(!value && value !== 0){
        return true
    }
    if(Array.isArray(value) && value.length == 0){
        return true
    }
}
export function offsetTop(el) {
    console.log(el)
    console.log(el.offsetTop)
    if(el.parentElement) {
        return offsetTop(el.parentElement) + el.offsetTop
    }
    return el.offsetTop
}
