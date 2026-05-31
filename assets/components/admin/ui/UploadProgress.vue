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

<style scoped lang="scss">
@use '../../../styles/utils/variables' as *;

.upload-item {
  border: 1.5px solid $color-cream-dark;
  border-radius: $size-6;
  background: $color-white;
  padding: $size-12 $size-14;
  display: flex;
  flex-direction: column;
  gap: $size-8;

  &__header {
    display: flex;
    align-items: center;
    gap: $size-8;

    > .ti {
      color: $color-brown-mid;
      font-size: $size-18;
      flex-shrink: 0;
    }
  }

  &__name {
    flex: 1;
    font-family: "Nunito", system-ui, sans-serif;
    font-size: $size-14;
    font-weight: 600;
    color: $color-brown;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  &__size {
    font-size: $size-12;
    color: $color-gray-300;
    flex-shrink: 0;
  }

  &__action {
    width: $size-24;
    height: $size-24;
    border-radius: 50%;
    border: none;
    background: $color-gray-175;
    color: $color-brown;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background .2s;
    padding: 0;

    .ti { font-size: $size-14; color: inherit; }

    svg { width: $size-14; height: $size-14; }

    &:hover { background: $color-gray-200; }
  }

  &__check {
    color: $color-success;
    font-size: $size-18;
    flex-shrink: 0;
  }

  &__track {
    height: 4px;
    background: $color-gray-175;
    border-radius: 99px;
    overflow: hidden;
  }

  &__bar {
    height: 100%;
    border-radius: 99px;
    background: $color-gold;
    transition: width .3s ease;

    &.done     { background: $color-success; }
    &.error    { background: $color-red; }
    &.loading  { background: $color-gold; }
    &.ready    { background: $color-gold; }
    &.idle     { background: $color-gray-200; }
  }

  &__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  &__badge {
    display: inline-flex;
    align-items: center;
    padding: 2px $size-8;
    border-radius: 99px;
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-10;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;

    &.idle      { background: rgba($color-gray-300, .15); color: $color-gray-300; }
    &.loading   { background: rgba($color-gold, .12);     color: $color-gold; }
    &.ready     { background: rgba($color-gold, .15);     color: $color-gold; }
    &.uploading { background: rgba($color-gold, .15);     color: $color-gold; }
    &.done      { background: rgba($color-success, .15);  color: $color-success; }
    &.error     { background: rgba($color-red, .15);      color: $color-red; }
  }

  &__pct {
    font-size: $size-12;
    color: $color-gray-300;
    font-family: "Raleway", system-ui, sans-serif;
  }
}
</style>
