<script setup>
import { ref, onMounted } from 'vue'
import { X, Pencil, Trash2, Plus, MapPin } from '@lucide/vue'
import AdminLayout from '../../components/admin/layout/AdminLayout.vue'
import EventForm from '../../components/admin/ui/EventForm.vue'

const events = ref([])
const showModal = ref(false)
const editingEvent = ref(null)

onMounted(async () => {
  const res = await fetch('/api/events', { credentials: 'include' })
  const data = await res.json()
  events.value = data.events
})

const refreshEvents = async () => {
  const res = await fetch('/api/events', { credentials: 'include' })
  const data = await res.json()
  events.value = data.events
}

const deleteEvent = async (id) => {
  await fetch(`/api/admin/event/${id}/delete`, { method: 'DELETE', credentials: 'include' })
  events.value = events.value.filter(e => e.id !== id)
}

function openModal(event = null) {
  editingEvent.value = event
  showModal.value = true
}
</script>

<template>
  <AdminLayout page-title="Événements">
    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-header__title">Événements</h1>
        <p class="admin-page-header__sub">Gérez les concerts et apparitions du groupe</p>
      </div>
      <button class="admin-btn admin-btn--primary" @click="openModal()">
        <Plus /> Ajouter
      </button>
    </div>

    <div class="admin-card">
      <div class="admin-card__header">
        <span class="admin-card__title">
          Tous les événements
          <span class="count">({{ events.length }})</span>
        </span>
      </div>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Événement</th>
              <th>Lieu</th>
              <th>Ville</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="event in events" :key="event.id">
              <td data-label="Date"><strong>{{ event.date }}</strong></td>
              <td data-label="Événement">{{ event.name }}</td>
              <td data-label="Lieu">
                <span class="venue-cell">
                  <MapPin :size="12" />{{ event.location }}
                </span>
              </td>
              <td data-label="Ville">{{ event.city }}</td>
              <td data-label="Actions">
                <div class="admin-table__actions">
                  <button
                    class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm"
                    title="Modifier"
                    @click="openModal(event)"
                  ><Pencil /></button>
                  <button
                    class="admin-btn admin-btn--danger admin-btn--icon admin-btn--sm"
                    title="Supprimer"
                    @click="deleteEvent(event.id)"
                  ><Trash2 /></button>
                </div>
              </td>
            </tr>
            <tr v-if="!events.length">
              <td colspan="5" class="empty-row">Aucun événement pour l'instant</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="showModal" class="admin-modal-backdrop" @click.self="showModal = false">
        <div class="admin-modal">
          <div class="admin-modal__header">
            <span class="admin-modal__title">
              {{ editingEvent ? 'Modifier le concert' : 'Ajouter un concert' }}
            </span>
            <button class="admin-modal__close" @click="showModal = false">
              <X />
            </button>
          </div>
          <div class="admin-modal__body">
            <EventForm
              :event="editingEvent"
              @close="showModal = false; refreshEvents()"
            />
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<style scoped>
.count {
  color: #838383;
  font-weight: 400;
}
.venue-cell {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.empty-row {
  text-align: center;
  padding: 2rem 1.25rem !important;
  color: #838383;
  font-style: italic;
}
</style>
