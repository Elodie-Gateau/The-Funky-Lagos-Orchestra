<script setup>
import { ref, computed, onMounted } from 'vue'
import { Pause, Play, X, Plus, Disc3, GripVertical } from '@lucide/vue'
import AdminLayout from '../../components/admin/layout/AdminLayout.vue'
import TrackForm from '../../components/admin/ui/TrackForm.vue'
import AlbumForm from '../../components/admin/ui/AlbumForm.vue'
import ConfirmModal from '../../components/admin/ui/ConfirmModal.vue'
import { useAudioPlayer } from '../../composables/useAudioPlayer.js'
import { useConfirm } from '../../composables/useConfirm.js'
import { Icon } from '@iconify/vue'

const { playingTrackId, togglePlay } = useAudioPlayer()
const { confirmState, confirm, onConfirm, onCancel } = useConfirm()
const albums = ref([])
const showModal = ref(false)
const modalType = ref(null)
const editingTrack = ref(null)
const editingAlbum = ref(null)
const togglingTrackId = ref(null)

const homeDrag = ref({ from: null, over: null })
const albumDrag = ref({ albumId: null, from: null, over: null })

const visibleTracks = computed(() => {
  const all = []
  for (const album of albums.value) {
    for (const track of album.tracks) {
      if (track.isVisible) all.push(track)
    }
  }
  return all.sort((a, b) => (a.homePosition ?? 9999) - (b.homePosition ?? 9999))
})

const visibleCount = computed(() => visibleTracks.value.length)

async function loadData() {
  const res = await fetch('/api/albums', { credentials: 'include' })
  const data = await res.json()
  albums.value = data.albums
}

onMounted(loadData)

async function refreshData() {
  await loadData()
}

function openTrackModal(track = null, album = null) {
  if (track) track = { ...track, album: track.album ?? album }
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

async function onSaved() {
  closeModal()
  await refreshData()
}

async function deleteAlbum(album) {
  const ok = await confirm(`Supprimer l'album "${album.name}" et tous ses morceaux ?`)
  if (!ok) return
  const res = await fetch(`/api/admin/album/${album.id}/delete`, { method: 'DELETE', credentials: 'include' })
  if (!res.ok) {
    const data = await res.json()
    alert(data.error || 'Erreur')
    return
  }
  await refreshData()
}

async function deleteTrack(track) {
  const ok = await confirm(`Supprimer le morceau "${track.title}" ?`)
  if (!ok) return
  const res = await fetch(`/api/admin/track/${track.id}/delete`, { method: 'DELETE', credentials: 'include' })
  if (!res.ok) {
    const data = await res.json()
    alert(data.error || 'Erreur')
    return
  }
  await refreshData()
}

async function toggleVisibility(track) {
  if (togglingTrackId.value) return
  togglingTrackId.value = track.id
  try {
    const res = await fetch(`/api/admin/track/${track.id}/visibility`, {
      method: 'PATCH',
      credentials: 'include',
    })
    if (!res.ok) {
      const data = await res.json()
      alert(data.error || 'Erreur')
      return
    }
    await refreshData()
  } finally {
    togglingTrackId.value = null
  }
}

// ── Home drag & drop ─────────────────────────────────────────
function onHomeDragStart(e, index) {
  homeDrag.value.from = index
  e.dataTransfer.effectAllowed = 'move'
}

function onHomeDragOver(e, index) {
  e.preventDefault()
  e.dataTransfer.dropEffect = 'move'
  homeDrag.value.over = index
}

function onHomeDragLeave() {
  homeDrag.value.over = null
}

async function onHomeDrop(e, dropIndex) {
  e.preventDefault()
  const fromIndex = homeDrag.value.from
  homeDrag.value = { from: null, over: null }
  if (fromIndex === null || fromIndex === dropIndex) return

  const newOrder = [...visibleTracks.value]
  const [moved] = newOrder.splice(fromIndex, 1)
  newOrder.splice(dropIndex, 0, moved)

  await fetch('/api/admin/tracks/reorder', {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ ids: newOrder.map(t => t.id) }),
  })
  await refreshData()
}

function onHomeDragEnd() {
  homeDrag.value = { from: null, over: null }
}

