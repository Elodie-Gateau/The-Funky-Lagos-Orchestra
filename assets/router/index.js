import {createRouter, createWebHistory} from 'vue-router'
import HomeView from '../views/HomeView.vue'
import {useAuthStore} from "../stores/auth.js";
import MusicView from "../views/MusicView.vue";
import i18n from "../i18n/index.js";

const routes = [
        {
            path: '/',
            component: HomeView,
            meta: { title: 'The Funky Lagos Orchestra' }
        },
        {
            path: '/login',
            component: () => import('../views/LoginView.vue'),
            meta: { title: 'Connexion — The Funky Lagos Orchestra' }
        },
        {
            path: '/admin',
            component: () => import('../views/admin/AdminView.vue'),
            meta: { requiresAuth: true, title: 'Admin — The Funky Lagos Orchestra'}
        },
        {
            path: '/:locale/',
            component: HomeView,
        },
        {
            path: '/music/',
            component: () => import('../views/MusicView.vue'),
            meta: { titleKey: 'music.meta_title' }
        },
        {
            path: '/admin/tracks',
            component: () => import('../views/admin/ManageMusicView.vue'),
            meta: { requiresAuth: true, title: 'Admin — The Funky Lagos Orchestra' }
        },
        {
            path: '/admin/event',
            component: () => import('../views/admin/ManageEventsView.vue'),
            meta: { requiresAuth: true, title: 'Admin — The Funky Lagos Orchestra' }
        },
        {
            path: '/admin/gallery',
            component: () => import('../views/admin/ManageGalleryView.vue'),
            meta: { requiresAuth: true, title: 'Admin — The Funky Lagos Orchestra' }
        },
        {
            path: '/admin/musicians',
            component: () => import('../views/admin/ManageMusiciansView.vue'),
            meta: { requiresAuth: true, title: 'Admin — The Funky Lagos Orchestra' }
        },
        {
            path: '/admin/settings',
            component: () => import('../views/admin/ManageSettingsView.vue'),
            meta: { requiresAuth: true , title: 'Admin — The Funky Lagos Orchestra'}
        }
    ]

const router = createRouter({history: createWebHistory(), routes })

router.beforeEach(async (to) => {
    // Gestion du titre avec i18n
    if (to.meta.titleKey) {
        document.title = `${i18n.global.t(to.meta.titleKey)} — The Funky Lagos Orchestra`
    } else if (to.meta.title) {
        document.title = to.meta.title
    }

    if (to.meta.requiresAuth) {
        const auth = useAuthStore()
        await auth.checkAuth()
        if (!auth.isAuthenticated) {
            return { path: '/login' }
        }
    }
})

export default router
