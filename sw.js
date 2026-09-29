const CACHE_NAME = "cambi-v4";
const urlsToCache = [
  "public/logo.webp",
  "public/logo.ico",
  "public/launchericon-512x512.png",
  "public/512.png",
  "public/qrcode.min.js",
  "public/banks/0102.svg",
  "public/banks/0105.svg",
  "public/banks/0108.svg",
  "public/banks/0114.png",
  "public/banks/0115.png",
  "public/banks/0128.png",
  "public/banks/0134.svg",
  "public/banks/0156.png",
  "public/banks/0157.png",
  "public/banks/0163.png",
  "public/banks/0168.png",
  "public/banks/0172.png",
  "public/banks/0174.png",
  "public/banks/0175.png",
  "public/banks/0191.png",
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
