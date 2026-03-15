import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const isAuthenticated = ref(false)

    async function checkAuth() {
        try {
            const res = await fetch('/api/me', { credentials: 'include' })
            if (res.ok) {
                const data = await res.json()
                if (data.authenticated) {
                    user.value = data
                    isAuthenticated.value = true
                    return
                }
            }
        } catch {}
        user.value = null
        isAuthenticated.value = false
    }

    async function login(email, password) {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ email, password})
        })
        if (res.ok) {
            await checkAuth()
            return true
        }
        return false
    }

    async function logout() {
        await fetch('/api/logout', {
            method: 'POST',
            credentials: 'include',
        })
        user.value = null
        isAuthenticated.value = false
    }

    return { user, isAuthenticated, login, logout, checkAuth }
})
