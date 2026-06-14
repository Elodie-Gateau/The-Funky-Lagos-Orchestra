<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import Chart from 'chart.js/auto'
import {formatDate} from "../../../composables/dateFormat.js";

const { t } = useI18n()

const loading = ref(true)
const error = ref(false)
const canvasRef = ref(null)
let chartInstance = null

onMounted(async () => {
    try {
        const res = await fetch('/api/admin/stats/visits', {
            credentials: 'include',
        })

        if (!res.ok) {
            throw new Error('request failed')
        }

        const json = await res.json()
        renderChart(json.data ?? [])
    } catch {
        error.value = true
    } finally {
        loading.value = false
    }
})

onBeforeUnmount(() => {
    chartInstance?.destroy()
})

function renderChart(rows) {
    if (!canvasRef.value || rows.length === 0) return

    const labels = rows.map((row) => formatDate(row.date))
    const pageViews = rows.map((row) => row.pageViews)
    const activeUsers = rows.map((row) => row.activeUsers)

    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Vues',
                    data: pageViews,
                    borderColor: '#BE8A2D',
                    backgroundColor: 'rgba(190, 138, 45, 0.15)',
                    tension: 0.3,
                    fill: true,
                },
                {
                    label: 'Visiteurs uniques',
                    data: activeUsers,
                    borderColor: '#517085',
                    backgroundColor: 'rgba(81, 112, 133, 0.15)',
                    tension: 0.3,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                },
            },
        },
    })
}
</script>

<template>
    <div class="analytics-card">
        <h2>Statistiques de visites (30 derniers jours)</h2>

        <p v-if="loading" class="analytics-card_state">Chargement...</p>
        <p v-else-if="error" class="analytics-card_state analytics-card_state--error">
            Impossible de charger les statistiques
        </p>
        <p v-else-if="!chartInstance" class="analytics-card_state">
            Pas encore de données disponibles
        </p>

        <div class="analytics-card_chart" v-show="!loading && !error">
            <canvas ref="canvasRef"></canvas>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

.analytics-card {
    background-color: $color-light;
    border-radius: $size-8;
    padding: $size-24;

    h2 {
        margin: 0 0 $size-16 0;
        font-size: $size-20;
        color: $color-text;
    }

    &_state {
        color: $color-gray-500;
        font-size: $size-14;

        &--error {
            color: $color-error;
        }
    }

    &_chart {
        position: relative;
        height: 300px;
        width: 100%;
    }
}
</style>
