<script setup>
import { ref, onMounted } from 'vue'
import { Pause, Play, Trash2, X, Plus, Pencil, Disc3 } from '@lucide/vue'
import AdminLayout from '../../components/admin/layout/AdminLayout.vue'
import TrackForm from '../../components/admin/ui/TrackForm.vue'
import AlbumForm from '../../components/admin/ui/AlbumForm.vue'
import { useAudioPlayer } from '../../composables/useAudioPlayer.js'
import {Icon} from "@iconify/vue";

const { playingTrackId, togglePlay } = useAudioPlayer()
const tracks = ref([])
const albums = ref([])
const showModal = ref(false)
const modalType = ref(null)
const editingTrack = ref(null)
const editingAlbum = ref(null)

onMounted(async () => {
  const [resT, resA] = await Promise.all([
    fetch('/api/tracks', { credentials: 'include' }),
    fetch('/api/albums', { credentials: 'include' })
  ])
  const [dataT, dataA] = await Promise.all([resT.json(), resA.json()])
  tracks.value = dataT.tracks
  albums.value = dataA.albums
})

const refreshData = async () => {
  const [resT, resA] = await Promise.all([
    fetch('/api/tracks', { credentials: 'include' }),
    fetch('/api/albums', { credentials: 'include' })
  ])
  const [dataT, dataA] = await Promise.all([resT.json(), resA.json()])
  tracks.value = dataT.tracks
  albums.value = dataA.albums
}

function openTrackModal(track = null, album = null) {
  if (track) track.album = album
  editingTrack.value = track
  modalType.value = 'track'
  showModal.value = true
}

function openAlbumModal(album = null) {
  editingAlbum.value = album
  modalType.value = 'album'
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingTrack.value = null
  editingAlbum.value = null
  modalType.value = null
}

const onSaved = async () => {
  closeModal()
  await refreshData()
}

const deleteAlbum = async (id) => {
  await fetch(`/api/admin/album/${id}/delete`, { method: 'DELETE', credentials: 'include' })
  await refreshData()
}

const deleteTrack = async (id) => {
  await fetch(`/api/admin/track/${id}/delete`, { method: 'DELETE', credentials: 'include' })
  await refreshData()
}
</script>

<template>
  <AdminLayout page-title="Musique">
    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-header__title">Musique</h1>
        <p class="admin-page-header__sub">Gérez les albums et morceaux diffusés sur le site</p>
      </div>
      <div class="header-actions">
        <button class="admin-btn admin-btn--secondary" @click="openAlbumModal()">
          <Plus /> Album
        </button>
        <button class="admin-btn admin-btn--primary" @click="openTrackModal()">
          <Plus /> Morceau
        </button>
      </div>
    </div>

    <div v-if="albums.length === 0" class="admin-card empty-card">
      <p>Aucun album. Commencez par créer un album.</p>
    </div>

    <div v-else class="admin-card">
      <div v-for="album in albums" :key="album.id" class="admin-album">
        <div class="admin-album__header">
          <div class="admin-album__cover">
            <img v-if="album.cover" :src="album.cover" :alt="album.name" />
            <Disc3 v-else />
          </div>
          <div class="admin-album__info">
            <div class="admin-album__name">{{ album.name }}</div>
            <div class="admin-album__year">{{ album.year }}</div>
          </div>
          <div class="admin-album__actions">
            <button
              class="admin-btn admin-btn--secondary admin-btn--sm"
              @click="openTrackModal(null, album)"
              title="Ajouter un morceau à cet album"
            >
              <Plus /> Morceau
            </button>
            <button
              class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm"
              title="Modifier l'album"
              @click="openAlbumModal(album)"
            ><Icon icon="basil:edit-outline"/></button>
            <button
              class="admin-btn admin-btn--danger admin-btn--icon admin-btn--sm"
              title="Supprimer l'album"
              @click="deleteAlbum(album.id)"
            ><Icon icon="mdi:trash"/></button>
          </div>
        </div>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th></th>
                <th>Titre</th>
                <th>Artiste</th>
                <th>Durée</th>
                <th>Visible sur la home</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="track in album.tracks" :key="track.id">
                <td class="play-cell">
                  <button
                    class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm play-btn"
                    @click="togglePlay(track)"
                  >
                    <Pause v-if="playingTrackId === track.id" />
                    <Play v-else />
                  </button>
                </td>
                <td data-label="Titre">
                  <div class="admin-table__title-cell">
                    <div>
                      {{ track.title }}
                    </div>
                  </div>
                </td>
                <td data-label="Artiste">{{ track.artist }}</td>
                <td data-label="Durée">{{ track.duration }}</td>
                <td data-label="Visible">
                  <span
                    class="admin-badge"
                    :class="track.isVisible ? 'admin-badge--success' : 'admin-badge--warning'"
                  >
                    {{ track.isVisible ? 'Visible' : 'Masqué' }}
                  </span>
                </td>
                <td data-label="Actions">
                  <div class="admin-table__actions">
                    <button
                      class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm"
                      title="Modifier"
                      @click="openTrackModal(track, album)"
                    >
                        <Icon icon="basil:edit-outline"/>
                    </button>
                    <button
                      class="admin-btn admin-btn--danger admin-btn--icon admin-btn--sm"
                      title="Supprimer"
                      @click="deleteTrack(track.id)"
                    > <Icon icon="mdi:trash"/>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!album.tracks?.length">
                <td colspan="6" class="empty-row">Aucun morceau dans cet album</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="showModal" class="admin-modal-backdrop" @click.self="closeModal">
        <div class="admin-modal">
          <div class="admin-modal__header">
            <span class="admin-modal__title">
              <template v-if="modalType === 'album'">
                {{ editingAlbum ? "Modifier l'album" : 'Ajouter un album' }}
              </template>
              <template v-else>
                {{ editingTrack ? 'Modifier le morceau' : 'Ajouter un morceau' }}
              </template>
            </span>
            <button class="admin-modal__close" @click="closeModal"><X /></button>
          </div>
          <div class="admin-modal__body">
            <TrackForm
              v-if="modalType === 'track'"
              :track="editingTrack"
              :albums="albums"
              @close="onSaved"
            />
            <AlbumForm
              v-if="modalType === 'album'"
              :album="editingAlbum"
              @close="onSaved"
            />
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<style scoped>
.header-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.play-cell {
  width: 52px;
  padding-right: 0 !important;
}

.play-btn {
  border-radius: 50% !important;
}

.empty-row {
  text-align: center;
  padding: 1.5rem 1.25rem !important;
  color: #838383;
  font-style: italic;
}

.empty-card {
  padding: 2rem;
  text-align: center;
  color: #838383;
}

@media (max-width: 768px) {
  .play-cell { display: none !important; }
}
</style>
