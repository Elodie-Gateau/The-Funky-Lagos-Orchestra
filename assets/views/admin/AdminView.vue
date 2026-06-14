<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth.js'
import AdminLayout from '../../components/admin/layout/AdminLayout.vue'
import { Music, Calendar, Image } from '@lucide/vue'
import {Icon} from "@iconify/vue";
import {formatDate} from "../../composables/dateFormat.js";
import AnalyticsChart from "../../components/admin/ui/AnalyticsChart.vue";

const auth = useAuthStore()
const tracks = ref([])
const events = ref([])
const photos = ref([])

onMounted(async () => {
  const [resT, resE, resP] = await Promise.all([
    fetch('/api/tracks', { credentials: 'include' }),
    fetch('/api/events', { credentials: 'include' }),
    fetch('/api/photos', { credentials: 'include' })
  ])
  const [dataT, dataE, dataP] = await Promise.all([resT.json(), resE.json(), resP.json()])
  tracks.value = dataT.tracks || []
  events.value = dataE.events || []
  photos.value = dataP.photos || []
})

const recentTracks = computed(() => tracks.value.slice(0, 4))
const upcomingEvents = computed(() => events.value.slice(0, 4))
</script>

<template>
  <AdminLayout page-title="Dashboard">
    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-header__title">Dashboard</h1>
        <p class="admin-page-header__sub">
          Bienvenue{{ auth.user?.surname ? `, ${auth.user.surname}` : '' }} — Aperçu général du site
        </p>
      </div>
    </div>

    <div class="admin-stats">
      <div class="admin-stat">
        <div class="admin-stat__info">
          <span class="admin-stat__label">Tracks</span>
          <span class="admin-stat__value">{{ tracks.length }}</span>
        </div>
        <div class="admin-stat__icon" style="background: #FEF3C7;">
          <Music />
        </div>
      </div>
      <div class="admin-stat">
        <div class="admin-stat__info">
          <span class="admin-stat__label">Événements</span>
          <span class="admin-stat__value">{{ events.length }}</span>
        </div>
        <div class="admin-stat__icon" style="background: #DBEAFE;">
          <Calendar />
        </div>
      </div>
      <div class="admin-stat">
        <div class="admin-stat__info">
          <span class="admin-stat__label">Photos</span>
          <span class="admin-stat__value">{{ photos.length }}</span>
        </div>
        <div class="admin-stat__icon" style="background: #D1FAE5;">
          <Image />
        </div>
      </div>
    </div>
      <div class="admin-stat analytics">
      <div class="admin-stat__info analytics">
          <span class="admin-stat__label">Vues</span>
          <span class="admin-stat__value"><AnalyticsChart /></span>
      </div>
  </div>
    <div class="dashboard-grid">
      <div class="admin-card">
        <div class="admin-card__header">
          <span class="admin-card__title">Derniers morceaux ajoutés</span>
          <RouterLink to="/admin/tracks" class="admin-btn admin-btn--secondary admin-btn--sm">
            Gérer →
          </RouterLink>
        </div>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Durée</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="track in recentTracks" :key="track.id">
                <td data-label="Titre">
                  <div class="admin-table__title-cell">
                    <div class="admin-table__thumb"><Icon icon="mingcute:play-fill" /></div>
                    <div>
                      {{ track.title }}
                      <span class="admin-table__sub">{{ track.artist }}</span>
                    </div>
                  </div>
                </td>
                <td data-label="Durée">{{ track.duration }}</td>
              </tr>
              <tr v-if="!recentTracks.length">
                <td colspan="2" class="empty-row">Aucun morceau</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="admin-card">
        <div class="admin-card__header">
          <span class="admin-card__title">Prochains événements</span>
          <RouterLink to="/admin/event" class="admin-btn admin-btn--secondary admin-btn--sm">
            Gérer →
          </RouterLink>
        </div>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Événement</th>
                <th>Ville</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in upcomingEvents" :key="event.id">
                <td data-label="Date"><strong>{{ formatDate(event.date) }}</strong></td>
                <td data-label="Événement">{{ event.name }}</td>
                <td data-label="Ville">{{ event.city }}</td>
              </tr>
              <tr v-if="!upcomingEvents.length">
                <td colspan="3" class="empty-row">Aucun événement</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped lang="scss">
@use '../../styles/utils/variables' as *;
@use '../../styles/utils/breakpoints' as *;

.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: $size-20;
    align-items: flex-start;

    .admin-card {
        margin-top: 0;
    }


  @media (min-width: $md) {
    grid-template-columns: 1fr 1fr;
  }
}
    .admin-stat.analytics {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: $size-24;
    }

.admin-stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: $size-16;
  margin-bottom: $size-24;

  @media (min-width: $md) {
    grid-template-columns: repeat(3, 1fr);
    gap: $size-20;
    margin-bottom: $size-32;
  }
}

.admin-stat {
  background: $color-white;
  border-radius: $size-8;
  padding: $size-16 $size-20;
  box-shadow: 0 2px 8px rgba($color-brown, .06);
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  border: 1px solid $color-cream-dark;
  gap: $size-12;

  @media (min-width: $md) {
    padding: $size-20 $size-24;
  }

  &__info { display: flex; flex-direction: column; gap: $size-4; }
    &__info.analytics {
        margin-bottom: $size-36;
        max-width: 700px;

    }

  &__label {
    font-family: "Raleway", system-ui, sans-serif;
    font-size: $size-10;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: $color-gray-300;
  }

  &__value {
    font-family: "Raleway", system-ui, sans-serif;
    font-weight: 800;
    font-size: $size-32;
    color: $color-brown;
    line-height: 1;
  }

  &__icon {
    width: $size-44;
    height: $size-44;
    border-radius: $size-6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;

    svg { width: $size-22; height: $size-22; }
  }
}
</style>
