import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Setup axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Setup Echo
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

// Router
const routes = [
    { path: '/vue', redirect: '/vue/chat' },
    { path: '/vue/login', name: 'login', component: () => import('./views/Login.vue'), meta: { guest: true } },
    { path: '/vue/register', name: 'register', component: () => import('./views/Register.vue'), meta: { guest: true } },
    { path: '/vue/chat', name: 'chat', component: () => import('./views/Chat.vue'), meta: { auth: true } },
    { path: '/vue/chat/:id', name: 'chat.show', component: () => import('./views/Chat.vue'), meta: { auth: true } },
    { path: '/vue/admin', name: 'admin', component: () => import('./views/Admin.vue'), meta: { auth: true, admin: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard
router.beforeEach(async (to, from, next) => {
    const app = router.app;
    if (to.meta.auth && !window.__user) {
        try {
            const res = await axios.get('/vue/user');
            window.__user = res.data.user;
        } catch {
            return next({ name: 'login' });
        }
    }
    if (to.meta.guest && window.__user) {
        return next({ name: 'chat' });
    }
    next();
});

const app = createApp(App);
app.use(router);
app.mount('#vue-app');
