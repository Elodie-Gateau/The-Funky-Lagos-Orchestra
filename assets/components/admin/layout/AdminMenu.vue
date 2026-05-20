<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'
import { LayoutGrid, Music, Calendar, Image, Settings, LogOut } from '@lucide/vue'

const props = defineProps({
  isOpen: { type: Boolean, default: false }
})
const emit = defineEmits(['close'])

const auth = useAuthStore()
const router = useRouter()

const initials = computed(() => {
  const name = auth.user?.surname || auth.user?.email || 'A'
  return name.slice(0, 2).toUpperCase()
})

async function handleLogout() {
  await auth.logout()
  router.push('/login')
  emit('close')
}
</script>

<template>
  <aside :class="['admin-sidebar', { 'admin-sidebar--open': isOpen }]">
    <div class="admin-sidebar__header">
      <div class="admin-sidebar__logo"><img src="/assets/images/logo.svg" alt="Logo"></div>
      <div class="admin-sidebar__brand">
          Espace Administrateur
      </div>
    </div>

    <nav class="admin-sidebar__nav">
      <div class="admin-sidebar__section-label">Navigation</div>

      <RouterLink
        to="/admin"
        class="admin-sidebar__item"
        :exact-active-class="'admin-sidebar__item--active'"
        :active-class="''"
        @click="emit('close')"
      >
        <span class="admin-sidebar__icon"><LayoutGrid /></span>
        Dashboard
      </RouterLink>

      <div class="admin-sidebar__section-label">Contenu</div>

      <RouterLink
        to="/admin/tracks"
        class="admin-sidebar__item"
        active-class="admin-sidebar__item--active"
        @click="emit('close')"
      >
        <span class="admin-sidebar__icon"><Music /></span>
        Musique
      </RouterLink>

      <RouterLink
        to="/admin/event"
        class="admin-sidebar__item"
        active-class="admin-sidebar__item--active"
        @click="emit('close')"
      >
        <span class="admin-sidebar__icon"><Calendar /></span>
        Événements
      </RouterLink>

      <RouterLink
        to="/admin/gallery"
        class="admin-sidebar__item"
        active-class="admin-sidebar__item--active"
        @click="emit('close')"
      >
        <span class="admin-sidebar__icon"><Image /></span>
        Galerie
      </RouterLink>

      <div class="admin-sidebar__section-label">Configuration</div>

      <RouterLink
        to="/admin/settings"
        class="admin-sidebar__item"
        active-class="admin-sidebar__item--active"
        @click="emit('close')"
      >
        <span class="admin-sidebar__icon"><Settings /></span>
        Paramètres
      </RouterLink>
    </nav>

    <div class="admin-sidebar__footer">
      <div class="admin-sidebar__user">
        <div class="admin-sidebar__avatar">{{ initials }}</div>
        <div>
          <div class="admin-sidebar__user-name">{{ auth.user?.surname || auth.user?.email }}</div>
        </div>
      </div>
      <button class="admin-sidebar__logout" @click="handleLogout">
        <LogOut />
        Déconnexion
      </button>
    </div>
  </aside>
</template>
