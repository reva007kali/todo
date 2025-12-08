const CACHE_NAME = 'task-manager-v1';
const urlsToCache = [
    '/offline', // Buat route/view ini jika mau halaman offline custom
    '/build/assets/app.css', // Sesuaikan dengan build file kamu
    '/build/assets/app.js',
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


// 4. HANDLE PUSH NOTIFICATION (PENTING!)
self.addEventListener('push', event => {
    const data = event.data.json();

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
        })
    );
});


// 5. HANDLE NOTIFICATION CLICK
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});