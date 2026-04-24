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
    <hr>
</div>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

.section-title{
    display: flex;
    flex-direction: column;
    gap: $size-10;
    justify-content: center;
    align-items: center;
    margin-bottom: $size-56;
}
h2{
    color: var(--secondary);
    font-size: $size-12;
    font-weight: 700;
    letter-spacing: 3px;
    list-style: inside;
}

h2::before {
    content: "● ";
    font-size: 0.5em;
    vertical-align: 0.7ex;
}

h3 {
    color: var(--title);
    font-size: $size-28;
    font-weight: 800;
}

hr{
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
    width: $size-48;
    height: 3.5px;
    border-radius: 2px;
}
</style>
