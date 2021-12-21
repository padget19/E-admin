<template>
    <pre v-highlight><code>{{code}}</code></pre>
</template>

<script>
    export default {
        name: "highlight",
        props:{
            code:String,
        }
    }
</script>
