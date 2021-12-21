<template>
    <span @click="opend" v-loading="loading" class="confirm">
        <slot></slot>
    </span>
</template>

<script>
    import {defineComponent} from 'vue'
    import {useHttp} from '@/hooks'
    import {ElMessageBox} from 'element-plus';

    export default defineComponent({
        name: "EadminConfirm",
        props: {
            //请求url
            url: String,
            //请求method
            method: {
                type: String,
                default: 'post'
            },
            //请求参数
            params: Object,
        },
        emits: ['confirm', 'cancel','gridRefresh'],
        setup(props, ctx) {
            const {loading,http} = useHttp()
            function opend() {
                ElMessageBox.confirm(props.message, props.title, ctx.attrs)
                    .then(({value}) => {
                        if (props.url) {
                            let params = props.params
                            if (value) {
                                params = Object.assign(params, {inputValue: value})
                            }
                            http({
                                url: props.url,
                                method: props.method,
                                data: params
                            }).then((res)=>{
                                ctx.emit('gridRefresh')
                                ctx.emit('confirm')
                            })
                        }else{
                            ctx.emit('gridRefresh')
                            ctx.emit('confirm')
                        }
                    }).catch((action) => {
                         ctx.emit('cancel')
                    })
            }

            return {
                loading,
                opend
            }
        }
    })
</script>

<style scoped>
.confirm{
    display: inline-block;
}
</style>
