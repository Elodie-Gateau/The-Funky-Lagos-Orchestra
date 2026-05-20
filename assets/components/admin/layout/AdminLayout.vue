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
