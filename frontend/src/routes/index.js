import { createRouter, createWebHistory } from 'vue-router'
import Cookies from 'js-cookie'

const getToken = () => Cookies.get('token')
const routes = [
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../views/dashboard/Index.vue'),
        meta: {
            requiresAuth: true,
            title: 'Dashboard',
        }
    },
    {
        path: '/tickets',
        name: 'ticket.index',
        component: () => import('../views/ticket/Index.vue'),
        meta: {
            requiresAuth: true,
            title: 'Tickets'
        }
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/auth/Login.vue'),
        meta: {
            title: 'Login',
            bodyClass: 'bg-primary'
        }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../views/errors/notFound.vue')
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title + ' - IT Helpdesk'
    // Ambil token otentikasi pengguna
    const token = getToken();

    // Jika rute tujuan membutuhkan otentikasi dan pengguna tidak memiliki token
    if (to.matched.some(record => record.meta.requiresAuth) && !token) {
        // Alihkan pengguna ke halaman login
        next({
            name: 'login'
        });
    }
    // Jika rute tujuan adalah halaman login atau register dan pengguna sudah login
    else if ((to.name === 'login' || to.name === 'register') && token) {
        // Alihkan pengguna ke halaman dashboard
        next({
            name: 'dashboard'
        });
    }
    // Jika tidak ada kondisi khusus, izinkan navigasi ke rute tujuan
    else {
        next();
    }
})

router.afterEach((to, from) => {
    document.body.className = to.meta.bodyClass || 'sb-nav-fixed'
})

export default router;
