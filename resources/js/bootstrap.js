import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Forzar HTTPS en producción para evitar Mixed Content
if (window.location.protocol === 'https:') {
    window.axios.defaults.baseURL = window.location.origin;
}

// Echo/Pusher deshabilitado temporalmente
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
// window.Echo = new Echo({ ... });