// ── Album drag & drop ─────────────────────────────────────────
function onAlbumDragStart(e, albumId, index) {
  albumDrag.value = { albumId, from: index, over: null }
  e.dataTransfer.effectAllowed = 'move'
}

function onAlbumDragOver(e, albumId, index) {
  if (albumDrag.value?.albumId !== albumId) return
  e.preventDefault()
  e.dataTransfer.dropEffect = 'move'
  albumDrag.value = { ...albumDrag.value, over: index }
}

function onAlbumDragLeave(albumId) {
  if (albumDrag.value?.albumId === albumId) {
    albumDrag.value = { ...albumDrag.value, over: null }
  }
}

async function onAlbumDrop(e, albumId, dropIndex) {
  if (albumDrag.value?.albumId !== albumId) return
  e.preventDefault()
  const fromIndex = albumDrag.value.from
  albumDrag.value = { albumId: null, from: null, over: null }
  if (fromIndex === dropIndex) return

  const album = albums.value.find(a => a.id === albumId)
  const newOrder = [...album.tracks]
  const [moved] = newOrder.splice(fromIndex, 1)
  newOrder.splice(dropIndex, 0, moved)

  await fetch(`/api/admin/album/${albumId}/tracks/reorder`, {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ ids: newOrder.map(t => t.id) }),
  })
  await refreshData()
}

