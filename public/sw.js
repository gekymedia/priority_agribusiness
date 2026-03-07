// PWA disabled: unregister this service worker so the app runs without SW
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (event) => {
  event.waitUntil(
    self.registration.unregister().then(() => self.clients.matchAll()).then((clientList) => {
      clientList.forEach((client) => client.navigate(client.url));
    })
  );
});
