import { ref } from 'vue'

export function useAudioPlayer() {
    const playingTrackId = ref(null)
    const audioInstance = ref(null)

    function togglePlay(track) {
        if (playingTrackId.value === track.id) {
            audioInstance.value.pause()
            playingTrackId.value = null
        } else {
            if (audioInstance.value) audioInstance.value.pause()
            audioInstance.value = new Audio(track.audioFile)
            audioInstance.value.play()
            playingTrackId.value = track.id
        }
    }

    return { playingTrackId, togglePlay }
}
