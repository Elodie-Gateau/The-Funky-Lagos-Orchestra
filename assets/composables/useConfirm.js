import { ref } from 'vue'

export function useConfirm() {
  const confirmState = ref(null)

  function confirm(message) {
    return new Promise((resolve) => {
      confirmState.value = { message, resolve }
    })
  }

  function onConfirm() {
    if (confirmState.value) {
      confirmState.value.resolve(true)
      confirmState.value = null
    }
  }

  function onCancel() {
    if (confirmState.value) {
      confirmState.value.resolve(false)
      confirmState.value = null
    }
  }

  return { confirmState, confirm, onConfirm, onCancel }
}
