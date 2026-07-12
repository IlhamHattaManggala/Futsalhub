const CACHE_NAME = 'futsalhub-v1';
const assetsToCache = [
    '/favicon_1780410241.webp',
    '/images/web_logo_1780410241.webp'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // Cache files individually and catch errors so a single 404 doesn't block the whole PWA installation
            const cachePromises = assetsToCache.map((asset) => {
                return cache.add(asset).catch((err) => {
                    console.warn('Failed to cache asset on install:', asset, err);
                });
            });
            return Promise.all(cachePromises);
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    // Only intercept GET requests to prevent issues with POST body stream handling (like saving tactics)
    if (event.request.method !== 'GET') {
        return;
    }

    // Network-first strategy: try fetching via network first
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});

/* Web Push Notifications */
self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    let data;
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: 'Notifikasi Baru',
            body: event.data.text()
        };
    }

    const options = {
        body: data.body || '',
        icon: data.icon || '/images/logo.png',
        badge: data.badge || '/favicon_1780410241.webp',
        data: data.data || {},
        vibrate: [100, 50, 100],
        actions: data.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Notifikasi', options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    let targetUrl = '/';
    if (event.notification.data && event.notification.data.url) {
        targetUrl = event.notification.data.url;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                let client = clientList[i];
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
