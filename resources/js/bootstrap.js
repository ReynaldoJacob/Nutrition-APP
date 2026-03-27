import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Forzar HTTPS en producción para evitar Mixed Content
if (window.location.protocol === 'https:') {
    window.axios.defaults.baseURL = window.location.origin;
}

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const env = import.meta.env;
const runtime = window.__realtime || {};

const pusherKey = runtime.key || env.VITE_PUSHER_APP_KEY;
const pusherHost = runtime.host || env.VITE_PUSHER_HOST;
const pusherCluster = runtime.cluster || env.VITE_PUSHER_APP_CLUSTER || 'mt1';
const pusherPort = Number(runtime.port || env.VITE_PUSHER_PORT || 443);
const pusherScheme = runtime.scheme || env.VITE_PUSHER_SCHEME || 'https';
const broadcastEnabled = typeof runtime.enabled === 'boolean'
    ? runtime.enabled
    : String(env.VITE_BROADCAST_ENABLED || '').toLowerCase() === 'true';

const hasValidPusherKey = Boolean(
    pusherKey
    && pusherKey !== 'your-app-key'
    && !pusherKey.includes('${')
);

// Si no está habilitado o falta key válida, no levantamos Echo (fallback seguro).
if (broadcastEnabled && hasValidPusherKey) {
    try {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: pusherCluster,
            wsHost: pusherHost || undefined,
            wsPort: pusherPort,
            wssPort: pusherPort,
            forceTLS: pusherScheme === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    } catch (e) {
        console.warn('Echo disabled: invalid realtime configuration.', e);
        window.Echo = null;
    }
} else {
    window.Echo = null;
}
