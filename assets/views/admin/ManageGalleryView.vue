<script setup>
import { ref, onMounted } from 'vue'
import { X, Trash2, Plus } from '@lucide/vue'
import AdminLayout from '../../components/admin/layout/AdminLayout.vue'
import PhotoForm from '../../components/admin/ui/PhotoForm.vue'
import ConfirmModal from '../../components/admin/ui/ConfirmModal.vue'
import { useConfirm } from '../../composables/useConfirm.js'

const photos = ref([])
const showModal = ref(false)
const { confirmState, confirm, onConfirm, onCancel } = useConfirm()

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
  const ok = await confirm('Supprimer cette photo de la galerie ?')
  if (!ok) return
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

    <ConfirmModal
      v-if="confirmState"
      :message="confirmState.message"
      @confirm="onConfirm"
      @cancel="onCancel"
    />

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

<style scoped lang="scss">
@use '../../styles/utils/variables' as *;
@use '../../styles/utils/breakpoints' as *;

.admin-gallery-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: $size-12;
  padding: $size-20;

  @media (min-width: $sm) {
    grid-template-columns: repeat(3, 1fr);
  }

  @media (min-width: $md) {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: $size-16;
    padding: $size-24;
  }
}

.admin-gallery-item {
  border-radius: $size-6;
  aspect-ratio: 1;
  background: linear-gradient(135deg, $color-brown, $color-brown-mid);
  position: relative;
  overflow: hidden;
  cursor: pointer;

  img { width: 100%; height: 100%; object-fit: cover; display: block; }

  &__overlay {
    position: absolute;
    inset: 0;
    background: rgba($color-brown, .6);
    opacity: 0;
    transition: opacity .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: $size-8;
  }

  &:hover &__overlay { opacity: 1; }

  &--add {
    background: transparent;
    border: 2px dashed $color-cream-dark;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: $size-8;
    color: $color-gray-300;
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-12;
    font-weight: 600;
    cursor: pointer;
    transition: border-color .2s, color .2s;

    &:hover { border-color: $color-gold; color: $color-gold; }

    .plus { font-size: $size-32; line-height: 1; }
  }
}
</style>
