<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'
import { LayoutGrid, Music, Calendar, Image, Settings, Drum, LogOut } from '@lucide/vue'

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
      <div class="admin-sidebar__logo"><img src="/assets/images/logo.webp" alt="Logo"></div>
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

        <RouterLink
        to="/admin/musicians"
        class="admin-sidebar__item"
        active-class="admin-sidebar__item--active"
        @click="emit('close')"
      >
        <span class="admin-sidebar__icon"><Drum /></span>
        Musiciens
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

<style scoped lang="scss">
@use '../../../styles/utils/variables' as *;
@use '../../../styles/utils/breakpoints' as *;

$_sidebar-w: 240px;

.admin-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: $_sidebar-w;
  height: 100vh;
  background: $color-brown;
  color: $color-cream;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  z-index: 200;
  transform: translateX(-100%);
  transition: transform .3s;

  @media (min-width: $md) {
    transform: translateX(0);
  }

  &--open { transform: translateX(0); }

  &__header {
    padding: $size-24 $size-20 $size-16;
    border-bottom: 1px solid rgba(255, 255, 255, .08);
    display: flex;
    align-items: center;
    gap: $size-12;
    flex-shrink: 0;
  }

  &__logo {
    width: $size-56;
    height: $size-56;
    border-radius: 50%;
    background: $color-gold;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: $size-18;
    flex-shrink: 0;

    img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
  }

  &__brand {
    font-family: "Raleway", system-ui, sans-serif;
    font-weight: 800;
    font-size: $size-12;
    letter-spacing: .06em;
    text-transform: uppercase;
    line-height: 1.2;
    color: $color-cream;

    span {
      display: block;
      font-size: $size-10;
      font-weight: 600;
      color: $color-gold;
      letter-spacing: .1em;
    }
  }

  &__section-label {
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-10;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .35);
    padding: $size-20 $size-20 $size-6;
  }

  &__nav {
    flex: 1;
    padding-bottom: $size-16;
  }

  &__item {
    display: flex;
    align-items: center;
    gap: $size-12;
    padding: $size-10 $size-20;
    color: rgba(255, 255, 255, .6);
    font-family: "Raleway", system-ui, sans-serif;
    font-weight: 600;
    font-size: $size-14;
    letter-spacing: .03em;
    transition: background .2s, color .2s;
    cursor: pointer;
    border-left: 3px solid transparent;
    text-decoration: none;

    &:hover {
      background: rgba(255, 255, 255, .06);
      color: $color-cream;
    }

    &--active,
    &.router-link-exact-active {
      background: rgba($color-gold, .15);
      color: $color-gold-light;
      border-left-color: $color-gold;
    }
  }

  &__icon {
    width: $size-20;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;

    svg { width: $size-16; height: $size-16; }
  }

  &__footer {
    padding: $size-16 $size-20;
    border-top: 1px solid rgba(255, 255, 255, .08);
    flex-shrink: 0;
  }

  &__user {
    display: flex;
    align-items: center;
    gap: $size-12;
  }

  &__avatar {
    width: $size-36;
    height: $size-36;
    border-radius: 50%;
    background: var(--secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Raleway", system-ui, sans-serif;
    font-weight: 700;
    font-size: $size-14;
    color: $color-cream;
    flex-shrink: 0;
  }

  &__user-name {
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-12;
    font-weight: 700;
    color: $color-cream;
    line-height: 1.2;
  }

  &__logout {
    margin-top: $size-12;
    display: flex;
    align-items: center;
    gap: $size-8;
    width: 100%;
    padding: $size-8 $size-12;
    background: rgba(255, 255, 255, .05);
    border: 1px solid rgba(255, 255, 255, .1);
    border-radius: $size-6;
    color: rgba(255, 255, 255, .6);
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-12;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;

    svg { width: $size-14; height: $size-14; }

    &:hover {
      background: rgba($color-red, .2);
      border-color: rgba($color-red, .4);
      color: $color-cream;
    }
  }
}
</style>
