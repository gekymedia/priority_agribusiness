// Service Worker for Priority Agribusiness PWA
// Strategy: Network First (try server first, use cache only when offline or network fails)
const CACHE_NAME = 'priority-agribusiness-v2';
const RUNTIME_CACHE = 'priority-agribusiness-runtime-v2';
const PRECACHE_ASSETS = ['/', '/manifest.json'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_ASSETS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.filter((n) => n !== CACHE_NAME && n !== RUNTIME_CACHE).map((n) => caches.delete(n))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin) ||
      event.request.url.includes('/admin') || event.request.url.includes('/api')) {
    return;
  }

  // Network First: fetch from server first, fall back to cache only when offline/fail
  event.respondWith(
    fetch(event.request)
      .then((response) => {
        if (response && response.status === 200 && response.type === 'basic') {
          const clone = response.clone();
          caches.open(RUNTIME_CACHE).then((cache) => cache.put(event.request, clone));
        }
        return response;
      })
      .catch(() => {
        return caches.match(event.request).then((cached) => {
          if (cached) return cached;
          if (event.request.destination === 'document') return caches.match('/');
          return null;
        });
      })
  );
});

