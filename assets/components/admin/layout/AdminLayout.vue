<script setup>
import { ref } from 'vue'
import AdminMenu from './AdminMenu.vue'
import AdminHeader from './AdminHeader.vue'

defineProps({
  pageTitle: { type: String, default: '' },
  pageSubtitle: { type: String, default: '' }
})

const sidebarOpen = ref(false)
</script>

<style scoped lang="scss">
@use '../../../styles/utils/variables' as *;
@use '../../../styles/utils/breakpoints' as *;

$_sidebar-w: 240px;

.admin-layout {
  min-height: 100vh;
  background: $color-gray-100;
  color: $color-brown;
}

.admin-layout__overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, .5);
  z-index: 150;
  display: none;

  &--open { display: block; }
}

.admin-layout__body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;

  @media (min-width: $md) {
    margin-left: $_sidebar-w;
  }
}

.admin-layout__main {
  flex: 1;
  padding: $size-20 $size-16;

  @media (min-width: $md) {
    padding: $size-32;
  }
}
</style>

<template>
  <div class="admin-layout">
    <AdminMenu :is-open="sidebarOpen" @close="sidebarOpen = false" />
    <div
      v-if="sidebarOpen"
      class="admin-layout__overlay admin-layout__overlay--open"
      @click="sidebarOpen = false"
    />
    <div class="admin-layout__body">
      <AdminHeader
        :page-title="pageTitle"
        @toggle-sidebar="sidebarOpen = !sidebarOpen"
      />
      <main class="admin-layout__main">
        <slot />
      </main>
    </div>
  </div>
</template>
