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
