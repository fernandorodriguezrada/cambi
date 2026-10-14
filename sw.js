const CACHE_NAME = "cambi-v114";

// Recursos esenciales que se guardan durante la instalación para arranque 100% offline
const PRECACHE_URLS = [
  "./",
  "index.php",
  "manifest.json",
  "public/logo.webp",
  "public/logo.ico",
  "public/launchericon-512x512.png",
  "public/maskable-icon-512x512.png",
  "public/icon-192x192.png",
  "public/512.png",
  "public/qrcode.min.js",
  "public/jsqr.min.js",
  "public/suiche7b-logo.webp",
  "public/bcv.svg",
  "public/crypto/binance.svg",
  "public/crypto/okx.svg",
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
];

// Instalación: descargar y cachear la app shell completa
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting()),
  );
});

// Activación: limpiar cachés antiguas y tomar control de inmediato
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((name) => name !== CACHE_NAME)
            .map((name) => caches.delete(name)),
        );
      })
      .then(() => self.clients.claim()),
  );
});

// Estrategia de peticiones
self.addEventListener("fetch", (event) => {
  const req = event.request;
  const url = new URL(req.url);

  // Solo manejar peticiones HTTP/HTTPS GET
  if (req.method !== "GET") return;

  // 1. Navegación principal (HTML de la app): Network First con Fallback a Caché
  if (req.mode === "navigate" || req.destination === "document") {
    event.respondWith(
      fetch(req)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(req, responseClone);
            });
          }
          return networkResponse;
        })
        .catch(async () => {
          // Si estamos offline (sin wifi/datos), servir el HTML precacheado
          const cached =
            (await caches.match(req)) ||
            (await caches.match("./")) ||
            (await caches.match("index.php"));
          return cached;
        }),
    );
    return;
  }

  // 2. Llamadas a la API: Network First (si falla offline, el frontend usa localStorage)
  if (url.pathname.endsWith("api.php")) {
    event.respondWith(
      fetch(req).catch(() => caches.match(req)),
    );
    return;
  }

  // 3. Fuentes y assets de Google Fonts: Cache First con guardado dinámico
  if (url.origin.includes("fonts.googleapis.com") || url.origin.includes("fonts.gstatic.com")) {
    event.respondWith(
      caches.match(req).then((cachedResponse) => {
        if (cachedResponse) return cachedResponse;
        return fetch(req).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(req, responseClone));
          }
          return networkResponse;
        });
      }),
    );
    return;
  }

  // 4. Recursos estáticos locales (logos, iconos, scripts, svg, webp): Cache First con fallback a Red
  event.respondWith(
    caches.match(req).then((cachedResponse) => {
      if (cachedResponse) return cachedResponse;
      return fetch(req).then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(req, responseClone));
        }
        return networkResponse;
      });
    }),
  );
});
