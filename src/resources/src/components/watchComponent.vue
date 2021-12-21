<template>
    <render :data="component"></render>
</template>

<script>
    import {useHttp} from '@/hooks'
    import {defineComponent,watch,ref} from 'vue'
    import { debounce} from '@/utils'
    export default defineComponent({
        name: "watchComponent",
        props:{
            field:String,
            params:Object,
            watchComponent:Object,
            proxyData:Object,
        },
        setup(props){
            const {loading,http} = useHttp()
            const component = ref(props.watchComponent)
            const show = ref(false)
            const debounceWatch = debounce((value)=>{
                http({
                    url:'eadmin.rest',
                    params:Object.assign(props.params,{value:value,field:props.field})
                }).then(res=>{
                    component.value = res.content.default[0]
                })
            }, 300)
            watch(()=>props.proxyData[props.field],value=>{
                debounceWatch(value,props.field)
            })
            return {
                loading,
                show,
                component
            }
        }
    })
</script>

<style scoped>

</style>
