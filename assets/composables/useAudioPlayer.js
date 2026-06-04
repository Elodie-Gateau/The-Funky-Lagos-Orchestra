import { ref } from 'vue'

const MAX_SECONDS = 30
const FADE_START = 26
const FADE_INTERVAL = 50

const playingTrackId = ref(null)
const audioInstance = ref(null)
const currentTime = ref(0)
let fadeTimer = null

function clearFade() {
    if (fadeTimer) { clearInterval(fadeTimer); fadeTimer = null }
}

function stopCurrent() {
    clearFade()
    if (audioInstance.value) {
        audioInstance.value.pause()
        audioInstance.value.src = ''
        audioInstance.value = null
    }
    playingTrackId.value = null
    currentTime.value = 0
}

export function useAudioPlayer() {
    function togglePlay(track) {
        if (playingTrackId.value === track.id) {
            stopCurrent()
            return
        }

        stopCurrent()

        const audio = new Audio(track.audioFile)
        audioInstance.value = audio

        audio.addEventListener('timeupdate', () => {
            currentTime.value = Math.floor(audio.currentTime)

            if (audio.currentTime >= MAX_SECONDS) {
                stopCurrent()
                return
            }

            if (audio.currentTime >= FADE_START && !fadeTimer) {
                const startVolume = audio.volume
                const steps = (MAX_SECONDS - FADE_START) * 1000 / FADE_INTERVAL
                const volumeStep = startVolume / steps

                fadeTimer = setInterval(() => {
                    if (!audioInstance.value) { clearFade(); return }
                    const next = audioInstance.value.volume - volumeStep
                    if (next <= 0) {
                        stopCurrent()
                    } else {
                        audioInstance.value.volume = Math.max(0, next)
                    }
                }, FADE_INTERVAL)
            }
        })

        audio.addEventListener('ended', stopCurrent, { once: true })
        audio.play().catch(() => stopCurrent())
        playingTrackId.value = track.id
    }

    return { playingTrackId, togglePlay, currentTime }
}
