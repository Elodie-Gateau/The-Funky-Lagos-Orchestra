<script setup>
import { X } from '@lucide/vue';
import { ref } from 'vue'
import NavMenu from "../ui/NavMenu.vue";

const isOpen = ref(false)
</script>

<template>
    <div class="mobile-menu">
        <img class="burger" v-if="!isOpen" @click="isOpen = true" src="/assets/images/icon.svg" alt="menu" />
        <div v-if="isOpen">
            <button @click="isOpen = false">
                <X :size="24" />
            </button>
        </div>
        <div v-if="isOpen">
            <NavMenu/>
        </div>

    </div>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/variables' as *;
@use '/assets/styles/utils/breakpoints' as *;

.mobile-menu {
    width: 100%;
    display: flex;
    justify-content: flex-end;
    position: fixed;
    right: $size-10;
    top: $size-10
}
.burger {
    width: $size-32;
    cursor: pointer;
 }

@media(min-width: $md){
    .mobile-menu {
        display: none;
    }
}
</style>
