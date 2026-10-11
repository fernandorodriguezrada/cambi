const CACHE_NAME = "cambi-v63";
const urlsToCache = [
  "public/logo.webp",
  "public/logo.ico",
  "public/launchericon-512x512.png",
  "public/maskable-icon-512x512.png",
  "public/icon-192x192.png",
  "public/512.png",
  "public/qrcode.min.js",
  "public/jsqr.min.js",
  "public/suiche7b-logo.webp",
  "public/banks/0102.svg",
  "public/banks/0104.webp",
  "public/banks/0105.svg",
  "public/banks/0108.svg",
  "public/banks/0114.webp",
  "public/banks/0115.webp",
  "public/banks/0128.webp",
  "public/banks/0134.svg",
  "public/banks/0137.webp",
  "public/banks/0138.webp",
  "public/banks/0146.webp",
  "public/banks/0151.webp",
  "public/banks/0156.webp",
  "public/banks/0157.webp",
  "public/banks/0163.webp",
  "public/banks/0166.webp",
  "public/banks/0168.webp",
  "public/banks/0169.webp",
  "public/banks/0171.svg",
  "public/banks/0172.webp",
  "public/banks/0174.webp",
  "public/banks/0175.webp",
  "public/banks/0177.webp",
  "public/banks/0191.webp",
  "public/bcv.svg",
  "public/crypto/binance.svg",
  "public/crypto/okx.svg",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(urlsToCache);
    }),
  );
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name)),
      );
    }),
  );
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request)),
  );
});
