import app from './app'
import {link} from '@/utils'
import hljs from 'highlight.js';

import 'highlight.js/styles/atom-one-dark.css'	//样式
//跳转
app.directive('redirect', {
    created(el, binding) {
        el.onclick = function () {
            link(binding.value)
        }
    }
})
app.directive('highlight', function (el) {
    let blocks = el.querySelectorAll('pre code');
    blocks.forEach((block) => {
        hljs.highlightBlock(block)
        block.innerHTML = "<ul><li>" + block.innerHTML.replace(/\n/g,"\n</li><li>") +"\n</li></ul>";
    })
})
