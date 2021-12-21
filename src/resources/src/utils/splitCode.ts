import {useRoute} from 'vue-router'
import {action} from '@/store'
import {appendCss} from '@/utils'
// @ts-ignore
import md5 from 'js-md5'

export const getSource = (source: string, type: string) => {
    const regex = new RegExp(`<${type}[^>]*>`)
    let openingTag: any = source.match(regex)

    if (!openingTag) {
        return ''
    } else {
        openingTag = openingTag[0]
    }

    return source.slice(source.indexOf(openingTag) + openingTag.length, source.lastIndexOf(`</${type}>`))
}

export const splitCode = (codeStr: string) => {

    const script = getSource(codeStr, 'script').replace(/export default/, 'return ')
    const css = getSource(codeStr, 'style')
    const template = getSource(codeStr, 'template')
    if (css) {
        const route = useRoute()
        appendCss(route.path,css,true)
    }
    return {
        ...new Function(script)(), template
    }
}
