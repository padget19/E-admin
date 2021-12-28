<template>
    <div class="flex">
        <render v-if="custom" :data="customRender"></render>
        <el-select v-else style="flex: 1" v-bind="$attrs" v-model="value" :multiple="multiple" @focus="focus" @click="focus" ref="select" value-key="id" v-loading="selectLoading">
            <el-option
                    v-for="item in options"
                    :key="item.id"
                    :label="item.label"
                    :value="item.id">
            </el-option>
        </el-select>
        <el-button icon="el-icon-plus" type="primary" plain style="margin-left: 5px;height: 36px" @click="open" :disabled="$attrs.disabled"></el-button>
        <el-dialog top="50px" v-model="visible" :append-to-body="true" width="80%" destroy-on-close>
            <render :data="content"  v-model:selection="selection" :scroll="height"  :add-params="params"
                    :selection-type="multiple ? 'checkbox':'radio'"></render>
            <template #footer>
                <div :class="multiple && selection.length > 0 ? 'footer':''">
                    <div v-if="multiple && selection.length > 0">已选中: {{selection.length}}</div>
                    <div>
                        <el-button type="primary" @click="submit">确认</el-button>
                        <el-button @click="visible = false">取消</el-button>
                    </div>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script>
    import {defineComponent, ref, watch} from "vue";
    import {useHttp} from '@/hooks'


    export default defineComponent({
        name: "EadminSelectTable",
        props: {
            modelValue: [String, Array, Number],
            params: Object,
            remoteParams: Object,
            custom: Boolean,
            multiple: Boolean,
        },
        inheritAttrs: false,
        emits: ['update:modelValue'],
        setup(props, ctx) {
            const selectLoading = ref(true)
            const select = ref('')
            const value = ref(props.modelValue)
            const visible = ref(false)
            const options = ref([])
            const customRender = ref(null)
            const selection = ref(props.modelValue || [])
            const content = ref('')
            if(!Array.isArray(selection.value)){
                selection.value = [selection.value]
            }
            const {loading, http} = useHttp()
            const height = {
                x:'max-content',
                y: window.innerHeight / 2
            }
            submit()
            watch(() => props.modelValue, val => {
                value.value = val
            })
            watch(value, (val) => {
                if (props.multiple) {
                    selection.value = val
                } else {
                    if(val){
                        selection.value = [val]
                    }else{
                        selection.value = []
                    }
                }
                ctx.emit('update:modelValue', val)
            })
            function open() {
                content.value = null
                visible.value = true
                http({
                    url: '/eadmin.rest',
                    params: props.params
                }).then(res => {
                    content.value = res
                })
            }

            function submit() {
                const {http} = useHttp()
                http({
                    url: '/eadmin.rest',
                    params: Object.assign(props.remoteParams, {eadminSelectTable: true, eadmin_id: selection.value}),
                }).then(res => {
                    if(props.custom){
                        customRender.value = res.data
                    }else{
                        options.value = res.data
                    }
                    visible.value = false
                    const selects = []
                    selection.value.forEach(item => {
                        selects.push(item)
                    })
                    if (props.multiple) {
                        value.value = selects
                    } else {
                        let val  = selects.pop()
                        if(typeof(val) !== 'undefined'){
                            value.value = val
                        }
                        if(!props.custom){
                            select.value.focus()
                        }
                    }
                }).finally(()=>{
                    selectLoading.value = false
                })
            }
            function focus() {
                select.value.blur()
            }
            return {
                selectLoading,
                loading,
                submit,
                open,
                focus,
                content,
                options,
                value,
                selection,
                visible,
                select,
                height,
                customRender
            }
        }
    })
</script>

<style scoped>
    .flex{
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .footer{
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>
