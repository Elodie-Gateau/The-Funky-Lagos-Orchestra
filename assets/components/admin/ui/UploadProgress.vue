<script setup>
import {X} from "@lucide/vue";

defineProps({
  file: { type: File, required: true },
  progress: { type: Number, default: 0 },
  state: { type: String, default: 'idle' }, // idle | uploading | done | error
  icon: { type: String, default: 'ti-photo' }
})

defineEmits(['remove', 'retry'])

const labels = {
  idle: 'En attente',
  loading: 'Lecture…',
  ready: 'Prêt',
  uploading: 'Envoi…',
  done: 'Envoyé',
  error: 'Erreur',
}
</script>

<template>
  <div class="upload-item">
    <div class="upload-item__header">
      <i :class="['ti', icon]" />
      <span class="upload-item__name">{{ file.name }}</span>
      <span class="upload-item__size">{{ (file.size / 1024 / 1024).toFixed(1) }} Mo</span>
      <button v-if="state === 'error'" type="button" @click="$emit('retry')" class="upload-item__action">
        <i class="ti ti-refresh" />
      </button>
      <button v-else-if="state !== 'done' && state !== 'uploading'" type="button" @click="$emit('remove')" class="upload-item__action">
          <X />
      </button>
      <i v-else class="ti ti-circle-check upload-item__check" />
    </div>

    <div class="upload-item__track">
      <div
        class="upload-item__bar"
        :class="state"
        :style="{ width: progress + '%' }"
      />
    </div>

    <div class="upload-item__footer">
      <span class="upload-item__badge" :class="state">{{ labels[state] }}</span>
      <span class="upload-item__pct">
        {{ state === 'done' ? (file.size / 1024 / 1024).toFixed(1) + ' Mo' : progress + '%' }}
      </span>
    </div>
  </div>
</template>
