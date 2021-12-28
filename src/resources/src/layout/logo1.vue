<template>
    <div class="sidebar-logo-container" :class="{'collapse':!collapse}">
        <transition name="sidebarLogoFade">
            <router-link key="collapse" class="sidebar-logo-link" to="/">
                <img :src="state.info.webLogo" class="sidebar-logo">
                <h1 v-if="collapse" class="sidebar-title">{{state.info.webName}}</h1>
            </router-link>
        </transition>
    </div>
</template>

<script>
    import { store } from '@/store'
    import {defineComponent,inject} from 'vue'
    export default defineComponent({
        name: 'SidebarLogo',
        props:{
            collapse:Boolean
        },
        setup(){
            const state = inject(store)
            return {
                state
            }
        }
    })
</script>

<style lang="scss" scoped>
    .sidebarLogoFade-enter-active {
        transition: opacity 1.5s;
    }

    .sidebarLogoFade-enter,
    .sidebarLogoFade-leave-to {
        opacity: 0;
    }

    .sidebar-logo-container {
        position: relative;
        height: 60px;
        width: 100%;
        line-height: 60px;
        border-bottom: 1px solid #101117;
        text-align: center;
        overflow: hidden;
        & .sidebar-logo-link {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            & .sidebar-logo {
                width: 32px;
                height: 32px;
                background: #ffffff;
                border-radius: 5px;
                vertical-align: middle;
                margin-right: 12px;
            }
            & .sidebar-title {
                display: inline-block;
                margin: 0;
                color: #ffffff;
                font-weight: 600;
                line-height: 60px;
                font-size: 16px;
                font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
                vertical-align: middle;
            }
        }

        &.collapse {
            .sidebar-logo {
                margin-right: 0px;
            }
        }
    }
</style>
