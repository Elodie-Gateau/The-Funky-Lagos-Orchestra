import {createRouter, createWebHistory} from 'vue-router'
import HomeView from '../views/HomeView.vue'
import {useAuthStore} from "../stores/auth.js";


const routes = [
        {
            path: '/',
            component: HomeView
        },
        {
            path: '/login',
            component: () => import('../views/LoginView.vue')
        },
        {
            path: '/admin',
            component: () => import('../views/admin/AdminView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/:locale/',
            component: HomeView
        },
        {
            path: '/admin/music',
            component: () => import('../views/admin/ManageMusicView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/admin/event',
            component: () => import('../views/admin/ManageEventsView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/admin/gallery',
            component: () => import('../views/admin/ManageGalleryView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/admin/settings',
            component: () => import('../views/admin/ManageSettingsView.vue'),
            meta: { requiresAuth: true }
        }
    ]

const router = createRouter({history: createWebHistory(), routes })

router.beforeEach(async (to) => {
    if (to.meta.requiresAuth) {
        const auth = useAuthStore()
        await auth.checkAuth()
        if (!auth.isAuthenticated) {
            return { path: '/login' }
        }
    }
})

export default router
