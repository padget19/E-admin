<template>
    <slot>
        <span v-html="value"></span>
    </slot>
</template>

<script>
    import {defineComponent, ref, watch} from "vue";
    export default defineComponent({
        name: "EadminDisplay",
        props:{
            modelValue:[String,Number],
        },
        emits:['update:modelValue'],
        setup(props){
            const value = ref(props.modelValue)
            watch(()=>props.modelValue,val=>{
                value.value =val
            })
            return {
                value
            }
        }
    })
</script>

<style scoped>

</style>
