<template>
    <render :data="state.component"></render>
</template>

<script>
    import { store } from '@/store'
    import {defineComponent,inject} from 'vue'
    export default defineComponent({
        name: "login.vue",
        setup(){
            const state = inject(store)
            return {
                state
            }
        }
    })
</script>

<style scoped>

</style>
