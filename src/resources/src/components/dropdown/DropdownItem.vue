<template>
    <div><a-menu-item @click.native="clickHandel"><slot></slot></a-menu-item></div>
</template>

<script>
    import {defineComponent} from 'vue'
    import {useAjax} from '@/hooks'
    export default defineComponent({
        name: "EadminDropdownItem",
        emits: ['gridRefresh'],
        setup(props, ctx) {
            const {http} = useAjax()
            function clickHandel() {
                http(ctx)
            }
            return {
                clickHandel
            }
        }
    })
</script>

<style scoped>

</style>
