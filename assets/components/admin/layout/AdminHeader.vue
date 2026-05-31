<script setup>
import { ExternalLink } from '@lucide/vue'

const props = defineProps({
  pageTitle: { type: String, default: 'Admin' }
})
const emit = defineEmits(['toggle-sidebar'])
</script>

<template>
  <header class="admin-topbar">
    <div class="admin-topbar__left">
      <button class="admin-topbar__hamburger" @click="emit('toggle-sidebar')" aria-label="Ouvrir le menu">
        <span /><span /><span />
      </button>
      <nav class="admin-topbar__breadcrumb" aria-label="Fil d'Ariane">
        <span>Admin</span>
        <span class="sep">›</span>
        <span class="current">{{ pageTitle }}</span>
      </nav>
    </div>
    <div class="admin-topbar__right">
      <a href="/" target="_blank" class="admin-topbar__site-link">
        <ExternalLink />
        Voir le site
      </a>
    </div>
  </header>
</template>

<style scoped lang="scss">
@use '../../../styles/utils/variables' as *;
@use '../../../styles/utils/breakpoints' as *;

.admin-topbar {
  height: $size-64;
  background: $color-white;
  border-bottom: 1px solid $color-cream-dark;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 $size-16;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 4px rgba($color-brown, .06);
  flex-shrink: 0;

  @media (min-width: $md) {
    padding: 0 $size-32;
  }

  &__left {
    display: flex;
    align-items: center;
    gap: $size-16;
  }

  &__hamburger {
    display: flex;
    flex-direction: column;
    gap: 4px;
    cursor: pointer;
    padding: $size-6;
    background: none;
    border: none;

    span {
      width: 22px;
      height: 2px;
      background: $color-brown;
      border-radius: 2px;
      display: block;
    }

    @media (min-width: $md) { display: none; }
  }

  &__breadcrumb {
    display: flex;
    align-items: center;
    gap: $size-6;
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-12;
    font-weight: 600;
    color: $color-gray-300;

    .sep { opacity: .5; }

    .current {
      color: $color-brown;
      font-weight: 700;
    }
  }

  &__right {
    display: flex;
    align-items: center;
    gap: $size-8;
  }

  &__site-link {
    display: inline-flex;
    align-items: center;
    gap: $size-6;
    padding: $size-6 $size-12;
    background: $color-cream;
    border: 1.5px solid $color-gold;
    border-radius: $size-6;
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-10;
    font-weight: 700;
    color: $color-brown;
    letter-spacing: .05em;
    text-transform: uppercase;
    transition: background .2s, color .2s;

    @media (min-width: $md) {
      font-size: $size-12;
      padding: $size-6 $size-16;
    }

    &:hover {
      background: $color-gold;
      color: $color-white;
    }

    svg { width: $size-14; height: $size-14; flex-shrink: 0; }
  }
}
</style>