function onAlbumDragEnd() {
  albumDrag.value = { albumId: null, from: null, over: null }
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

    <!-- Morceaux visibles sur la homepage -->
    <div class="admin-card">
      <div class="admin-card__header">
        <span class="admin-card__title">
          Morceaux sur la homepage
          <span class="count">({{ visibleCount }}/6)</span>
        </span>
        <span v-if="visibleCount >= 6" class="admin-badge admin-badge--warning">
          Maximum atteint
        </span>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th class="drag-th"></th>
              <th>Titre</th>
              <th>Album</th>
              <th>Durée</th>
              <th>Visible</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(track, index) in visibleTracks"
              :key="track.id"
              draggable="true"
              :class="{ 'drag-over': homeDrag.over === index && homeDrag.from !== index }"
              @dragstart="onHomeDragStart($event, index)"
              @dragover="onHomeDragOver($event, index)"
              @dragleave="onHomeDragLeave"
              @drop="onHomeDrop($event, index)"
              @dragend="onHomeDragEnd"
            >
              <td class="drag-cell">
                <span class="drag-handle"><GripVertical /></span>
              </td>
              <td data-label="Titre">
                <div class="admin-table__title-cell">{{ track.title }}</div>
              </td>
              <td data-label="Album">{{ track.album?.name }}</td>
              <td data-label="Durée">{{ track.duration }}</td>
              <td data-label="Visible">
                <button
                  type="button"
                  class="admin-toggle admin-toggle--on"
                  :disabled="togglingTrackId === track.id"
                  title="Masquer ce morceau"
                  @click="toggleVisibility(track)"
                >
                  <span class="admin-toggle__thumb"></span>
                </button>
              </td>
              <td data-label="Actions">
                <div class="admin-table__actions">
                  <button
                    class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm"
                    title="Modifier"
                    @click="openTrackModal(track, track.album)"
                  >
                    <Icon icon="basil:edit-outline" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!visibleTracks.length">
              <td colspan="6" class="empty-row">Aucun morceau visible sur la homepage</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Albums -->
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
              title="Ajouter un morceau à cet album"
              @click="openTrackModal(null, album)"
            >
              <Plus /> Morceau
            </button>
            <button
              class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm"
              title="Modifier l'album"
              @click="openAlbumModal(album)"
            ><Icon icon="basil:edit-outline" /></button>
            <button
              class="admin-btn admin-btn--danger admin-btn--icon admin-btn--sm"
              title="Supprimer l'album"
              @click="deleteAlbum(album)"
            ><Icon icon="mdi:trash" /></button>
          </div>
        </div>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th class="drag-th"></th>
                <th class="play-th"></th>
                <th>Titre</th>
                <th>Durée</th>
                <th>Homepage</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(track, index) in album.tracks"
                :key="track.id"
                draggable="true"
                :class="{ 'drag-over': albumDrag.albumId === album.id && albumDrag.over === index && albumDrag.from !== index }"
                @dragstart="onAlbumDragStart($event, album.id, index)"
                @dragover="onAlbumDragOver($event, album.id, index)"
                @dragleave="onAlbumDragLeave(album.id)"
                @drop="onAlbumDrop($event, album.id, index)"
                @dragend="onAlbumDragEnd"
              >
                <td class="drag-cell">
                  <span class="drag-handle"><GripVertical /></span>
                </td>
                <td class="play-cell">
                  <button
                    class="admin-btn admin-btn--secondary admin-btn--icon play-btn"
                    @click="togglePlay(track)"
                  >
                    <Pause v-if="playingTrackId === track.id" />
                    <Play v-else />
                  </button>
                </td>
                <td data-label="Titre">
                  <div class="admin-table__title-cell">{{ track.title }}</div>
                </td>
                <td data-label="Durée">{{ track.duration }}</td>
                <td data-label="Homepage">
                  <button
                    type="button"
                    class="admin-toggle"
                    :class="{ 'admin-toggle--on': track.isVisible }"
                    :disabled="(!track.isVisible && visibleCount >= 6) || togglingTrackId === track.id"
                    :title="!track.isVisible && visibleCount >= 6 ? 'Maximum 6 morceaux atteint' : (track.isVisible ? 'Masquer' : 'Afficher sur la homepage')"
                    @click="toggleVisibility(track)"
                  >
                    <span class="admin-toggle__thumb"></span>
                  </button>
                </td>
                <td data-label="Actions">
                  <div class="admin-table__actions">
                    <button
                      class="admin-btn admin-btn--secondary admin-btn--icon admin-btn--sm"
                      title="Modifier"
                      @click="openTrackModal(track, album)"
                    >
                      <Icon icon="basil:edit-outline" />
                    </button>
                    <button
                      class="admin-btn admin-btn--danger admin-btn--icon admin-btn--sm"
                      title="Supprimer"
                      @click="deleteTrack(track)"
                    >
                      <Icon icon="mdi:trash" />
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

    <ConfirmModal
      v-if="confirmState"
      :message="confirmState.message"
      @confirm="onConfirm"
      @cancel="onCancel"
    />

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

<style scoped lang="scss">
@use '../../styles/utils/variables' as *;
@use '../../styles/utils/breakpoints' as *;

.header-actions {
  display: flex;
  gap: $size-8;
  flex-wrap: wrap;
}

.drag-th { width: 36px; padding-right: 0 !important; }
.play-th { width: 52px; padding-right: 0 !important; }
.drag-cell { width: 36px; padding-right: 0 !important; }
.play-cell { width: 52px; padding-right: 0 !important; }
.play-btn { border-radius: 50% !important; }

.drag-handle {
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: grab;
  color: $color-gray-300;
  padding: $size-4;
  border-radius: $size-4;
  transition: color .15s;

  &:active { cursor: grabbing; }
  svg { width: $size-16; height: $size-16; }

  &:hover { color: $color-brown-mid; }
}

tr.drag-over td {
  background: rgba($color-gold, .1) !important;
}

tr.drag-over td:first-child {
  border-left: 3px solid $color-gold;
}

.empty-card {
  padding: $size-32;
  text-align: center;
  color: $color-gray-300;
}

@media (max-width: $md) {
  .drag-cell,
  .play-cell { display: none !important; }
}

.admin-album {
  & + .admin-album { border-top: 2px solid $color-cream-dark; }

  &__header {
    display: flex;
    align-items: center;
    gap: $size-14;
    padding: $size-12 $size-20;
    background: $color-cream;

    @media (min-width: $md) {
      padding: $size-14 $size-24;
    }
  }

  &__cover {
    width: $size-44;
    height: $size-44;
    border-radius: $size-6;
    background: linear-gradient(135deg, $color-brown, $color-brown-mid);
    overflow: hidden;
    flex-shrink: 0;

    img { width: 100%; height: 100%; object-fit: cover; }
  }

  &__info { flex: 1; min-width: 0; }

  &__name {
    font-family: "Raleway", system-ui, sans-serif;
    font-weight: 700;
    font-size: $size-16;
    color: $color-brown;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__year {
    font-size: $size-12;
    color: $color-gray-300;
  }

  &__actions {
    display: flex;
    gap: $size-6;
    flex-shrink: 0;
  }
}
</style>
