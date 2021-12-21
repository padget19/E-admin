<template>
    <el-tag
            :key="tag"
            v-for="tag in dynamicTags"
            closable
            :disable-transitions="false"
            @close="handleClose(tag)">
        {{tag}}
    </el-tag>
    <el-input
            class="input-new-tag"
            v-if="inputVisible"
            v-model="inputValue"
            ref="saveTagInput"
            size="mini"
            @keyup.enter.native="handleInputConfirm"
            @blur="handleInputConfirm"
    >
    </el-input>
    <el-button v-else class="button-new-tag" size="mini" @click="showInput">+ {{text}}</el-button>
</template>

<script>
    import {defineComponent,reactive,nextTick,toRefs,watch} from "vue";

    export default defineComponent({
        name: "EadminTag",
        props:{
          text:{
              type:String,
              default:'添加'
          },
          modelValue:[Array,Number,String]
        },
        emits:['update:modelValue'],
        setup(props,ctx){
            const state = reactive({
                dynamicTags:props.modelValue || [],
                inputVisible: false,
                inputValue: '',
                saveTagInput:''
            })
            watch(()=>props.modelValue,value=>{
                state.dynamicTags = value
            })
            watch(state.dynamicTags,value=>{
                ctx.emit('update:modelValue',value)
            })
            function handleClose(tag) {
                state.dynamicTags.splice(state.dynamicTags.indexOf(tag), 1)
            }
            function showInput() {
                state.inputVisible = true;
                nextTick(() => {
                    state.saveTagInput.focus()
                });
            }
            function handleInputConfirm() {
                let inputValue = state.inputValue
                if (inputValue) {
                    state.dynamicTags.push(inputValue)
                }
                state.inputVisible = false
                state.inputValue = ''
            }
            return {
                ...toRefs(state),
                showInput,
                handleInputConfirm,
                handleClose
            }
        }
    })
</script>

<style scoped>
    .el-tag + .el-tag {
        margin-left: 10px;
    }
    .button-new-tag {
        margin-left: 10px;

        padding-top: 0;
        padding-bottom: 0;
    }
    .input-new-tag {
        width: 90px;
        margin-left: 10px;
    }
</style>
