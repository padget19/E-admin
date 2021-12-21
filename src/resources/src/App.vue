<template>
    <router-view/>
    <eadmin-error-page/>
</template>

<script>
    import EadminErrorPage from './layout/EadminErrorPage.vue'
    import {defineComponent} from "vue";
    import resize from "@/hooks/use-resize"
    export default defineComponent({
        name: 'App',
        components:{
            EadminErrorPage,
        },
        setup() {
            resize()
        }
    })
</script>
