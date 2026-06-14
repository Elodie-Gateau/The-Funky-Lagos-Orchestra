<script setup>

import AdminLayout from "../../components/admin/layout/AdminLayout.vue";
import ConfirmModal from "../../components/admin/ui/ConfirmModal.vue";
import EventForm from "../../components/admin/ui/EventForm.vue";
import { Pencil, Plus, Trash2, X} from "@lucide/vue";
import {onMounted, ref} from "vue";
import {useConfirm} from "../../composables/useConfirm.js";
import MusicianForm from "../../components/admin/ui/MusicianForm.vue";

const musicians = ref([])
const showModal = ref(false)
const editingMusician = ref(null)
const {confirmState, confirm, onConfirm, onCancel} = useConfirm()

onMounted(async () => {
    const res = await fetch('/api/musicians', {credentials: 'include'})
    const data = await res.json()
    musicians.value = data.musicians
})

const refreshMusicians = async () => {
    const res = await fetch('/api/musicians', { credentials: 'include' })
    const data = await res.json()
    musicians.value = data.musicians
}

const deleteMusician = async (musician) => {
    const ok = await confirm(`Retirer ${musician.fullName} du groupe ?`)
    if (!ok) return
    await fetch(`/api/admin/musician/delete/${musician.id}`, { method: 'DELETE', credentials: 'include' })
    musicians.value = musicians.value.filter(m => m.id !== musician.id)
}

function openModal(musician = null) {
    editingMusician.value = musician
    showModal.value = true
}
</script>

<template>
    <AdminLayout page-title="Musiciens">
        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-header__title">Musiciens</h1>
                <p class="admin-page-header__sub">Gérez les musiciens membres du groupe</p>
            </div>
            <button class="admin-btn admin-btn--primary" @click="openModal()">
                <Plus /> Ajouter
            </button>
        </div>

        <div class="admin-card">
            <div class="admin-card__header">
        <span class="admin-card__title">
          Tous les musiciens
          <span class="count">({{ musicians.length }})</span>
        </span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Instrument</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="musician in musicians" :key="musician.id">
                            <td data-label="Nom"><strong>{{ musician.fullName }}</strong></td>
                            <td data-label="Instrument">{{ musician.instrument }}</td>
                            <td data-label="Actions">
                                <div class="admin-table__actions">
                                    <button
                                        class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm"
                                        title="Modifier"
                                        @click="openModal(musician)"
                                    ><Pencil /></button>
                                    <button
                                        class="admin-btn admin-btn--danger admin-btn--icon admin-btn--sm"
                                        title="Supprimer"
                                        @click="deleteMusician(musician)"
                                    ><Trash2 /></button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!musicians.length">
                            <td colspan="5" class="empty-row">Aucun musicien pour l'instant</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <ConfirmModal
            v-if="confirmState"
            :message="confirmState.message"
            @confirm="onConfirm"
            @cancel="onCancel"
        />

        <Teleport to="body">
            <div v-if="showModal" class="admin-modal-backdrop" @click.self="showModal = false">
                <div class="admin-modal">
                    <div class="admin-modal__header">
            <span class="admin-modal__title">
              {{ editingMusician ? 'Modifier le concert' : 'Ajouter un musicien' }}
            </span>
                        <button class="admin-modal__close" @click="showModal = false">
                            <X />
                        </button>
                    </div>
                    <div class="admin-modal__body">
                        <MusicianForm
                            :musician="editingMusician"
                            @close="showModal = false; refreshMusicians()"
                        />
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>

<style scoped lang="scss">

</style>
