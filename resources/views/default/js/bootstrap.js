/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// import axios from 'axios';
// window.axios = axios;

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;
let pusherConfig = window.pusherConfig;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherConfig?.key,
    cluster: pusherConfig?.cluster,
    wsHost: pusherConfig?.wsHost ?? `ws-${pusherConfig?.cluster}.pusher.com`,
    wsPort: pusherConfig?.port ?? 80,
    wssPort: pusherConfig?.port ?? 443,
    forceTLS: (pusherConfig?.scheme ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
