<script setup>

import {X} from "@lucide/vue";
import TrackForm from "../../components/admin/ui/TrackForm.vue";
import {ref} from "vue";

const showModal = ref(false);
</script>

<template>
    <a href="#" @click.prevent="showModal = true">Ajouter un morceau</a>

    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal">
            <button @click="showModal = false"><X /></button>
            <TrackForm @close="showModal = false"/>
        </div>
    </div>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.modal {
    position: relative;
    background: white;
}
</style>
