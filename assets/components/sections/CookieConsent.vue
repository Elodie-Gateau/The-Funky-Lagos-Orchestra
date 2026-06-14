<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const visible = ref(false)
const STORAGE_KEY = 'cookie_consent'
const GA_MEASUREMENT_ID = 'G-ZE6FGJ0556'

onMounted(() => {
    const consent = localStorage.getItem(STORAGE_KEY)

    if (consent === 'accepted') {
        loadGoogleAnalytics()
    } else if (consent !== 'rejected') {
        visible.value = true
    }
})

function loadGoogleAnalytics() {
    if (window.gtag) return

    const script = document.createElement('script')
    script.async = true
    script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`
    document.head.appendChild(script)

    window.dataLayer = window.dataLayer || []
    window.gtag = function () {
        window.dataLayer.push(arguments)
    }
    window.gtag('js', new Date())
    window.gtag('config', GA_MEASUREMENT_ID)
}

function accept() {
    localStorage.setItem(STORAGE_KEY, 'accepted')
    visible.value = false
    loadGoogleAnalytics()
}

function reject() {
    localStorage.setItem(STORAGE_KEY, 'rejected')
    visible.value = false
}
</script>

<template>
    <div v-if="visible" class="cookie-consent">
        <p class="cookie-consent_text">
            {{ t('cookieConsent.text') }}
        </p>
        <div class="cookie-consent_actions">
            <button class="cookie-consent_btn cookie-consent_btn--reject" @click="reject">
                {{ t('cookieConsent.reject') }}
            </button>
            <button class="cookie-consent_btn cookie-consent_btn--accept" @click="accept">
                {{ t('cookieConsent.accept') }}
            </button>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

.cookie-consent {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1000;

    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: $size-16;

    padding: $size-16 $size-24;
    background-color: $color-text;
    color: $color-light;

    &_text {
        flex: 1 1 280px;
        margin: 0;
        font-size: $size-14;
        line-height: 1.4;
    }

    &_actions {
        display: flex;
        gap: $size-12;
        flex-shrink: 0;
    }

    &_btn {
        padding: $size-8 $size-20;
        border: 1px solid $color-light;
        border-radius: $size-4;
        background: transparent;
        color: $color-light;
        font-size: $size-14;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;

        &:hover {
            background-color: $color-light;
            color: $color-text;
        }

        &--accept {
            background-color: $color-accent;
            border-color: $color-accent;
            color: $color-text;

            &:hover {
                background-color: darken($color-accent, 10%);
            }
        }
    }
}

@media (max-width: 600px) {
    .cookie-consent {
        flex-direction: column;
        align-items: stretch;
        text-align: center;

        &_actions {
            justify-content: center;
        }
    }
}
</style>
