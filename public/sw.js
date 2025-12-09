const CACHE_NAME = 'task-manager-v1';
const urlsToCache = [
    '/dashboard',
    '/offline', // Buat route/view ini jika mau halaman offline custom
    '/images/icons/icon-192x192.png'
];

// 1. INSTALL SW & CACHE ASSETS
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// 2. ACTIVATE & CLEAN OLD CACHE
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// 3. FETCH STRATEGY (Network First, fallback to Cache)
self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.match(event.request);
            })
    );
});

// 4. HANDLE PUSH NOTIFICATION (PENTING!)
self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const payload = event.data ? event.data.json() : {};
    const title = payload.title || 'Task Reminder';
    const options = {
        body: payload.body || 'Cek tugas kamu sekarang!',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-192x192.png',
        vibrate: [100, 50, 100],
        data: {
            url: payload.action_url || '/'
        },
        actions: [
            { action: 'open', title: 'Buka Aplikasi' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// 5. HANDLE NOTIFICATION CLICK
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});