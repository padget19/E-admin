<template>
    <div ref="video"></div>
</template>

<script>
    import {defineComponent,ref,nextTick} from 'vue'
    import Player from 'xgplayer';
    export default defineComponent({
        name: "EadminVideo",
        props:{
          options:Object,
        },
        setup(props){
            const video = ref('')
            nextTick(()=>{
                const options = Object.assign(props.options,{el:video.value})
                new Player(options)
            })
            return {
                video
            }
        }
    })
</script>

<style scoped>

</style>