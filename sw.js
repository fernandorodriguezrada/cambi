const CACHE_NAME = "cambi-v3";
const urlsToCache = [
  "public/logo.webp",
  "public/logo.ico",
  "public/launchericon-512x512.png",
  "public/512.png",
  "public/qrcode.min.js",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(urlsToCache);
    }),
  );
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request)),
  );
});
