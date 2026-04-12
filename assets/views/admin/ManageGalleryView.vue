<script setup>
import AdminHeader from "../../components/admin/layout/AdminHeader.vue";
import AdminMenu from "../../components/admin/layout/AdminMenu.vue";
import {onMounted, ref} from "vue";
import PhotoForm from "../../components/admin/ui/PhotoForm.vue";
import { X, Trash2 } from "@lucide/vue";

const photos = ref([]);
const showModal = ref(false);


onMounted(async() => {
    const res = await fetch("/api/photos", {credentials: 'include'})
    const data = await res.json();
    photos.value = data.photos;
})

const onPhotoAdded = async () => {
    showModal.value = false
    const res = await fetch("/api/photos", { credentials: 'include' })
    const data = await res.json()
    photos.value = data.photos
}

const deletePhoto = async (id) => {
    await fetch(`/api/admin/photo/${id}/delete`, {
        method: 'DELETE',
        credentials: 'include'
    })
    photos.value = photos.value.filter(p => p.id !== id)
}
</script>

<template>
    <AdminHeader   />
    <AdminMenu />
    <a href="#" @click.prevent="showModal = true">Ajouter une photo</a>
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal">
            <button @click="showModal = false"><X /></button>
            <PhotoForm @close="onPhotoAdded"/>
        </div>
    </div>

    <ul id="gallery">
        <li v-for="photo in photos" :key="photo.id">
            <img :src="photo.path" alt="Photo" />
            <Trash2 @click="deletePhoto(photo.id)"/>
        </li>
    </ul>
</template>

<style scoped>
img {
    max-width: 100%;
}
</style>
