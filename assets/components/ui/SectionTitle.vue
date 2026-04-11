<script setup>
defineProps({
    title: String,
    subtitle: String
})
</script>

<template>
<div class="section-title">
    <h2>{{ title }}</h2>
    <h3>{{ subtitle }}</h3>
</div>
</template>

<style scoped>

</style>
