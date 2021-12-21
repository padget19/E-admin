<template>
    <a-step>
        <template #title><slot name="title"></slot></template>
        <template #subTitle><slot name="subTitle"></slot></template>
        <template #icon><slot name="icon"></slot></template>
        <template #description><slot name="description"></slot></template>
    </a-step>
</template>

<script>
    export default {
        name: "EadminStep"
    }
</script>

<style scoped>

</style>
