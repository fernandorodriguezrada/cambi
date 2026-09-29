const CACHE_NAME = "cambi-v9";
const urlsToCache = [
  "public/logo.webp",
  "public/logo.ico",
  "public/launchericon-512x512.png",
  "public/512.png",
  "public/qrcode.min.js",
  "public/banks/0102.svg",
  "public/banks/0104.png",
  "public/banks/0105.svg",
  "public/banks/0108.svg",
  "public/banks/0114.png",
  "public/banks/0115.png",
  "public/banks/0128.png",
  "public/banks/0134.svg",
  "public/banks/0137.png",
  "public/banks/0138.png",
  "public/banks/0146.png",
  "public/banks/0151.png",
  "public/banks/0156.png",
  "public/banks/0157.png",
  "public/banks/0163.png",
  "public/banks/0166.png",
  "public/banks/0168.png",
  "public/banks/0169.png",
  "public/banks/0171.png",
  "public/banks/0172.png",
  "public/banks/0174.png",
  "public/banks/0175.png",
  "public/banks/0177.png",
  "public/banks/0191.png",
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
