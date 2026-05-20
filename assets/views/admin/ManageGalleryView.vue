<script setup>
import { ref, onMounted } from 'vue'
import { X, Trash2, Plus } from '@lucide/vue'
import AdminLayout from '../../components/admin/layout/AdminLayout.vue'
import PhotoForm from '../../components/admin/ui/PhotoForm.vue'

const photos = ref([])
const showModal = ref(false)

onMounted(async () => {
  const res = await fetch('/api/photos', { credentials: 'include' })
  const data = await res.json()
  photos.value = data.photos
})

const onPhotoAdded = async () => {
  showModal.value = false
  const res = await fetch('/api/photos', { credentials: 'include' })
  const data = await res.json()
  photos.value = data.photos
}

const deletePhoto = async (id) => {
  await fetch(`/api/admin/photo/${id}/delete`, { method: 'DELETE', credentials: 'include' })
  photos.value = photos.value.filter(p => p.id !== id)
}
</script>

<template>
  <AdminLayout page-title="Galerie">
    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-header__title">Galerie</h1>
        <p class="admin-page-header__sub">Gérez les photos publiées sur le site</p>
      </div>
      <button class="admin-btn admin-btn--primary" @click="showModal = true">
        <Plus /> Importer
      </button>
    </div>

    <div class="admin-card">
      <div class="admin-card__header">
        <span class="admin-card__title">
          Photos <span class="count">({{ photos.length }})</span>
        </span>
      </div>

      <div class="admin-gallery-grid">
        <div class="admin-gallery-item admin-gallery-item--add" @click="showModal = true">
          <span class="plus">+</span>
          <span>Importer</span>
        </div>

        <div
          v-for="photo in photos"
          :key="photo.id"
          class="admin-gallery-item"
        >
          <img :src="photo.path" :alt="`Photo ${photo.id}`" />
          <div class="admin-gallery-item__overlay">
            <button
              class="admin-btn admin-btn--danger admin-btn--icon admin-btn--sm"
              title="Supprimer"
              @click.stop="deletePhoto(photo.id)"
            ><Trash2 /></button>
          </div>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="showModal" class="admin-modal-backdrop" @click.self="showModal = false">
        <div class="admin-modal">
          <div class="admin-modal__header">
            <span class="admin-modal__title">Ajouter une photo</span>
            <button class="admin-modal__close" @click="showModal = false"><X /></button>
          </div>
          <div class="admin-modal__body">
            <PhotoForm @close="onPhotoAdded" />
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<style scoped>
.count {
  color: #838383;
  font-weight: 400;
}
</style>
