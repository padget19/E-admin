<template>
    <a-dropdown>
        <div><slot></slot></div>
        <template #overlay>
            <slot name="dropdown"></slot>
        </template>
    </a-dropdown>
    <slot name="reference"></slot>
</template>

<script>
    export default {
        name: "EadminDropdown"
    }
</script>

<style scoped>

</style>
