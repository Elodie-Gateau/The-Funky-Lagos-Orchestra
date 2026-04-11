<script setup>
import { X} from "@lucide/vue";
import AdminHeader from "../../components/admin/layout/AdminHeader.vue";
import AdminMenu from "../../components/admin/layout/AdminMenu.vue";
import { MapPin } from '@lucide/vue';
import EventForm from "../../components/admin/ui/EventForm.vue";
import {onMounted, ref} from "vue";

const events = ref([]);
const showModal = ref(false);
const editingEvent = ref(null);

onMounted(async() => {
    const res = await fetch("/api/events", {credentials: 'include'})
    const data = await res.json();
    events.value = data.events;
})
</script>

<template>
    <AdminHeader   />
    <AdminMenu />
    <a href="#" @click.prevent="editingEvent = null; showModal = true">Ajouter un concert</a>

    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal">
            <button @click="showModal = false"><X /></button>
            <EventForm :event="editingEvent" @close="showModal = false" />
        </div>
    </div>

    <ul id="events">
        <li v-for="event in events" :key="event.id">
            {{ event.date }}
            {{ event.name }}
            <MapPin /> {{ event.location }}
            {{ event.host }}
            <button @click="editingEvent = event; showModal = true">Modifier</button>
        </li>
    </ul>
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
