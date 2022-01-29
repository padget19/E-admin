<template>
    <el-checkbox v-if="onCheckAll" :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">
      {{ trans('el.checkbox.all') }}</el-checkbox>
    <el-divider v-if="onCheckAll"></el-divider>
    <el-checkbox-group v-bind="$attrs" v-model="value" @change="handleCheckedCitiesChange" :class="horizontal?'horizontal':''">
        <slot></slot>
    </el-checkbox-group>
</template>

<script>
    import {trans} from '@/utils'
    import {defineComponent, ref, watch} from "vue";
    export default defineComponent({
        name: "EadminCheckboxGroup",
        props: {
            modelValue: Array,
            options: Array,
            checkAll:Boolean,
            onCheckAll:Boolean,
            checkTag:Boolean,
            horizontal:Boolean,
        },
        emits: ['update:modelValue','change'],
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
                ctx.emit('change',value.value)
            }
            function handleCheckedCitiesChange(value) {
                let checkedCount = value.length;
                checkAll.value = checkedCount === props.options.length;
                isIndeterminate.value = checkedCount > 0 && checkedCount < props.options.length;
                ctx.emit('change',value)
            }

            return {
                trans,
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
    .el-divider--horizontal {
        margin: 10px 0;
    }
    .horizontal{
        display: flex;
        flex-direction: column;
    }
</style>
