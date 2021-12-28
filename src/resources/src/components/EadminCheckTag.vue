<template>
    <el-check-tag style="margin-right: 8px;"
                  v-for="option in options" :key="option"
                  :checked="isSelect(option.value)"
                  @change="checked => handleChange(option.value, checked)"
    >
        {{ option.label }}
    </el-check-tag>
</template>

<script>
    import {defineComponent, ref, watch} from "vue";

    export default defineComponent({
        name: "EadminCheckTag",
        props: {
            modelValue: [Array,String,Number],
            options: Array,
            multiple:Boolean,
            disabled:Boolean,
        },
        emits: ['update:modelValue'],
        setup(props,ctx){
            const value = ref(props.modelValue)
            if(props.multiple && !Array.isArray(value.value)){
                value.value = []
            }
            watch(() => props.modelValue, val => {

                value.value = val
            })
            watch(value, value => {

                ctx.emit('update:modelValue', value)
            })
            //标签是否选择
            function isSelect(selectValue){
                if(props.multiple){
                    return  value.value.some((item)=>{
                        if(item == selectValue){
                            return true
                        }else{
                            return false
                        }
                    })
                }else{
                    if(value.value == selectValue){
                        return true
                    }else{
                        return false
                    }
                }
            }
            //标签切换事件
            function handleChange(val,checked) {
                if(props.disabled){
                    return
                }
                if(props.multiple){
                    if(checked){
                        value.value.push(val)
                    }else{
                        const index =  value.value.indexOf(val)
                        value.value.splice(index, 1)
                    }
                }else{
                    if(checked){
                        value.value = val
                    }else{
                        value.value = null
                    }
                }
            }
            return {
                isSelect,
                handleChange
            }
        }
    })
</script>

<style scoped>

</style>
