<template>
  <router-view/>
  <eadmin-error-page/>
</template>

<script>
    import { store,action} from '@/store'
    import { locale } from 'element-plus'
    import EadminErrorPage from './layout/EadminErrorPage.vue'
    import {defineComponent, inject,watch} from "vue";
    import resize from "@/hooks/use-resize"
    export default defineComponent({
        name: 'App',
        components:{
            EadminErrorPage,
        },
        setup() {
            const state = inject(store)
            watch(()=>state.lang,value=>{
              if(value.element){
                locale(value.element)
              }
            })
            resize()
            return {
              state
            }
        }
    })
</script>
