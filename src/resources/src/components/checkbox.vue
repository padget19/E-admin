<template>
    <el-checkbox v-if="onCheckAll" :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
    <el-checkbox-group v-model="value" @change="handleCheckedCitiesChange">
        <slot></slot>
    </el-checkbox-group>
</template>

<script>
    import {defineComponent, ref, watch} from "vue";
    export default defineComponent({
        name: "EadminCheckboxGroup",
        props: {
            modelValue: Array,
            options: Array,
            checkAll:Boolean,
            onCheckAll:Boolean,
            checkTag:Boolean,
        },
        emits: ['update:modelValue'],
        setup(props, ctx) {
            const value = ref(props.modelValue)
            const checkAll = ref(props.modelValue.length == props.options.length)
            const isIndeterminate = ref(props.modelValue.length > 0 && props.modelValue.length < props.options.length)
            if(props.checkAll){
                value.value = props.options.map(item=>item.value)
            }
            watch(() => props.modelValue, val => {
                value.value = val
            })
            watch(value, value => {
                ctx.emit('update:modelValue', value)
            })
            //多选框全选
            function handleCheckAllChange(val) {
                value.value = val ? props.options.map(item=>item.value) : []
                isIndeterminate.value = false;
            }
            function handleCheckedCitiesChange(value) {
                let checkedCount = value.length;
                checkAll.value = checkedCount === props.options.length;
                isIndeterminate.value = checkedCount > 0 && checkedCount < props.options.length;
            }

            return {
                value,
                isIndeterminate,
                checkAll,
                handleCheckedCitiesChange,
                handleCheckAllChange
            }
        }
    })
</script>

<style scoped>

</style>
