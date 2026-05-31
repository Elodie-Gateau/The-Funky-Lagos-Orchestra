<script setup>
import { X, Trash2 } from '@lucide/vue'

defineProps({
  message: { type: String, required: true },
})

defineEmits(['confirm', 'cancel'])
</script>

<template>
  <Teleport to="body">
    <div class="admin-modal-backdrop" @click.self="$emit('cancel')">
      <div class="admin-modal admin-modal--confirm">
        <div class="admin-modal__header">
          <span class="admin-modal__title">Confirmer la suppression</span>
          <button class="admin-modal__close" @click="$emit('cancel')"><X /></button>
        </div>
        <div class="admin-modal__body confirm-body">
          <div class="confirm-icon">
            <Trash2 />
          </div>
          <p class="confirm-message">{{ message }}</p>
          <p class="confirm-hint">Cette action est irréversible.</p>
        </div>
        <div class="admin-modal__footer">
          <button class="admin-btn admin-btn--secondary" @click="$emit('cancel')">Annuler</button>
          <button class="admin-btn admin-btn--primary admin-btn--delete" @click="$emit('confirm')">
            Supprimer
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped lang="scss">
@use '../../../styles/utils/variables' as *;

.confirm-body {
  align-items: center;
  text-align: center;
  gap: $size-10 !important;
  padding: $size-28 $size-24 $size-20 !important;
}

.confirm-icon {
  width: $size-44;
  height: $size-44;
  border-radius: 50%;
  background: rgba($color-red, .1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto $size-4;

  svg {
    width: $size-20;
    height: $size-20;
    color: $color-red;
  }
}

.confirm-message {
  font-family: "Raleway", system-ui, sans-serif;
  font-weight: 700;
  font-size: $size-16;
  color: $color-brown;
  margin: 0;
}

.confirm-hint {
  font-size: $size-12;
  color: $color-gray-300;
  margin: 0;
}

.admin-btn--delete {
  background: $color-red !important;
  &:hover:not(:disabled) { background: #a82215 !important; }
}
</style>
