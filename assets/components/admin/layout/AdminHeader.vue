<script setup>
import {useRouter} from "vue-router";
import {useAuthStore} from "../../../stores/auth.js";

const router = useRouter();
const auth = useAuthStore()
async function handleLogout() {
    await auth.logout()
    router.push('/login')
}
</script>

<template>
    <h1>Espace Administrateur</h1>
    <p>Bonjour {{ auth.user?.surname }} !</p>
    <div class="admin-header">
        <span>{{ auth.user?.email }}</span>
        <button @click="handleLogout">Déconnexion</button>
    </div>
</template>

<style scoped>

</style>
