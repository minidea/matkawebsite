const CACHE_NAME = 'matka-pro-v1';
const urlsToCache = [
  '/',
  '/css/styles.css',
  '/js/main.js',
  '/js/chart.js',
  '/pages/jodi-chart.html',
  '/pages/panel-chart.html',
  '/manifest.json'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  const isApiRequest = event.request.url.includes('/api/');
  
  if (isApiRequest) {
    // For API requests, always try network first for live records
    event.respondWith(
      fetch(event.request).catch(() => caches.match(event.request))
    );
  } else {
    // For static files, Cache First, then Network
    event.respondWith(
      caches.match(event.request)
        .then(response => {
          if (response) {
            return response;
          }
          return fetch(event.request);
        })
    );
  }
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
