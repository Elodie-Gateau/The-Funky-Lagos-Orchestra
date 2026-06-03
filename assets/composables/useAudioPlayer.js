import { ref } from 'vue'

const TOTAL_DURATION = 30000
const FADE_DURATION = 4000
const FADE_INTERVAL = 50

const playingTrackId = ref(null)
const audioInstance = ref(null)
let stopTimer = null
let fadeTimer = null

function clearTimers() {
    if (stopTimer) { clearTimeout(stopTimer); stopTimer = null }
    if (fadeTimer) { clearInterval(fadeTimer); fadeTimer = null }
}

function stopCurrent() {
    clearTimers()
    if (audioInstance.value) {
        audioInstance.value.pause()
        audioInstance.value = null
    }
    playingTrackId.value = null
}

function startFadeOut() {
    if (!audioInstance.value) return
    const steps = FADE_DURATION / FADE_INTERVAL
    const volumeStep = audioInstance.value.volume / steps

    fadeTimer = setInterval(() => {
        if (!audioInstance.value) { clearTimers(); return }
        const next = audioInstance.value.volume - volumeStep
        if (next <= 0) {
            stopCurrent()
        } else {
            audioInstance.value.volume = next
        }
    }, FADE_INTERVAL)
}

export function useAudioPlayer() {
    function togglePlay(track) {
        console.log(track.audioFile)
        if (playingTrackId.value === track.id) {
            stopCurrent()
        } else {
            stopCurrent()
            audioInstance.value = new Audio(track.audioFile)
            audioInstance.value.play().catch(() => stopCurrent())
            playingTrackId.value = track.id

            stopTimer = setTimeout(startFadeOut, TOTAL_DURATION - FADE_DURATION)
            audioInstance.value.addEventListener('ended', stopCurrent, { once: true })
        }
    }

    return { playingTrackId, togglePlay }
}
