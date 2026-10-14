<script>
    let currentRate = 0;
    let ratesData = {
        usd: 0,
        eur: 0,
        binance_usdt: 0,
        binance_usdc: 0,
        okx_usdt: 0
    };
    let selectedRateType = localStorage.getItem("defaultRateType") || "usd";
    let isTopUsd = true; // Estado para saber el sentido de la conversión
    let isResultPendingClear = false; // Control para limpiar el input tras un resultado
    let isInitialDefault = true; // Control para sobreescribir el 1,00 inicial al primer toque
    let caretTimer = null;

    const inputTop = document.getElementById('input-top');
    const inputBottom = document.getElementById('input-bottom');
    const labelTop = document.getElementById('label-top');
    const labelBottom = document.getElementById('label-bottom');
    const iconTop = document.getElementById('icon-top');
    const iconBottom = document.getElementById('icon-bottom');

    const rateDisplay = document.getElementById('rate');
    const dateDisplay = document.getElementById('date');
    const rateEurDisplay = document.getElementById('rate-eur');
    const dateEurDisplay = document.getElementById('date-eur');
    const customCaret = document.getElementById('custom-caret');
    const textMeasurer = document.getElementById('text-measurer');

    const updateCaretPosition = () => {
        if (!customCaret) return;
        if (!inputTop || !inputTop.value) {
            customCaret.style.display = 'none';
            return;
        }

        // Medimos el texto a la derecha del cursor para saber cuánto desplazar desde el borde derecho
        const val = inputTop.value;
        const pos = inputTop.selectionStart ?? val.length;
        const textAfter = val.substring(pos);
        
        let textAfterWidth = 0;
        if (textMeasurer) {
            textMeasurer.innerText = textAfter;
            textAfterWidth = textMeasurer.offsetWidth;
        }
        
        // Con input-field-wrapper, el padding-right del input es 8px
        const rightOffset = 8 + textAfterWidth;
        customCaret.style.right = `${rightOffset}px`;
        customCaret.style.display = 'block';

        // Reiniciar temporizador de 10 segundos
        clearTimeout(caretTimer);
        caretTimer = setTimeout(() => {
            if (customCaret) customCaret.style.display = 'none';
        }, 10000);
    };

    /**
     * Mover el cursor a la posición donde el usuario hace tap o click sobre el input
     */
    const setCursorFromEvent = (e) => {
        if (!inputTop || !inputTop.value) return;
        isInitialDefault = false;

        const rect = inputTop.getBoundingClientRect();
        const clientX = (e.touches && e.touches[0]) ? e.touches[0].clientX : e.clientX;
        
        // Distancia en píxeles desde el borde derecho del input
        const clickOffsetFromRight = rect.right - clientX - 8;

        if (clickOffsetFromRight <= 0) {
            const endPos = inputTop.value.length;
            inputTop.setSelectionRange(endPos, endPos);
            updateCaretPosition();
            return;
        }

        const val = inputTop.value;
        let closestPos = val.length;
        let minDiff = Infinity;

        if (textMeasurer) {
            for (let i = val.length; i >= 0; i--) {
                textMeasurer.innerText = val.substring(i);
                const textWidth = textMeasurer.offsetWidth;
                const diff = Math.abs(textWidth - clickOffsetFromRight);
                if (diff < minDiff) {
                    minDiff = diff;
                    closestPos = i;
                }
            }
        }

        inputTop.setSelectionRange(closestPos, closestPos);
        updateCaretPosition();
    };

    /**
     * Formatea números según normas RAE/ISO:
     * - Espacio fino (o espacio) como separador de miles.
     * - Coma como separador decimal.
     */
    const formatRAE = (number, minDecimals = 2) => {
        return new Intl.NumberFormat('es-ES', {
            minimumFractionDigits: minDecimals,
            maximumFractionDigits: 2
        }).format(number).replace(/\./g, ' ');
    };

    /**
     * Limpia el formato (espacios y comas) para obtener un número válido.
     */
    const cleanValue = (val) => {
        if (!val) return 0;
        let cleaned = val.replace(/\s/g, '').replace(',', '.');
        return parseFloat(cleaned) || 0;
    };

    /**
     * Formatea dinámicamente un input mientras el usuario escribe preservando el cursor.
     */
    const formatInputDisplay = (el) => {
        if (!el) return;
        const cursor = el.selectionStart ?? el.value.length;
        const oldVal = el.value;
        const oldLen = oldVal.length;

        // Limpiamos todo excepto dígitos, operadores y coma
        let sanitized = oldVal.replace(/[^0-9,xX*\/+\-.]/g, '');
        sanitized = sanitized.replace(/\./g, ','); // Convertir puntos accidentales a coma
        
        // Garantizar una sola coma
        const parts = sanitized.split(',');
        if (parts.length > 2) {
            sanitized = parts[0] + ',' + parts.slice(1).join('');
        }

        el.value = sanitized;

        // Ajustar el cursor de forma relativa para que no salte al final
        const newLen = el.value.length;
        let nextCursor = cursor + (newLen - oldLen);
        const finalPos = Math.max(0, Math.min(nextCursor, newLen));
        el.setSelectionRange(finalPos, finalPos);
        if (el === inputTop) updateCaretPosition();
    };

    /**
     * Cambia el color del texto a gris cuando el valor es 0,00 o está vacío,
     * y sincroniza las clases cromáticas de las filas (Verde menta/azul para divisa extranjera,
     * Blanco/neutro para VES) según el sentido de la conversión (isTopUsd).
     */
    const updateInputColors = () => {
        [inputTop, inputBottom].forEach(el => {
            if (!el) return;
            const isMuted = el.value === '0,00' || el.value === '';
            el.classList.toggle('value-zero', isMuted);
        });

        const rowTop = document.getElementById('calc-row-top');
        const rowBottom = document.getElementById('calc-row-bottom');
        if (rowTop && rowBottom) {
            rowTop.classList.toggle('row-foreign', isTopUsd);
            rowTop.classList.toggle('row-local', !isTopUsd);
            rowBottom.classList.toggle('row-foreign', !isTopUsd);
            rowBottom.classList.toggle('row-local', isTopUsd);
        }

        const operationResult = document.getElementById('operation-result');
        if (operationResult) {
            const foreignColor = selectedRateType === 'eur' ? 'var(--rate-eur-color)' : 'var(--rate-usd-color)';
            operationResult.style.color = isTopUsd ? foreignColor : 'var(--md-sys-color-on-surface)';
        }
    };

    /**
     * Muestra una notificación emergente tipo toast
     */
    function showToast(message) {
        const toast = document.getElementById("pm-toast");
        const toastMsg = document.getElementById("pm-toast-msg");
        if (!toast) return;

        clearTimeout(toast._timeout);
        clearTimeout(toast._retriggerTimeout);

        // Si ya está visible o en transición, reemplazamos con una micro-animación fluida de salida/entrada
        if (toast.classList.contains("active") || toast.classList.contains("toast-replacing")) {
            toast.classList.remove("active");
            toast.classList.add("toast-replacing");

            toast._retriggerTimeout = setTimeout(() => {
                toast.classList.remove("toast-replacing");
                if (toastMsg) toastMsg.innerText = message;
                void toast.offsetWidth; // Forzar reflow para reiniciar la animación
                toast.classList.add("active");
                toast._timeout = setTimeout(() => {
                    toast.classList.remove("active");
                }, 2600);
            }, 110);
            return;
        }

        if (toastMsg) toastMsg.innerText = message;
        toast.classList.add("active");
        toast._timeout = setTimeout(() => {
            toast.classList.remove("active");
        }, 2600);
    }
    window.showToast = showToast;

    /**
     * Gestión del estado de conexión (En línea / Modo sin conexión)
     */
    function updateNetworkStatus() {
        const isOnline = navigator.onLine;
        const btn = document.getElementById("network-status-btn");
        const icon = document.getElementById("network-status-icon");
        if (!btn || !icon) return;

        if (isOnline) {
            btn.classList.remove("offline");
            icon.innerText = "cloud";
        } else {
            btn.classList.add("offline");
            icon.innerText = "cloud_off";
        }
    }
    window.updateNetworkStatus = updateNetworkStatus;

    window.addEventListener("online", () => {
        updateNetworkStatus();
        showToast("Conexión restablecida");
        setTimeout(() => {
            if (typeof fetchRate === "function" && navigator.onLine) {
                fetchRate();
            }
        }, 500);
    });

    window.addEventListener("offline", () => {
        updateNetworkStatus();
        showToast("Modo sin conexión");
    });

    // Inicializar estado inmediatamente
    updateNetworkStatus();

    /**
     * Utilidad universal para copiar al portapapeles con fallback robusto
     */
    function copyTextToClipboard(text, successCallback) {
        if (!text) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    if (successCallback) successCallback();
                })
                .catch(() => {
                    fallbackCopyText(text, successCallback);
                });
        } else {
            fallbackCopyText(text, successCallback);
        }
    }
    window.copyTextToClipboard = copyTextToClipboard;

    function fallbackCopyText(text, successCallback) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        textArea.setAttribute("readonly", "");
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            const successful = document.execCommand("copy");
            if (successful && successCallback) {
                successCallback();
            }
        } catch (err) {
            console.error("Fallback copy failed", err);
        }
        document.body.removeChild(textArea);
    }

    /**
     * Copiar el monto de las tarjetas de tasas
     */
    function copyRateCard(rateId, btn) {
        const el = document.getElementById(rateId);
        if (!el) return;
        const text = el.innerText.trim();
        if (!text || text === "--.--" || text.toLowerCase().includes("error") || text.toLowerCase().includes("esperando") || text.toLowerCase().includes("no disponible")) {
            showToast("Tasa aún no disponible para copiar");
            return;
        }

        copyTextToClipboard(text, () => {
            showToast(`Tasa copiada: ${text} Bs.`);
            if (btn) {
                const icon = btn.querySelector(".material-symbols-rounded");
                if (icon) {
                    const originalIcon = icon.innerText;
                    icon.innerText = "check";
                    btn.classList.add("copied");
                    setTimeout(() => {
                        icon.innerText = originalIcon;
                        btn.classList.remove("copied");
                    }, 1400);
                }
            }
        });
    }
    window.copyRateCard = copyRateCard;

    /**
     * Sanitizar texto pegado para extraer únicamente el monto numérico válido
     */
    function sanitizePastedNumber(raw) {
        if (!raw) return "";
        let str = String(raw).trim();

        // Eliminar saltos de línea y espacios en blanco
        str = str.replace(/\s+/g, "");

        // Extraer el primer bloque numérico
        const match = str.match(/[-+]?[0-9.,]+/);
        if (!match) return "";
        let numStr = match[0];

        const hasComma = numStr.includes(",");
        const hasDot = numStr.includes(".");

        if (hasComma && hasDot) {
            const lastComma = numStr.lastIndexOf(",");
            const lastDot = numStr.lastIndexOf(".");
            if (lastComma > lastDot) {
                // Formato venezolano/europeo 1.250,50 -> remover puntos de miles
                numStr = numStr.replace(/\./g, "");
            } else {
                // Formato americano 1,250.50 -> remover comas de miles y usar coma decimal
                numStr = numStr.replace(/,/g, "").replace(/\./g, ",");
            }
        } else if (hasDot && !hasComma) {
            const dotParts = numStr.split(".");
            if (dotParts.length > 2) {
                numStr = dotParts.join("");
            } else {
                numStr = numStr.replace(".", ",");
            }
        }

        // Si empieza directamente con coma (ej: ,50 -> 0,50)
        if (numStr.startsWith(",")) numStr = "0" + numStr;

        if (!/\d/.test(numStr)) return "";
        return numStr;
    }
    window.sanitizePastedNumber = sanitizePastedNumber;

    /**
     * Copiar monto de la fila especificada en la calculadora (top o bottom)
     */
    function copyFromCalc(target, btn) {
        const input = target === "top" ? inputTop : inputBottom;
        if (!input || !input.value || input.value === "0,00" || input.value.trim() === "") {
            showToast("No hay monto para copiar");
            return;
        }

        const val = input.value.trim();
        copyTextToClipboard(val, () => {
            const cur = target === "top" 
                ? (isTopUsd ? getCurrencySymbolForRate(selectedRateType) : "VES") 
                : (isTopUsd ? "VES" : getCurrencySymbolForRate(selectedRateType));
            showToast(`Monto copiado: ${val} ${cur}`);

            if (btn) {
                const icon = btn.querySelector(".material-symbols-rounded");
                if (icon) {
                    const originalIcon = icon.innerText;
                    icon.innerText = "check";
                    btn.classList.add("copied");
                    setTimeout(() => {
                        icon.innerText = originalIcon;
                        btn.classList.remove("copied");
                    }, 1400);
                }
            }
        });
    }
    window.copyFromCalc = copyFromCalc;

    /**
     * Pegar monto del portapapeles en la fila especificada de la calculadora
     */
    async function pasteToCalc(target, btn) {
        let text = "";
        try {
            if (navigator.clipboard && navigator.clipboard.readText) {
                text = await navigator.clipboard.readText();
            } else {
                text = prompt("Pega aquí el monto a calcular:");
            }
        } catch (e) {
            text = prompt("Pega aquí el monto a calcular:");
        }

        if (!text) return;

        const sanitized = sanitizePastedNumber(text);
        if (!sanitized) {
            showToast("No se encontró un número válido en el portapapeles");
            return;
        }

        if (target === "top") {
            if (!inputTop) return;
            isInitialDefault = false;
            inputTop.value = sanitized;
            isResultPendingClear = false;
            formatInputDisplay(inputTop);
            convert(inputTop);
            updateCaretPosition();
            updateInputColors();
            showToast(`Monto pegado: ${inputTop.value}`);
        } else if (target === "bottom") {
            if (!inputBottom) return;
            inputBottom.value = sanitized;
            formatInputDisplay(inputBottom);
            convert(inputBottom);
            updateInputColors();
            showToast(`Monto pegado: ${inputBottom.value}`);
        }

        if (btn) {
            const icon = btn.querySelector(".material-symbols-rounded");
            if (icon) {
                const originalIcon = icon.innerText;
                icon.innerText = "check";
                btn.classList.add("copied");
                setTimeout(() => {
                    icon.innerText = originalIcon;
                    btn.classList.remove("copied");
                }, 1400);
            }
        }
    }
    window.pasteToCalc = pasteToCalc;

    let ctxMenuTarget = "top";

    /**
     * Muestra el menú contextual flotante estilo iOS/Android sobre el monto seleccionado
     */
    function showCalcContextMenu(target, clientX, clientY, sourceElement) {
        const menu = document.getElementById("calc-context-menu");
        if (!menu) return;

        ctxMenuTarget = target;
        const input = target === "top" ? inputTop : inputBottom;
        const copyBtn = document.getElementById("calc-ctx-copy");

        // Deshabilitar visualmente Copiar si el input está vacío o es 0,00
        const hasVal = input && input.value && input.value !== "0,00" && input.value.trim() !== "";
        if (copyBtn) {
            copyBtn.classList.toggle("disabled", !hasVal);
        }

        // Posicionamiento dinámico
        menu.style.visibility = "hidden";
        menu.style.display = "flex";
        menu.classList.add("active");

        const menuRect = menu.getBoundingClientRect();
        const menuWidth = menuRect.width || 170;
        const menuHeight = menuRect.height || 42;

        const row = document.getElementById(target === "top" ? "calc-row-top" : "calc-row-bottom");
        const rowRect = row ? row.getBoundingClientRect() : null;
        if (!rowRect) return;

        // Centrar horizontalmente:
        // Si el usuario hizo clic o tocó en un punto específico (clientX), centramos sobre ese punto;
        // de lo contrario, centramos sobre la parte numérica derecha del input.
        let posX = (clientX !== undefined && clientX > 0) ? clientX : (rowRect.right - 70);
        let left = posX - (menuWidth / 2);
        left = Math.max(12, Math.min(window.innerWidth - menuWidth - 12, left));

        // La burbuja DEBE estar JUSTO ENCIMA de la fila que la invoque
        let top = rowRect.top - menuHeight - 10;
        const arrow = document.getElementById("calc-ctx-arrow");

        // Si estamos demasiado cerca del tope de la pantalla (menos de 8px):
        if (top < 8) {
            top = rowRect.bottom + 10;
            menu.classList.add("arrow-top");
        } else {
            menu.classList.remove("arrow-top");
        }

        menu.style.left = `${Math.round(left)}px`;
        menu.style.top = `${Math.round(top)}px`;

        if (arrow) {
            const arrowX = posX - left;
            const clampedArrowX = Math.max(18, Math.min(menuWidth - 18, arrowX));
            arrow.style.left = `${Math.round(clampedArrowX)}px`;
        }

        menu.style.visibility = "";
    }
    window.showCalcContextMenu = showCalcContextMenu;

    function hideCalcContextMenu() {
        const menu = document.getElementById("calc-context-menu");
        if (menu && menu.classList.contains("active")) {
            menu.classList.remove("active");
        }
    }
    window.hideCalcContextMenu = hideCalcContextMenu;

    function triggerCtxCopy(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        hideCalcContextMenu();
        copyFromCalc(ctxMenuTarget);
    }
    window.triggerCtxCopy = triggerCtxCopy;

    function triggerCtxPaste(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        hideCalcContextMenu();
        pasteToCalc(ctxMenuTarget);
    }
    window.triggerCtxPaste = triggerCtxPaste;

    /**
     * Configurar menú contextual estilo iOS/Android:
     * - Pulsación larga (touch) para móvil
     * - Clic derecho (contextmenu) para escritorio
     */
    function setupLongPressMenu(rowEl, inputEl, targetType) {
        if (!rowEl) return;
        let pressTimer = null;
        let startX = 0;
        let startY = 0;
        let isLongPress = false;

        const onTouchStart = (e) => {
            if (!e.touches || e.touches.length > 1) return;
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            isLongPress = false;
            clearTimeout(pressTimer);

            pressTimer = setTimeout(() => {
                isLongPress = true;
                if (navigator.vibrate) navigator.vibrate(40);
                showCalcContextMenu(targetType, startX, startY, rowEl);
            }, 450);
        };

        const onTouchMove = (e) => {
            if (e.touches && e.touches[0]) {
                const dx = Math.abs(e.touches[0].clientX - startX);
                const dy = Math.abs(e.touches[0].clientY - startY);
                if (dx > 10 || dy > 10) {
                    clearTimeout(pressTimer);
                }
            } else {
                clearTimeout(pressTimer);
            }
        };

        const onTouchEnd = (e) => {
            clearTimeout(pressTimer);
            if (isLongPress) {
                if (e.cancelable) e.preventDefault();
                e.stopPropagation();
            }
        };

        rowEl.addEventListener("touchstart", onTouchStart, { passive: true });
        rowEl.addEventListener("touchmove", onTouchMove, { passive: true });
        rowEl.addEventListener("touchend", onTouchEnd, { passive: false });
        rowEl.addEventListener("touchcancel", () => clearTimeout(pressTimer), { passive: true });

        // Clic derecho (Desktop)
        rowEl.addEventListener("contextmenu", (e) => {
            e.preventDefault();
            showCalcContextMenu(targetType, e.clientX, e.clientY, rowEl);
        });
    }

    // Cerrar el menú contextual al tocar fuera o hacer scroll
    document.addEventListener("pointerdown", (e) => {
        const menu = document.getElementById("calc-context-menu");
        if (menu && menu.classList.contains("active")) {
            if (!menu.contains(e.target)) {
                hideCalcContextMenu();
            }
        }
    });

    window.addEventListener("scroll", hideCalcContextMenu, { passive: true });
    const mainContentEl = document.getElementById("main-content");
    if (mainContentEl) {
        mainContentEl.addEventListener("scroll", hideCalcContextMenu, { passive: true });
    }

    const updateLoadingProgress = (percent) => {};

    const RATES_CACHE_KEY = "cambi_latest_rates_cache";
    // Duración elegante para la animación de inicio (permite que la UI inicialice, aplique tasas y coloque la pestaña sin parpadeos)
    const SPLASH_MIN_DISPLAY_MS = 900;
    let splashStartTime = performance.now();
    let splashIsDismissed = false;

    const hideSplashScreen = (forceImmediate = false) => {
        if (splashIsDismissed) return;
        const splash = document.getElementById('splash-screen');
        if (!splash) return;

        const elapsed = performance.now() - splashStartTime;
        const delay = forceImmediate ? 0 : Math.max(0, SPLASH_MIN_DISPLAY_MS - elapsed);

        setTimeout(() => {
            splash.classList.add('hidden');
            splash.classList.remove('manual-refresh');
            splashIsDismissed = true;
        }, delay);
    };

    /**
     * Dibuja los datos de tasas en la interfaz de forma reactiva
     */
    function applyRatesToUI(data) {
        if (!data) return;

        // Renderizar Historial si viene en el payload
        const historyList = document.getElementById('history-list');
        if (data.history && historyList) {
            let historyHtml = `
                <div class="history-header">
                    <span>Fecha / Hora</span><span style="text-align: right">USD</span><span style="text-align: right">EUR</span>
                </div>`;
            [...data.history].reverse().forEach(entry => {
                historyHtml += `
                    <div class="history-item">
                        <span>${entry.date}</span>
                        <span class="usd-val">${formatRAE(entry.usd)}</span>
                        <span class="eur-val">${entry.eur ? formatRAE(entry.eur) : 'N/D'}</span>
                    </div>`;
            });
            historyList.innerHTML = historyHtml;
        }

        if (data.usd) ratesData.usd = data.usd;
        if (data.eur) ratesData.eur = data.eur;
        if (data.p2p) {
            if (data.p2p.binance_usdt) ratesData.binance_usdt = data.p2p.binance_usdt;
            if (data.p2p.binance_usdc) ratesData.binance_usdc = data.p2p.binance_usdc;
            if (data.p2p.okx_usdt) ratesData.okx_usdt = data.p2p.okx_usdt;
        }

        currentRate = ratesData[selectedRateType] || ratesData.usd || 0;
        if (rateDisplay && ratesData.usd) rateDisplay.innerText = formatRAE(ratesData.usd);

        const sourceLabel = data.source ? ` (${data.source})` : '';
        const updateLabel = data.last_update ? `Actualizado: ${data.last_update}${sourceLabel}` : 'Consultando BCV...';

        if (dateDisplay) {
            dateDisplay.style.color = "";
            dateDisplay.innerText = updateLabel;
        }

        if (ratesData.eur) {
            if (rateEurDisplay) rateEurDisplay.innerText = formatRAE(ratesData.eur);
            if (dateEurDisplay) {
                dateEurDisplay.style.color = "";
                dateEurDisplay.innerText = updateLabel;
            }
        } else if (rateEurDisplay) {
            rateEurDisplay.innerText = "No disponible";
            if (dateEurDisplay) dateEurDisplay.innerText = "Fuente sin Euro";
        }

        // Renderizar tasas P2P (Binance y OKX)
        const p2pUsdtEl = document.getElementById("rate-binance-usdt");
        const p2pUsdcEl = document.getElementById("rate-binance-usdc");
        const p2pOkxEl = document.getElementById("rate-okx-usdt");
        const dateBinanceUsdt = document.getElementById("date-binance-usdt");
        const dateBinanceUsdc = document.getElementById("date-binance-usdc");
        const dateOkxUsdt = document.getElementById("date-okx-usdt");

        if (p2pUsdtEl) p2pUsdtEl.innerText = ratesData.binance_usdt ? formatRAE(ratesData.binance_usdt) : "--.--";
        if (p2pUsdcEl) p2pUsdcEl.innerText = ratesData.binance_usdc ? formatRAE(ratesData.binance_usdc) : "--.--";
        if (p2pOkxEl) p2pOkxEl.innerText = ratesData.okx_usdt ? formatRAE(ratesData.okx_usdt) : "--.--";

        const binanceTime = data.p2p?.last_update ? `Actualizado: ${data.p2p.last_update} (Binance P2P)` : "Consultando Binance...";
        const okxTime = data.p2p?.last_update ? `Actualizado: ${data.p2p.last_update} (OKX P2P)` : "Consultando OKX...";

        if (dateBinanceUsdt) dateBinanceUsdt.innerText = binanceTime;
        if (dateBinanceUsdc) dateBinanceUsdc.innerText = binanceTime;
        if (dateOkxUsdt) dateOkxUsdt.innerText = okxTime;

        updateRateChipsDisplay();
        if (inputTop) {
            if (inputTop.value === '' || isInitialDefault) {
                if (isTopUsd) {
                    inputTop.value = '1,00';
                } else {
                    inputTop.value = currentRate ? formatRAE(currentRate) : '1,00';
                }
                isInitialDefault = true;
                convert(inputTop);
            } else if (cleanValue(inputTop.value) !== 0) {
                convert(inputTop);
            }
        }
    }

    /**
     * Cargar instantáneamente la última tasa guardada en el dispositivo (0ms)
     */
    function loadInitialCachedRates() {
        try {
            const raw = localStorage.getItem(RATES_CACHE_KEY);
            if (raw) {
                const cached = JSON.parse(raw);
                if (cached && (cached.usd || cached.p2p)) {
                    applyRatesToUI(cached);
                    // Respeta el tiempo mínimo de splash para una experiencia visual libre de parpadeos
                    hideSplashScreen();
                }
            }
        } catch (e) {
            console.warn("Error leyendo caché local:", e);
        }
    }

    async function fetchRate(forceRefresh = false) {
        const splash = document.getElementById('splash-screen');
        
        // Si el usuario presiona "Actualizar" manualmente, mostramos la animación de forma fluida
        if (forceRefresh && splash) {
            splash.classList.add('manual-refresh');
            splashStartTime = performance.now();
            splashIsDismissed = false;
            splash.classList.remove('hidden');
        }

        try {
            const queryParam = forceRefresh ? `force=1&t=${Date.now()}` : `t=${Date.now()}`;
            const response = await fetch(`api.php?${queryParam}`, { cache: 'no-store' });
            if (!response.ok) throw new Error("Error en el servidor local");

            const data = await response.json();
            
            // Aplicar inmediatamente los datos actualizados a la interfaz
            applyRatesToUI(data);

            // Guardar en almacenamiento local para inicio instantáneo futuro
            try {
                localStorage.setItem(RATES_CACHE_KEY, JSON.stringify(data));
            } catch (e) {}

            // Refresco en segundo plano si es necesario o si fue forzado
            if (forceRefresh || (data.p2p && data.p2p.is_stale)) {
                setTimeout(refreshP2PSilently, 100);
            }
        } catch (error) {
            console.error("Error fetching rate:", error);
            if (!ratesData.usd) {
                if (rateDisplay) rateDisplay.innerText = "Error de Red";
                if (rateEurDisplay) rateEurDisplay.innerText = "Error de Red";
            } else {
                if (typeof showToast === "function") {
                    if (!navigator.onLine) {
                        showToast("Modo sin conexión");
                    } else if (forceRefresh) {
                        showToast("Error al actualizar tasa");
                    } else {
                        showToast("Conexión restablecida");
                    }
                }
            }
        } finally {
            hideSplashScreen();
        }
    }

    async function refreshP2PSilently() {
        try {
            const res = await fetch(`api.php?update_p2p=1&t=${Date.now()}`, { cache: 'no-store' });
            const json = await res.json();
            if (json && json.p2p) {
                ratesData.binance_usdt = json.p2p.binance_usdt || 0;
                ratesData.binance_usdc = json.p2p.binance_usdc || 0;
                ratesData.okx_usdt = json.p2p.okx_usdt || 0;

                const p2pUsdtEl = document.getElementById("rate-binance-usdt");
                const p2pUsdcEl = document.getElementById("rate-binance-usdc");
                const p2pOkxEl = document.getElementById("rate-okx-usdt");
                const dateBinanceUsdt = document.getElementById("date-binance-usdt");
                const dateBinanceUsdc = document.getElementById("date-binance-usdc");
                const dateOkxUsdt = document.getElementById("date-okx-usdt");

                if (p2pUsdtEl) p2pUsdtEl.innerText = ratesData.binance_usdt ? formatRAE(ratesData.binance_usdt) : "--.--";
                if (p2pUsdcEl) p2pUsdcEl.innerText = ratesData.binance_usdc ? formatRAE(ratesData.binance_usdc) : "--.--";
                if (p2pOkxEl) p2pOkxEl.innerText = ratesData.okx_usdt ? formatRAE(ratesData.okx_usdt) : "--.--";

                const binanceTime = `Actualizado: ${json.p2p.last_update} (Binance P2P)`;
                const okxTime = `Actualizado: ${json.p2p.last_update} (OKX P2P)`;

                if (dateBinanceUsdt) dateBinanceUsdt.innerText = binanceTime;
                if (dateBinanceUsdc) dateBinanceUsdc.innerText = binanceTime;
                if (dateOkxUsdt) dateOkxUsdt.innerText = okxTime;

                updateRateChipsDisplay();
                if (selectedRateType !== "usd" && selectedRateType !== "eur") {
                    currentRate = ratesData[selectedRateType] || 0;
                    if (inputTop && cleanValue(inputTop.value) !== 0) convert(inputTop);
                }

                // Sincronizar en caché local
                try {
                    const raw = localStorage.getItem(RATES_CACHE_KEY);
                    if (raw) {
                        const cached = JSON.parse(raw);
                        cached.p2p = json.p2p;
                        localStorage.setItem(RATES_CACHE_KEY, JSON.stringify(cached));
                    }
                } catch (e) {}
            }
        } catch (e) {
            console.warn("Silent P2P refresh error:", e);
        }
    }

    function getCurrencySymbolForRate(type) {
        switch (type) {
            case "binance_usdt":
            case "okx_usdt":
                return "USDT";
            case "binance_usdc":
                return "USDC";
            case "eur":
                return "EUR";
            case "usd":
            default:
                return "USD";
        }
    }

    function updateRateChipsDisplay() {
        const valUsd = document.getElementById("modal-rate-val-usd");
        const valBUsdt = document.getElementById("modal-rate-val-binance-usdt");
        const valBUsdc = document.getElementById("modal-rate-val-binance-usdc");
        const valOkx = document.getElementById("modal-rate-val-okx-usdt");
        const valEur = document.getElementById("modal-rate-val-eur");

        if (valUsd) valUsd.innerText = ratesData.usd ? `${formatRAE(ratesData.usd)} Bs.` : "--.--";
        if (valBUsdt) valBUsdt.innerText = ratesData.binance_usdt ? `${formatRAE(ratesData.binance_usdt)} Bs.` : "--.--";
        if (valBUsdc) valBUsdc.innerText = ratesData.binance_usdc ? `${formatRAE(ratesData.binance_usdc)} Bs.` : "--.--";
        if (valOkx) valOkx.innerText = ratesData.okx_usdt ? `${formatRAE(ratesData.okx_usdt)} Bs.` : "--.--";
        if (valEur) valEur.innerText = ratesData.eur ? `${formatRAE(ratesData.eur)} Bs.` : "--.--";
    }

    function openCalcRateModal() {
        updateRateChipsDisplay();
        const modal = document.getElementById("calc-rate-modal");
        if (modal) modal.classList.add("active");
    }
    window.openCalcRateModal = openCalcRateModal;

    function closeCalcRateModal() {
        const modal = document.getElementById("calc-rate-modal");
        if (modal) modal.classList.remove("active");
    }
    window.closeCalcRateModal = closeCalcRateModal;

    function selectCalcRateFromModal(type) {
        setCalcRateType(type);
        closeCalcRateModal();
    }
    window.selectCalcRateFromModal = selectCalcRateFromModal;

    function setCalcRateType(type) {
        selectedRateType = type;
        currentRate = ratesData[type] || ratesData.usd || 0;

        // Actualizar ícono del botón de la calculadora
        const pickerIcon = document.getElementById("calc-picker-active-icon");
        if (pickerIcon) {
            if (type === "binance_usdt" || type === "binance_usdc") {
                pickerIcon.src = "public/crypto/binance.svg";
                pickerIcon.alt = "Binance";
            } else if (type === "okx_usdt") {
                pickerIcon.src = "public/crypto/okx.svg";
                pickerIcon.alt = "OKX";
            } else {
                pickerIcon.src = "public/bcv.svg";
                pickerIcon.alt = "BCV";
            }
        }

        // Actualizar item activo en el modal
        document.querySelectorAll(".calc-rate-option-item").forEach(item => {
            item.classList.toggle("active", item.getAttribute("data-type") === type);
        });

        const cur = getCurrencySymbolForRate(type);
        if (labelTop && labelBottom) {
            if (isTopUsd) {
                labelTop.innerText = cur;
                labelBottom.innerText = "VES";
            } else {
                labelTop.innerText = "VES";
                labelBottom.innerText = cur;
            }
        }

        const combinedCard = document.querySelector('.combined-card');
        if (combinedCard) {
            combinedCard.style.setProperty('--calc-foreign-color', type === 'eur' ? 'var(--rate-eur-color)' : 'var(--rate-usd-color)');
        }

        if (inputTop) {
            if (isInitialDefault) {
                if (isTopUsd) {
                    inputTop.value = '1,00';
                } else {
                    inputTop.value = currentRate ? formatRAE(currentRate) : '1,00';
                }
            }
            convert(inputTop);
        }
    }
    window.setCalcRateType = setCalcRateType;

    function selectRateForCalc(type) {
        setCalcRateType(type);
        switchTab("calculator");
    }
    window.selectRateForCalc = selectRateForCalc;

    function convert(triggeringInput) {
        if (!triggeringInput) return;
        const operationResult = document.getElementById('operation-result');

        if (!currentRate) {
            if (operationResult) operationResult.innerText = 'Esperando tasa oficial...';
            return;
        }
        
        // Evaluación de expresión matemática simple
        let numericVal = 0;
        let hasOperation = false;
        try {
            // Limpiamos espacios, comas y convertimos la 'x' en '*' para que JS pueda calcular
            let expression = triggeringInput.value.replace(/\s/g, '').replace(/,/g, '.').replace(/x/gi, '*').replace(/[+\-*\/]$/, '');
            hasOperation = /[+\-*\/]/.test(expression);
            numericVal = expression ? Function('"use strict";return (' + expression + ')')() : 0;
        } catch (e) { numericVal = 0; }

        if (triggeringInput === inputTop) {
            if (operationResult) {
                if (hasOperation) {
                    const cur = getCurrencySymbolForRate(selectedRateType); const label = isTopUsd ? cur : "Bs.";
                    operationResult.innerText = `= ${label} ${formatRAE(numericVal)}`;
                } else {
                    operationResult.innerText = '';
                }
            }

            // Si el valor numérico es 0, dejamos el campo vacío para que actúe el placeholder
            const result = isTopUsd ? numericVal * currentRate : numericVal / currentRate;
            if (inputBottom) inputBottom.value = numericVal !== 0 ? formatRAE(result) : '';
        } else {
            if (operationResult) operationResult.innerText = '';
            const result = isTopUsd ? numericVal / currentRate : numericVal * currentRate;
            if (inputTop) inputTop.value = numericVal !== 0 ? formatRAE(result, 0) : '';
        }

        updateInputColors();
    }

    if (inputTop) {
        inputTop.addEventListener('keydown', (e) => {
            if (isInitialDefault) {
                if (/^[0-9]$/.test(e.key)) {
                    inputTop.value = '';
                    isInitialDefault = false;
                } else if (e.key === 'Backspace' || e.key === 'Delete') {
                    inputTop.value = '';
                    isInitialDefault = false;
                    e.preventDefault();
                    if (inputBottom) inputBottom.value = '';
                    convert(inputTop);
                } else {
                    isInitialDefault = false;
                }
            }
        });
        inputTop.addEventListener('input', (e) => {
            isInitialDefault = false;
            formatInputDisplay(e.target);
            convert(inputTop);
        });
    }
    if (inputBottom) {
        inputBottom.addEventListener('input', (e) => {
            formatInputDisplay(e.target);
            convert(inputBottom);
        });
    }

    // Desactivar apertura de teclado móvil tanto en Android como en iOS (Safari/Chrome)
    [inputTop, inputBottom].forEach(el => {
        if (!el) return;
        el.addEventListener('focus', (e) => {
            if (el.hasAttribute('readonly')) {
                el.blur();
            }
        });
    });

    // Inicializar menú contextual estilo burbuja iOS/Android (long-press en mobile y clic derecho en desktop)
    const rowTop = document.getElementById("calc-row-top");
    const rowBottom = document.getElementById("calc-row-bottom");
    setupLongPressMenu(rowTop, inputTop, "top");
    setupLongPressMenu(rowBottom, inputBottom, "bottom");

    // Permitir al usuario tocar con el dedo o hacer clic con el ratón para mover el cursor
    if (inputTop) {
        inputTop.addEventListener('click', (e) => {
            setCursorFromEvent(e);
        });
        inputTop.addEventListener('touchend', (e) => {
            if (e.cancelable) {
                e.preventDefault();
            }
            setCursorFromEvent(e.changedTouches ? e.changedTouches[0] : e);
        }, { passive: false });
    }

    // Permitir mover el cursor con las flechas de teclado físico en PC
    window.addEventListener('keydown', (e) => {
        const activeTab = document.querySelector('.tab-content.active');
        if (!activeTab || activeTab.id !== 'calculator-tab-content') return;
        if (!inputTop) return;

        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            const currentPos = inputTop.selectionStart ?? inputTop.value.length;
            const newPos = Math.max(0, currentPos - 1);
            inputTop.setSelectionRange(newPos, newPos);
            updateCaretPosition();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            const currentPos = inputTop.selectionStart ?? inputTop.value.length;
            const newPos = Math.min(inputTop.value.length, currentPos + 1);
            inputTop.setSelectionRange(newPos, newPos);
            updateCaretPosition();
        } else if (e.key === 'Backspace') {
            const backspaceBtn = document.querySelector('.keypad-btn[data-key="backspace"]');
            if (backspaceBtn) backspaceBtn.click();
        } else if (e.key === 'Escape' || e.key.toLowerCase() === 'c') {
            const clearBtn = document.querySelector('.keypad-btn[data-key="C"]');
            if (clearBtn) clearBtn.click();
        } else if (e.key === 'Enter' || e.key === '=') {
            const operationResult = document.getElementById('operation-result');
            if (operationResult && operationResult.innerText.startsWith('=')) {
                const cleanRes = operationResult.innerText.replace(/[^0-9,]/g, '');
                inputTop.value = cleanRes;
                operationResult.innerText = '';
                isResultPendingClear = true;
                formatInputDisplay(inputTop);
                convert(inputTop);
                updateCaretPosition();
                updateInputColors();
            }
        } else if (/^[0-9,+\-*\/x]$/i.test(e.key)) {
            let key = e.key;
            if (key === '*') key = 'x';
            if (key === '.') key = ',';
            const btn = document.querySelector(`.keypad-btn[data-key="${key}"]`);
            if (btn) btn.click();
        }
    });

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js');
        });
    }

    /* =========================================
       CONSTANTES & UTILIDADES PAGO MÓVIL
       ========================================= */
    const PM_STORAGE_KEY = "cambi_pagomovil_profile";

    const BANK_NAMES = {
        "0102": "Banco de Venezuela",
        "0104": "Venezolano de Crédito",
        "0105": "Mercantil Banco",
        "0108": "BBVA Banco Provincial",
        "0114": "Bancaribe",
        "0115": "Banco Exterior",
        "0128": "Banco Caroní",
        "0134": "Banesco Banco Universal",
        "0137": "Banco Sofitasa",
        "0138": "Banco Plaza",
        "0146": "Bangente",
        "0151": "BFC Banco Fondo Común",
        "0156": "100% Banco",
        "0157": "DelSur Banco Universal",
        "0163": "Banco del Tesoro",
        "0166": "Banco Agrícola de Venezuela",
        "0168": "Bancrecer",
        "0169": "Mi Banco",
        "0171": "Banco Activo",
        "0172": "Bancamiga Banco Universal",
        "0174": "Banplus Banco Universal",
        "0175": "Banco Bicentenario",
        "0177": "BANFANB",
        "0191": "Banco Nacional de Crédito (BNC)"
    };

    const BANK_SHORT = {
        "0102": "Venezuela",
        "0104": "Ven. Crédito",
        "0105": "Mercantil",
        "0108": "Provincial",
        "0114": "Bancaribe",
        "0115": "Exterior",
        "0128": "Caroní",
        "0134": "Banesco",
        "0137": "Sofitasa",
        "0138": "Plaza",
        "0146": "Bangente",
        "0151": "BFC",
        "0156": "100% Banco",
        "0157": "DelSur",
        "0163": "Tesoro",
        "0166": "Agrícola",
        "0168": "Bancrecer",
        "0169": "Mi Banco",
        "0171": "Activo",
        "0172": "Bancamiga",
        "0174": "Banplus",
        "0175": "Bicentenario",
        "0177": "BANFANB",
        "0191": "BNC"
    };

    const getPmProfile = () => {
        try {
            const raw = localStorage.getItem(PM_STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    };

    const setPmProfile = (profile) => {
        localStorage.setItem(PM_STORAGE_KEY, JSON.stringify(profile));
    };

    // =======================================================
    // MÓDULO DE AJUSTES & PREFERENCIAS (MATERIAL YOU 3)
    // =======================================================
    const settingsScreen = document.getElementById("settings-screen");
    const settingsScrim = document.getElementById("settings-scrim");
    const openSettings = document.getElementById("open-settings");
    const closeSettings = document.getElementById("close-settings");
    const darkModeToggle = document.getElementById("dark-mode-toggle");

    const openSettingsDrawer = () => {
        if (settingsScreen) settingsScreen.classList.add("active");
        if (settingsScrim) settingsScrim.classList.add("active");
    };

    const closeSettingsDrawer = () => {
        if (settingsScreen) settingsScreen.classList.remove("active");
        if (settingsScrim) settingsScrim.classList.remove("active");
    };

    const updateThemeColor = (isDark) => {
        const color = isDark ? "#3B454E" : "#F3EDF7";
        const themeColorMeta = document.querySelector('meta[name="theme-color"]');
        if (themeColorMeta) {
            themeColorMeta.setAttribute("content", color);
        }
        document.documentElement.style.backgroundColor = color;
        document.body.style.backgroundColor = color;
        document.querySelectorAll("header").forEach(h => {
            h.style.backgroundColor = color;
        });
        document.querySelectorAll(".bottom-navigation").forEach(n => {
            n.style.backgroundColor = color;
        });
        const settings = document.getElementById("settings-screen");
        if (settings) settings.style.backgroundColor = isDark ? "#262C33" : "#FEF7FF";
    };

    if (openSettings) {
        openSettings.onclick = (e) => {
            e.stopPropagation();
            openSettingsDrawer();
        };
    }
    if (closeSettings) {
        closeSettings.onclick = () => closeSettingsDrawer();
    }
    if (settingsScrim) {
        settingsScrim.onclick = () => closeSettingsDrawer();
    }

    // Cerrar ajustes al hacer clic afuera en desktop
    document.addEventListener("click", (e) => {
        if (settingsScreen && settingsScreen.classList.contains("active") && window.innerWidth >= 900) {
            if (!settingsScreen.contains(e.target) && !openSettings.contains(e.target)) {
                closeSettingsDrawer();
            }
        }
    });

    // 1. Modo Oscuro
    if (darkModeToggle) {
        darkModeToggle.addEventListener("change", () => {
            const isDark = darkModeToggle.checked;
            document.body.classList.add("theme-transitioning");
            if (isDark) {
                document.documentElement.classList.add("dark-mode");
                document.body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "enabled");
            } else {
                document.documentElement.classList.remove("dark-mode");
                document.body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "disabled");
            }
            updateThemeColor(isDark);
            setTimeout(() => document.body.classList.remove("theme-transitioning"), 350);
        });
    }

    const isDarkModeEnabled = document.documentElement.classList.contains("dark-mode") || localStorage.getItem("darkMode") === "enabled";
    if (isDarkModeEnabled) {
        document.documentElement.classList.add("dark-mode");
        document.body.classList.add("dark-mode");
        if (darkModeToggle) darkModeToggle.checked = true;
    }
    updateThemeColor(isDarkModeEnabled);

    // 2. Color de Acento Material You
    const savedAccent = localStorage.getItem("cambiAccentColor") || "mint";
    const applyAccentColor = (accent) => {
        document.documentElement.dataset.accent = accent;
        document.body.dataset.accent = accent;
        localStorage.setItem("cambiAccentColor", accent);
        document.querySelectorAll(".accent-color-btn").forEach(btn => {
            btn.classList.toggle("active", btn.dataset.color === accent);
        });
    };
    applyAccentColor(savedAccent);

    document.querySelectorAll(".accent-color-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            applyAccentColor(btn.dataset.color);
            showToast("Color de acento actualizado");
        });
    });

    // 3. Tasa por Defecto en la Calculadora
    const defaultRateSelect = document.getElementById("settings-default-rate");
    if (defaultRateSelect) {
        const currentSavedRate = localStorage.getItem("defaultRateType") || "usd";
        defaultRateSelect.value = currentSavedRate;
        defaultRateSelect.addEventListener("change", (e) => {
            const newRateType = e.target.value;
            localStorage.setItem("defaultRateType", newRateType);
            setCalcRateType(newRateType);
            showToast("Tasa por defecto actualizada");
        });
    }

    // 4. Vibración Háptica
    const hapticToggle = document.getElementById("haptic-toggle");
    if (hapticToggle) {
        hapticToggle.checked = localStorage.getItem("hapticFeedback") !== "disabled";
        hapticToggle.addEventListener("change", () => {
            const isEnabled = hapticToggle.checked;
            localStorage.setItem("hapticFeedback", isEnabled ? "enabled" : "disabled");
            if (isEnabled && navigator.vibrate) navigator.vibrate(12);
        });
    }

    // 5. Pantalla de Inicio (Tasas vs Calculadora vs Pago Móvil)
    const startTabSelect = document.getElementById("settings-start-tab");
    if (startTabSelect) {
        let savedStartTab = localStorage.getItem("startTab") || "rates";
        if (savedStartTab === "dashboard") savedStartTab = "rates";
        startTabSelect.value = savedStartTab;
        startTabSelect.addEventListener("change", (e) => {
            localStorage.setItem("startTab", e.target.value);
            showToast("Pantalla de inicio guardada");
        });
    }

    // 6. Formato de Copiado de Pago Móvil
    const copyFormatSelect = document.getElementById("settings-copy-format");
    const copyPreviewEl = document.getElementById("settings-copy-preview");

    const getSampleOrRealCopyPreview = (fmt) => {
        const profile = getPmProfile();
        const hasData = profile && profile.selectedBank && profile.phoneNum && profile.docNum;

        const bankCode = hasData ? profile.selectedBank : "0134";
        const bankName = hasData ? (BANK_SHORT[bankCode] || BANK_NAMES[bankCode] || "Banesco") : "Banesco";
        const phone = hasData ? `${profile.phonePrefix}${profile.phoneNum}` : "04121234567";
        const docNum = hasData ? profile.docNum : "12345678";
        const docType = hasData ? (profile.docType || "V") : "V";
        const holder = hasData ? (profile.holder || "Juan Pérez") : "Juan Pérez";

        if (fmt === "complete") {
            const holderLine = holder ? `${holder}\n` : "";
            return `${holderLine}${docType}-${docNum}\n${bankName} (${bankCode})\n${phone}`;
        } else if (fmt === "compact") {
            const holderPart = holder ? ` • ${holder}` : "";
            return `${bankName} (${bankCode}) • ${phone} • ${docType}-${docNum}${holderPart}`;
        } else {
            return `${bankName} (${bankCode})\n${phone}\n${docNum}`;
        }
    };

    const updateCopyPreview = (fmt) => {
        if (!copyPreviewEl) return;
        copyPreviewEl.textContent = getSampleOrRealCopyPreview(fmt);
    };

    if (copyFormatSelect) {
        const savedFormat = localStorage.getItem("pmCopyFormat") || "simple";
        copyFormatSelect.value = savedFormat;
        updateCopyPreview(savedFormat);
        copyFormatSelect.addEventListener("change", (e) => {
            const fmt = e.target.value;
            localStorage.setItem("pmCopyFormat", fmt);
            updateCopyPreview(fmt);
            showToast("Formato de copiado actualizado");
        });
    }

    // 7. Copia de Seguridad (Exportar / Importar)
    const btnExport = document.getElementById("btn-export-backup");
    if (btnExport) {
        btnExport.addEventListener("click", () => {
            const profile = getPmProfile();
            if (!profile || !profile.banks || profile.banks.length === 0) {
                showToast("No hay bancos guardados para exportar");
                return;
            }
            const backupData = {
                appName: "Cambi",
                version: "v113",
                exportedAt: new Date().toISOString(),
                pmProfile: profile,
                settings: {
                    darkMode: localStorage.getItem("darkMode"),
                    cambiAccentColor: localStorage.getItem("cambiAccentColor"),
                    defaultRateType: localStorage.getItem("defaultRateType"),
                    hapticFeedback: localStorage.getItem("hapticFeedback"),
                    startTab: localStorage.getItem("startTab"),
                    pmCopyFormat: localStorage.getItem("pmCopyFormat")
                }
            };
            const jsonBlob = new Blob([JSON.stringify(backupData, null, 2)], { type: "application/json" });
            const url = URL.createObjectURL(jsonBlob);
            const a = document.createElement("a");
            const dateStr = new Date().toISOString().slice(0, 10);
            a.href = url;
            a.download = `cambi-respaldo-${dateStr}.json`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
            showToast("¡Respaldo exportado con éxito!");
        });
    }

    const btnImport = document.getElementById("btn-import-backup");
    const backupInput = document.getElementById("backup-file-input");
    if (btnImport && backupInput) {
        btnImport.addEventListener("click", () => {
            backupInput.value = "";
            backupInput.click();
        });
        backupInput.addEventListener("change", (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    const parsed = JSON.parse(event.target.result);
                    if (parsed && parsed.pmProfile && parsed.pmProfile.phoneNum) {
                        setPmProfile(parsed.pmProfile);
                        if (parsed.settings) {
                            if (parsed.settings.cambiAccentColor) {
                                applyAccentColor(parsed.settings.cambiAccentColor);
                            }
                            if (parsed.settings.defaultRateType) {
                                localStorage.setItem("defaultRateType", parsed.settings.defaultRateType);
                                if (defaultRateSelect) defaultRateSelect.value = parsed.settings.defaultRateType;
                            }
                            if (parsed.settings.startTab) {
                                localStorage.setItem("startTab", parsed.settings.startTab);
                                if (startTabSelect) startTabSelect.value = parsed.settings.startTab;
                            }
                            if (parsed.settings.pmCopyFormat) {
                                localStorage.setItem("pmCopyFormat", parsed.settings.pmCopyFormat);
                                if (copyFormatSelect) copyFormatSelect.value = parsed.settings.pmCopyFormat;
                                updateCopyPreview(parsed.settings.pmCopyFormat);
                            }
                        }
                        renderPmView();
                        showToast("¡Bancos y datos restaurados!");
                    } else {
                        alert("El archivo seleccionado no tiene una estructura de respaldo válida de Cambi.");
                    }
                } catch (err) {
                    alert("Error al procesar el respaldo: " + err.message);
                }
            };
            reader.readAsText(file);
        });
    }

    // 8. Actualizar / Limpiar Caché
    const btnCheckUpdate = document.getElementById("btn-check-update");
    if (btnCheckUpdate) {
        btnCheckUpdate.addEventListener("click", () => {
            showToast("Comprobando actualizaciones...");
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(regs => {
                    for (let r of regs) r.update();
                    setTimeout(() => window.location.reload(true), 600);
                });
            } else {
                setTimeout(() => window.location.reload(true), 600);
            }
        });
    }

    // =======================================================
    // MOTOR DE SELECTORES PERSONALIZADOS M3 (AJUSTES)
    // =======================================================
    const closeAllSettingsCustomSelects = () => {
        document.querySelectorAll(".settings-custom-select-wrap.open").forEach(w => {
            w.classList.remove("open");
            w.classList.remove("dropup");
            const btn = w.querySelector(".settings-custom-select-trigger");
            if (btn) btn.setAttribute("aria-expanded", "false");
        });
    };
    window.closeAllSettingsCustomSelects = closeAllSettingsCustomSelects;

    const syncSettingsCustomSelect = (selectId) => {
        const select = document.getElementById(selectId);
        if (!select) return;
        const wrap = document.getElementById(`wrap-${selectId}`);
        if (!wrap) return;
        const textSpan = wrap.querySelector(".settings-custom-select-text");
        const items = wrap.querySelectorAll(".settings-custom-select-item");
        
        items.forEach(item => {
            const isSelected = item.dataset.value === select.value;
            item.classList.toggle("selected", isSelected);
            if (isSelected && textSpan) {
                const label = item.querySelector("span");
                if (label) textSpan.textContent = label.textContent;
            }
        });
    };
    window.syncSettingsCustomSelect = syncSettingsCustomSelect;

    const initSettingsCustomSelects = () => {
        const selects = document.querySelectorAll("select.settings-select");
        selects.forEach(select => {
            if (select.dataset.customized === "true") return;
            select.dataset.customized = "true";

            const wrap = document.createElement("div");
            wrap.className = "settings-custom-select-wrap";
            wrap.id = `wrap-${select.id}`;

            const trigger = document.createElement("button");
            trigger.type = "button";
            trigger.className = "settings-custom-select-trigger";
            trigger.id = `trigger-${select.id}`;
            trigger.setAttribute("aria-haspopup", "listbox");
            trigger.setAttribute("aria-expanded", "false");

            const textSpan = document.createElement("span");
            textSpan.className = "settings-custom-select-text";

            const arrowSpan = document.createElement("span");
            arrowSpan.className = "material-symbols-rounded settings-custom-select-arrow";
            arrowSpan.textContent = "keyboard_arrow_down";

            trigger.appendChild(textSpan);
            trigger.appendChild(arrowSpan);

            const dropdown = document.createElement("div");
            dropdown.className = "settings-custom-select-dropdown";
            dropdown.setAttribute("role", "listbox");

            Array.from(select.options).forEach(opt => {
                const item = document.createElement("div");
                item.className = "settings-custom-select-item";
                item.dataset.value = opt.value;

                const label = document.createElement("span");
                label.textContent = opt.text;
                item.appendChild(label);

                const check = document.createElement("span");
                check.className = "material-symbols-rounded settings-item-check";
                check.textContent = "check";
                item.appendChild(check);

                if (opt.value === select.value) {
                    item.classList.add("selected");
                    textSpan.textContent = opt.text;
                }

                item.addEventListener("click", (e) => {
                    e.stopPropagation();
                    select.value = opt.value;
                    select.dispatchEvent(new Event("change"));
                    syncSettingsCustomSelect(select.id);
                    closeAllSettingsCustomSelects();
                });

                dropdown.appendChild(item);
            });

            if (!textSpan.textContent) {
                const cur = select.options[select.selectedIndex];
                textSpan.textContent = cur ? cur.text : "";
            }

            trigger.addEventListener("click", (e) => {
                e.stopPropagation();
                const wasOpen = wrap.classList.contains("open");
                closeAllSettingsCustomSelects();
                if (!wasOpen) {
                    const rect = trigger.getBoundingClientRect();
                    const spaceBelow = window.innerHeight - rect.bottom;
                    if (spaceBelow < 220 && rect.top > 220) {
                        wrap.classList.add("dropup");
                    } else {
                        wrap.classList.remove("dropup");
                    }
                    wrap.classList.add("open");
                    trigger.setAttribute("aria-expanded", "true");
                }
            });

            select.parentNode.insertBefore(wrap, select);
            wrap.appendChild(select);
            wrap.appendChild(trigger);
            wrap.appendChild(dropdown);
        });
    };

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".settings-custom-select-wrap")) {
            closeAllSettingsCustomSelects();
        }
    });

    initSettingsCustomSelects();

    // Lógica del Teclado Numérico
    document.querySelectorAll('.keypad-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Respuesta háptica opcional
            if (localStorage.getItem("hapticFeedback") !== "disabled" && navigator.vibrate) {
                try { navigator.vibrate(10); } catch(e) {}
            }

            if (!inputTop) return;
            const key = btn.dataset.key;
            if (!key || key === 'swap') return;
            
            // Botón C (Clear / Limpiar todo -> vuelve al valor base por defecto según la divisa activa)
            if (key === 'C') {
                if (isTopUsd) {
                    inputTop.value = '1,00';
                } else {
                    inputTop.value = currentRate ? formatRAE(currentRate) : '1,00';
                }
                isInitialDefault = true;
                isResultPendingClear = false;
                const operationResult = document.getElementById('operation-result');
                if (operationResult) operationResult.innerText = '';
                formatInputDisplay(inputTop);
                convert(inputTop);
                if (customCaret) customCaret.style.display = 'none';
                updateInputColors();
                return;
            }

            // Teclas de navegación
            if (key === 'left' || key === 'right') {
                isInitialDefault = false;
                const currentPos = inputTop.selectionStart ?? inputTop.value.length;
                const newPos = key === 'left' ? currentPos - 1 : currentPos + 1;
                inputTop.setSelectionRange(newPos, newPos);
                updateCaretPosition();
                return;
            }

            if (key === 'backspace') {
                if (isInitialDefault) {
                    inputTop.value = '';
                    if (inputBottom) inputBottom.value = '';
                    isInitialDefault = false;
                    isResultPendingClear = false;
                    const operationResult = document.getElementById('operation-result');
                    if (operationResult) operationResult.innerText = '';
                    formatInputDisplay(inputTop);
                    convert(inputTop);
                    updateCaretPosition();
                    updateInputColors();
                    return;
                }
                isResultPendingClear = false;
                const start = inputTop.selectionStart ?? inputTop.value.length;
                const end = inputTop.selectionEnd ?? inputTop.value.length;
                if (start === end) {
                    if (start > 0) {
                        inputTop.value = inputTop.value.slice(0, start - 1) + inputTop.value.slice(end);
                        inputTop.setSelectionRange(start - 1, start - 1);
                    }
                } else {
                    inputTop.value = inputTop.value.slice(0, start) + inputTop.value.slice(end);
                    inputTop.setSelectionRange(start, start);
                }
            } else if (key === '=') {
                isInitialDefault = false;
                const operationResult = document.getElementById('operation-result');
                if (operationResult && operationResult.innerText.startsWith('=')) {
                    const cleanRes = operationResult.innerText.replace(/[^0-9,]/g, '');
                    inputTop.value = cleanRes;
                    operationResult.innerText = '';
                    isResultPendingClear = true;
                }
            } else {
                if (isInitialDefault) {
                    if (/^[0-9]$/.test(key)) {
                        inputTop.value = key;
                        inputTop.setSelectionRange(key.length, key.length);
                        isInitialDefault = false;
                        isResultPendingClear = false;
                        formatInputDisplay(inputTop);
                        convert(inputTop);
                        updateCaretPosition();
                        updateInputColors();
                        return;
                    } else if (key === ',') {
                        if (isTopUsd) {
                            inputTop.value = '1,';
                            inputTop.setSelectionRange(2, 2);
                        } else {
                            inputTop.value = '0,';
                            inputTop.setSelectionRange(2, 2);
                        }
                        isInitialDefault = false;
                        isResultPendingClear = false;
                        formatInputDisplay(inputTop);
                        convert(inputTop);
                        updateCaretPosition();
                        updateInputColors();
                        return;
                    } else if (['+', '-', 'x', '/'].includes(key)) {
                        inputTop.value = '1' + key;
                        inputTop.setSelectionRange(1 + key.length, 1 + key.length);
                        isInitialDefault = false;
                        isResultPendingClear = false;
                        formatInputDisplay(inputTop);
                        convert(inputTop);
                        updateCaretPosition();
                        updateInputColors();
                        return;
                    }
                }

                if (isResultPendingClear && !['+', '-', 'x', '/'].includes(key)) {
                    inputTop.value = '';
                }
                isResultPendingClear = false;
                isInitialDefault = false;

                const start = inputTop.selectionStart ?? inputTop.value.length;
                const end = inputTop.selectionEnd ?? inputTop.value.length;
                inputTop.value = inputTop.value.slice(0, start) + key + inputTop.value.slice(end);
                inputTop.setSelectionRange(start + key.length, start + key.length);
            }

            formatInputDisplay(inputTop);
            convert(inputTop);
            updateCaretPosition();
            updateInputColors();
        });
    });

    const navItems = document.querySelectorAll('.bottom-navigation .nav-item');
    const tabContents = document.querySelectorAll('.tab-content');
    const swapBtn = document.getElementById('swap-btn');
    const TABS_ORDER = ['rates', 'pagomovil', 'calculator'];

    const switchTab = (tabId, animate = true) => {
        let normalizedTabId = tabId;
        if (tabId === 'dashboard') {
            normalizedTabId = 'rates';
        }

        // Sincronizar clases de vista en desktop
        document.body.classList.remove('desktop-view-dashboard', 'desktop-view-pagomovil', 'desktop-view-history');
        if (tabId === 'pagomovil') {
            document.body.classList.add('desktop-view-pagomovil');
        } else if (tabId === 'history') {
            document.body.classList.add('desktop-view-history');
        } else {
            // 'dashboard', 'rates', 'calculator' activan la vista combinada de dashboard en desktop
            document.body.classList.add('desktop-view-dashboard');
        }

        // Sincronizar botones de navegación desktop en el header
        const desktopNav = document.getElementById('desktop-nav');
        if (desktopNav) {
            desktopNav.dataset.active = (tabId === 'pagomovil') ? 'pagomovil' : 'dashboard';
        }
        const desktopNavItems = document.querySelectorAll('.desktop-nav-item');
        desktopNavItems.forEach(item => {
            const itemTab = item.dataset.tab;
            const isMatch = (itemTab === 'dashboard' && (tabId === 'dashboard' || tabId === 'rates' || tabId === 'calculator')) ||
                            (itemTab === tabId);
            item.classList.toggle('active', isMatch);
        });

        const historyTab = document.getElementById('history-tab-content');
        if (tabId === 'history') {
            if (historyTab) historyTab.classList.add('active');
            if (window.innerWidth < 900) return;
        } else {
            if (historyTab) historyTab.classList.remove('active');
        }

        const targetIdx = TABS_ORDER.indexOf(normalizedTabId);
        if (targetIdx !== -1) {
            const tabsTrack = document.getElementById('tabs-track');
            if (tabsTrack && window.innerWidth < 900) {
                if (animate) {
                    tabsTrack.style.transition = 'transform 0.32s cubic-bezier(0.22, 1, 0.36, 1)';
                    void tabsTrack.offsetWidth;
                } else {
                    tabsTrack.style.transition = 'none';
                }
                tabsTrack.style.transform = `translate3d(-${targetIdx * (100 / 3)}%, 0, 0)`;
            }

            navItems.forEach(nav => {
                nav.classList.toggle('active', nav.dataset.tab === normalizedTabId);
            });
            tabContents.forEach(content => {
                content.classList.toggle('active', content.id === `${normalizedTabId}-tab-content`);
            });
        }

        if (inputTop) convert(inputTop);
        if (tabId === 'pagomovil') {
            document.querySelectorAll(".pm-modal-overlay").forEach(overlay => {
                overlay.addEventListener("click", (e) => {
                    if (e.target === overlay) {
                        overlay.classList.remove("active");
                    }
                });
            });
            renderPmView();
        } else {
            const fab = document.getElementById("pm-fab-container");
            if (fab) fab.classList.remove("active");
        }
    };

    window.switchTab = switchTab;

    if (swapBtn) {
        swapBtn.onclick = () => {
            isResultPendingClear = false;
            isInitialDefault = false;
            isTopUsd = !isTopUsd;

            swapBtn.style.transform = isTopUsd ? 'rotate(0deg)' : 'rotate(180deg)';

            if (labelTop && labelBottom) {
                const cur = getCurrencySymbolForRate(selectedRateType);
                if (isTopUsd) {
                    labelTop.innerText = cur;
                    labelBottom.innerText = "VES";
                } else {
                    labelTop.innerText = "VES";
                    labelBottom.innerText = cur;
                }
            }

            if (inputTop && inputBottom) {
                const valTop = inputTop.value;
                const valBottom = inputBottom.value;
                inputTop.value = valBottom;
                inputBottom.value = valTop;

                // Al invertir divisas, activamos el reemplazo automático para que el nuevo monto
                // se sobreescriba directamente al presionar cualquier número
                isInitialDefault = true;

                formatInputDisplay(inputTop);
                convert(inputTop);
                updateInputColors();
                if (customCaret) customCaret.style.display = 'none';
            }
        };
    }

    
    /* =========================================
       LÓGICA PAGO MÓVIL (LOCAL-FIRST)
       ========================================= */
    let currentPmSlide = 0;
    let pmModalMode = "create";

    const goToPmSlide = (index) => {
        currentPmSlide = index;
        const carousel = document.getElementById("pm-carousel");
        const dots = document.querySelectorAll(".pm-dot");
        if (carousel) {
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }
        dots.forEach((dot, idx) => {
            dot.classList.toggle("active", idx === index);
        });
    };
    window.goToPmSlide = goToPmSlide;

// showToast se encuentra definida de forma global más arriba

    const BANK_OFFICIAL_LOGOS = {
        "0102": "public/banks/0102.svg",
        "0104": "public/banks/0104.webp",
        "0105": "public/banks/0105.svg",
        "0108": "public/banks/0108.svg",
        "0114": "public/banks/0114.webp",
        "0115": "public/banks/0115.webp",
        "0128": "public/banks/0128.webp",
        "0134": "public/banks/0134.svg",
        "0137": "public/banks/0137.webp",
        "0138": "public/banks/0138.webp",
        "0146": "public/banks/0146.webp",
        "0151": "public/banks/0151.webp",
        "0156": "public/banks/0156.webp",
        "0157": "public/banks/0157.webp",
        "0163": "public/banks/0163.webp",
        "0166": "public/banks/0166.webp",
        "0168": "public/banks/0168.webp",
        "0169": "public/banks/0169.webp",
        "0171": "public/banks/0171.svg",
        "0172": "public/banks/0172.webp",
        "0174": "public/banks/0174.webp",
        "0175": "public/banks/0175.webp",
        "0177": "public/banks/0177.webp",
        "0191": "public/banks/0191.webp"
    };

    const getBankLogoHtml = (bankCode) => {
        const logoUrl = BANK_OFFICIAL_LOGOS[bankCode];
        const name = BANK_NAMES[bankCode] || "Banco";
        if (logoUrl) {
            return `<img src="${logoUrl}" alt="${name}" loading="eager" decoding="async" />`;
        }
        return `<span class="material-symbols-rounded">account_balance</span>`;
    };
    window.getBankLogoHtml = getBankLogoHtml;
    // Pre-cargar logos de bancos en memoria caché del navegador en background
    function preloadBankLogos() {
        if (window._bankLogosPreloaded) return;
        window._bankLogosPreloaded = true;
        Object.values(BANK_OFFICIAL_LOGOS).forEach(url => {
            const img = new Image();
            img.decoding = "async";
            img.src = url;
        });
    }

    if (window.requestIdleCallback) {
        requestIdleCallback(preloadBankLogos, { timeout: 2000 });
    } else {
        setTimeout(preloadBankLogos, 800);
    }


    const renderPmView = () => {
        if (typeof updateCopyPreview === "function") {
            updateCopyPreview(localStorage.getItem("pmCopyFormat") || "simple");
        }
        const profile = getPmProfile();
        const onboardingView = document.getElementById("pm-onboarding-view");
        const activeView = document.getElementById("pm-active-view");
        if (!onboardingView || !activeView) return;

        if (!profile || !profile.banks || profile.banks.length === 0) {
            onboardingView.style.display = "flex";
            activeView.style.display = "none";
            const fab = document.getElementById("pm-fab-container");
            if (fab) fab.classList.remove("active");
            goToPmSlide(0);
            return;
        }

        onboardingView.style.display = "none";
        activeView.style.display = "flex";

        // Render profile info in header
        const displayHolder = document.getElementById("pm-display-holder");
        const displayDoc = document.getElementById("pm-display-doc");
        const displayPhone = document.getElementById("pm-display-phone");
        const countBadge = document.getElementById("pm-banks-count-badge");

        if (displayHolder) displayHolder.innerText = profile.holder;
        if (displayDoc) displayDoc.innerText = `${profile.docType}-${profile.docNum}`;
        if (displayPhone) displayPhone.innerText = `${profile.phonePrefix}-${profile.phoneNum}`;
        if (countBadge) countBadge.innerText = profile.banks.length;

        // Ensure selectedBank is valid
        if (!profile.selectedBank || !profile.banks.includes(profile.selectedBank)) {
            profile.selectedBank = profile.banks[0];
            setPmProfile(profile);
        }

        // Render colorful pastel bank blocks with logos
        const grid = document.getElementById("pm-banks-grid");
        if (grid) {
            grid.innerHTML = "";
            profile.banks.forEach(bankCode => {
                const block = document.createElement("div");
                const pastelClass = `pm-bank-pastel-${bankCode}`;
                block.className = `pm-bank-block ${pastelClass}`;
                block.setAttribute("role", "button");
                block.setAttribute("tabindex", "0");
                block.onclick = () => openPmDetailModal(bankCode);

                const fullName = BANK_NAMES[bankCode] || `Banco (${bankCode})`;
                const shortName = BANK_SHORT[bankCode] || fullName;
                const logoHtml = getBankLogoHtml(bankCode);

                block.innerHTML = `
                    <div class="pm-block-top">
                        <div class="pm-block-badge">
                            <div class="pm-block-logo">
                                ${logoHtml}
                            </div>
                            <span class="pm-block-code">${bankCode}</span>
                        </div>
                        <span class="material-symbols-rounded pm-block-chevron">chevron_right</span>
                    </div>
                    <div class="pm-block-bottom">
                        <h4 class="pm-block-name">${shortName}</h4>
                        <span class="pm-block-sub">${profile.phonePrefix}-${profile.phoneNum}</span>
                    </div>
                `;
                grid.appendChild(block);
            });
        }

        applyPmGridLayout(getPmGridLayout());

        const fab = document.getElementById("pm-fab-container");
        if (fab) {
            const isPmTab = document.getElementById("pagomovil-tab-content")?.classList.contains("active");
            fab.classList.toggle("active", Boolean(isPmTab));
        }
    };
    window.renderPmView = renderPmView;

    // ==========================================
    // GRID LAYOUT TOGGLE (1 Columna vs 2 Columnas)
    // ==========================================
    const getPmGridLayout = () => {
        return localStorage.getItem("pm_grid_layout") || "2-col";
    };

    const applyPmGridLayout = (layout) => {
        const grid = document.getElementById("pm-banks-grid");
        const toggleBtn = document.getElementById("pm-grid-toggle-btn");
        const toggleIcon = document.getElementById("pm-grid-toggle-icon");
        if (toggleBtn) toggleBtn.blur();
        if (!grid) return;

        if (layout === "1-col") {
            grid.classList.add("single-col");
            if (toggleIcon) toggleIcon.innerText = "grid_view";
            if (toggleBtn) toggleBtn.setAttribute("title", "Cambiar a vista de 2 columnas");
        } else {
            grid.classList.remove("single-col");
            if (toggleIcon) toggleIcon.innerText = "view_agenda";
            if (toggleBtn) toggleBtn.setAttribute("title", "Cambiar a vista de 1 columna");
        }
    };

    const togglePmGridLayout = () => {
        const toggleBtn = document.getElementById("pm-grid-toggle-btn");
        if (toggleBtn) toggleBtn.blur();
        const current = getPmGridLayout();
        const next = current === "1-col" ? "2-col" : "1-col";
        localStorage.setItem("pm_grid_layout", next);
        applyPmGridLayout(next);
    };
    window.togglePmGridLayout = togglePmGridLayout;

    const openPmDetailModal = (bankCode) => {
        const profile = getPmProfile();
        if (!profile) return;

        profile.selectedBank = bankCode;
        setPmProfile(profile);

        const modal = document.getElementById("pm-modal-detail");
        const badgeWrap = document.getElementById("pm-detail-badge");
        const bankNameEl = document.getElementById("pm-detail-bank-name");
        const bankCodeEl = document.getElementById("pm-detail-bank-code");
        const phoneEl = document.getElementById("pm-detail-phone");
        const docEl = document.getElementById("pm-detail-doc");
        const holderEl = document.getElementById("pm-detail-holder");

        const fullName = BANK_NAMES[bankCode] || `Banco (${bankCode})`;

        if (badgeWrap) {
            badgeWrap.className = `pm-detail-bank-badge pm-bank-pastel-${bankCode}`;
        }
        const logoContainer = document.getElementById("pm-detail-logo-container");
        if (logoContainer) {
            logoContainer.innerHTML = getBankLogoHtml(bankCode);
        }

        if (bankNameEl) bankNameEl.innerText = fullName;
        if (bankCodeEl) bankCodeEl.innerText = bankCode;
        if (phoneEl) phoneEl.innerText = `${profile.phonePrefix}-${profile.phoneNum}`;
        if (docEl) docEl.innerText = `${profile.docType}-${profile.docNum}`;
        if (holderEl) holderEl.innerText = profile.holder;

        if (modal) modal.classList.add("active");
    };
    window.openPmDetailModal = openPmDetailModal;

    const closePmDetailModal = () => {
        const modal = document.getElementById("pm-modal-detail");
        if (modal) modal.classList.remove("active");
    };
    window.closePmDetailModal = closePmDetailModal;

    const copySinglePmField = (type) => {
        const profile = getPmProfile();
        if (!profile) return;
        let textToCopy = "";
        let msg = "";
        if (type === "phone") {
            textToCopy = `${profile.phonePrefix}${profile.phoneNum}`;
            msg = "¡Teléfono copiado!";
        } else if (type === "doc") {
            textToCopy = `${profile.docType}-${profile.docNum}`;
            msg = "¡Cédula/RIF copiado!";
        }
        if (!textToCopy) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textToCopy).then(() => {
                showToast(msg);
            }).catch(() => {
                fallbackCopy(textToCopy);
            });
        } else {
            fallbackCopy(textToCopy);
        }
    };
    window.copySinglePmField = copySinglePmField;

    // =========================================
    // MOTOR DE SELECTORES PERSONALIZADOS M3 (PAGO MÓVIL)
    // =========================================
    const initCustomSelects = () => {
        const selects = document.querySelectorAll("select.pm-styled-select");
        selects.forEach(select => {
            if (select.dataset.customized === "true") return;
            select.dataset.customized = "true";

            const parentGroup = select.closest(".pm-form-group");
            if (!parentGroup) return;

            const wrap = document.createElement("div");
            wrap.className = "pm-custom-select-wrap";
            wrap.id = `wrap-${select.id}`;

            const trigger = document.createElement("button");
            trigger.type = "button";
            trigger.className = "pm-custom-select-trigger";
            trigger.id = `trigger-${select.id}`;
            trigger.setAttribute("aria-haspopup", "listbox");
            trigger.setAttribute("aria-expanded", "false");

            const textSpan = document.createElement("span");
            textSpan.className = "pm-custom-select-text";

            const arrowSpan = document.createElement("span");
            arrowSpan.className = "material-symbols-rounded pm-custom-select-arrow";
            arrowSpan.textContent = "keyboard_arrow_down";

            trigger.appendChild(textSpan);
            trigger.appendChild(arrowSpan);

            const dropdown = document.createElement("div");
            dropdown.className = "pm-custom-select-dropdown";
            dropdown.setAttribute("role", "listbox");

            Array.from(select.options).forEach(opt => {
                const item = document.createElement("div");
                item.className = "pm-custom-select-item";
                item.dataset.value = opt.value;

                const label = document.createElement("span");
                label.textContent = opt.text;
                item.appendChild(label);

                const check = document.createElement("span");
                check.className = "material-symbols-rounded pm-item-check";
                check.textContent = "check";
                item.appendChild(check);

                if (opt.value === select.value) {
                    item.classList.add("selected");
                    textSpan.textContent = opt.text;
                    if (!opt.value) textSpan.classList.add("placeholder");
                }

                item.addEventListener("click", (e) => {
                    e.stopPropagation();
                    select.value = opt.value;
                    select.dispatchEvent(new Event("change"));
                    syncCustomSelect(select.id);
                    closeAllCustomSelects();
                });

                dropdown.appendChild(item);
            });

            if (!textSpan.textContent) {
                const cur = select.options[select.selectedIndex];
                textSpan.textContent = cur ? cur.text : "Selecciona una opción";
                if (!select.value) textSpan.classList.add("placeholder");
            }

            trigger.addEventListener("click", (e) => {
                e.stopPropagation();
                const wasOpen = wrap.classList.contains("open");
                closeAllCustomSelects();
                if (!wasOpen) {
                    const rect = trigger.getBoundingClientRect();
                    const spaceBelow = window.innerHeight - rect.bottom;
                    if (spaceBelow < 230 && rect.top > 230) {
                        wrap.classList.add("dropup");
                    } else {
                        wrap.classList.remove("dropup");
                    }
                    wrap.classList.add("open");
                    trigger.setAttribute("aria-expanded", "true");

                    const selItem = dropdown.querySelector(".pm-custom-select-item.selected");
                    if (selItem) {
                        setTimeout(() => selItem.scrollIntoView({ block: "nearest", behavior: "smooth" }), 60);
                    }
                }
            });

            wrap.appendChild(trigger);
            wrap.appendChild(dropdown);
            parentGroup.appendChild(wrap);
        });
    };

    const closeAllCustomSelects = () => {
        document.querySelectorAll(".pm-custom-select-wrap.open").forEach(w => {
            w.classList.remove("open");
            const trg = w.querySelector(".pm-custom-select-trigger");
            if (trg) trg.setAttribute("aria-expanded", "false");
        });
    };

    const syncCustomSelect = (selectId) => {
        const select = document.getElementById(selectId);
        if (!select) return;
        const wrap = document.getElementById(`wrap-${selectId}`);
        if (!wrap) return;

        const textSpan = wrap.querySelector(".pm-custom-select-text");
        const items = wrap.querySelectorAll(".pm-custom-select-item");

        let matched = false;
        items.forEach(item => {
            if (item.dataset.value === select.value) {
                item.classList.add("selected");
                if (textSpan) {
                    textSpan.textContent = item.querySelector("span").textContent;
                    if (!select.value) {
                        textSpan.classList.add("placeholder");
                    } else {
                        textSpan.classList.remove("placeholder");
                    }
                }
                matched = true;
            } else {
                item.classList.remove("selected");
            }
        });

        if (!matched && textSpan) {
            const cur = select.options[select.selectedIndex];
            textSpan.textContent = cur ? cur.text : "Selecciona una opción";
            if (!select.value) textSpan.classList.add("placeholder");
            else textSpan.classList.remove("placeholder");
        }
    };

    const syncAllCustomSelects = () => {
        document.querySelectorAll("select.pm-styled-select").forEach(sel => {
            syncCustomSelect(sel.id);
        });
    };

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".pm-custom-select-wrap")) {
            closeAllCustomSelects();
        }
    });

    const openPmModal = (mode) => {
        pmModalMode = mode;
        const modal = document.getElementById("pm-modal-profile");
        const title = document.getElementById("pm-modal-title");
        const subtitle = document.getElementById("pm-modal-subtitle");
        const bankGroup = document.getElementById("pm-bank-select-group");
        const bankSelect = document.getElementById("pm-input-bank");
        const inputHolder = document.getElementById("pm-input-holder");
        const inputDocType = document.getElementById("pm-input-doc-type");
        const inputDocNum = document.getElementById("pm-input-doc-num");
        const inputPhonePrefix = document.getElementById("pm-input-phone-prefix");
        const inputPhoneNum = document.getElementById("pm-input-phone-num");

        if (!modal) return;

        const profile = getPmProfile();

        if (mode === "edit" && profile) {
            if (title) title.innerText = "Editar Datos Personales";
            if (subtitle) subtitle.innerText = "Modifica los datos asociados a todos tus pagos móviles.";
            if (bankGroup) bankGroup.style.display = "none";
            if (bankSelect) bankSelect.removeAttribute("required");
            if (inputHolder) inputHolder.value = profile.holder || "";
            if (inputDocType) inputDocType.value = profile.docType || "V";
            if (inputDocNum) inputDocNum.value = profile.docNum || "";
            if (inputPhonePrefix) inputPhonePrefix.value = profile.phonePrefix || "0412";
            if (inputPhoneNum) inputPhoneNum.value = profile.phoneNum || "";
        } else {
            if (title) title.innerText = "Configurar mi primer Pago Móvil";
            if (subtitle) subtitle.innerText = "Configura tus datos base una sola vez y añade tu primer banco para comenzar.";
            if (bankGroup) bankGroup.style.display = "flex";
            if (bankSelect) {
                bankSelect.setAttribute("required", "required");
                bankSelect.value = "";
            }
            if (inputHolder) inputHolder.value = "";
            if (inputDocType) inputDocType.value = "V";
            if (inputDocNum) inputDocNum.value = "";
            if (inputPhonePrefix) inputPhonePrefix.value = "0412";
            if (inputPhoneNum) inputPhoneNum.value = "";
        }

        syncAllCustomSelects();
        closeAllCustomSelects();
        modal.classList.add("active");
    };
    window.openPmModal = openPmModal;

    const closePmModal = () => {
        closeAllCustomSelects();
        const modal = document.getElementById("pm-modal-profile");
        if (modal) modal.classList.remove("active");
    };
    window.closePmModal = closePmModal;

    const savePmProfile = (e) => {
        e.preventDefault();
        const inputHolder = document.getElementById("pm-input-holder").value.trim();
        const inputDocType = document.getElementById("pm-input-doc-type").value;
        const inputDocNum = document.getElementById("pm-input-doc-num").value.trim().replace(/\D/g, "");
        const inputPhonePrefix = document.getElementById("pm-input-phone-prefix").value;
        const inputPhoneNum = document.getElementById("pm-input-phone-num").value.trim().replace(/\D/g, "");
        const bankSelect = document.getElementById("pm-input-bank");
        const selectedBank = bankSelect ? bankSelect.value : "";

        if (!inputHolder || !inputDocNum || !inputPhoneNum) {
            alert("Por favor completa todos los campos requeridos.");
            return;
        }

        let profile = getPmProfile() || { banks: [] };
        profile.holder = inputHolder;
        profile.docType = inputDocType;
        profile.docNum = inputDocNum;
        profile.phonePrefix = inputPhonePrefix;
        profile.phoneNum = inputPhoneNum;

        if (pmModalMode === "create") {
            if (!selectedBank) {
                alert("Por favor selecciona tu banco principal.");
                return;
            }
            if (!profile.banks.includes(selectedBank)) {
                profile.banks.push(selectedBank);
            }
            profile.selectedBank = selectedBank;
        }

        setPmProfile(profile);
        closePmModal();
        renderPmView();
        showToast("¡Datos guardados con éxito!");
    };
    window.savePmProfile = savePmProfile;

    const normalizeSearchStr = (str) => {
        return (str || "")
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    };

    const renderBankSearchResults = (query = "") => {
        const container = document.getElementById("pm-bank-search-results");
        if (!container) return;

        const profile = getPmProfile() || { banks: [] };
        const userBanks = profile.banks || [];
        const normQ = normalizeSearchStr(query);

        const bankEntries = Object.entries(BANK_NAMES);

        const filtered = bankEntries.filter(([code, name]) => {
            if (!normQ) return true;
            const normCode = normalizeSearchStr(code);
            const normName = normalizeSearchStr(name);
            const normShort = normalizeSearchStr(BANK_SHORT[code] || "");
            return normCode.includes(normQ) || normName.includes(normQ) || normShort.includes(normQ);
        });

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="pm-bank-search-empty">
                    <span class="material-symbols-rounded" style="font-size: 2rem; display: block; margin-bottom: 6px; opacity: 0.6;">search_off</span>
                    No se encontraron bancos para "${query}"
                </div>`;
            return;
        }

        container.innerHTML = filtered.map(([code, name]) => {
            const isAdded = userBanks.includes(code);
            const logoHtml = getBankLogoHtml(code);

            if (isAdded) {
                return `
                    <div class="pm-bank-search-item pm-bank-item-disabled" title="Este banco ya está en tu lista">
                        <div class="pm-bank-item-left">
                            <div class="pm-bank-item-logo">${logoHtml}</div>
                            <div class="pm-bank-item-info">
                                <span class="pm-bank-item-name">${name}</span>
                                <span class="pm-bank-item-code">Código: ${code}</span>
                            </div>
                        </div>
                        <div class="pm-bank-item-action">
                            <span class="pm-bank-item-added-badge">
                                <span class="material-symbols-rounded" style="font-size: 0.95rem;">check</span>
                                Añadido
                            </span>
                        </div>
                    </div>`;
            }

            return `
                <button type="button" class="pm-bank-search-item" onclick="addBankToProfile('${code}')">
                    <div class="pm-bank-item-left">
                        <div class="pm-bank-item-logo">${logoHtml}</div>
                        <div class="pm-bank-item-info">
                            <span class="pm-bank-item-name">${name}</span>
                            <span class="pm-bank-item-code">Código: ${code}</span>
                        </div>
                    </div>
                    <div class="pm-bank-item-action">
                        <span class="material-symbols-rounded">add_circle</span>
                    </div>
                </button>`;
        }).join("");
    };
    window.renderBankSearchResults = renderBankSearchResults;

    const filterBankSearchResults = (query) => {
        const clearBtn = document.getElementById("pm-bank-search-clear");
        if (clearBtn) clearBtn.style.display = query.trim() ? "inline-flex" : "none";
        renderBankSearchResults(query);
    };
    window.filterBankSearchResults = filterBankSearchResults;

    const clearBankSearch = () => {
        const input = document.getElementById("pm-bank-search-input");
        const clearBtn = document.getElementById("pm-bank-search-clear");
        if (input) {
            input.value = "";
            input.focus();
        }
        if (clearBtn) clearBtn.style.display = "none";
        renderBankSearchResults("");
    };
    window.clearBankSearch = clearBankSearch;

    const addBankToProfile = (bankCode) => {
        if (!bankCode) return;
        const profile = getPmProfile();
        if (!profile) return;

        profile.banks = profile.banks || [];
        if (!profile.banks.includes(bankCode)) {
            profile.banks.push(bankCode);
        }
        profile.selectedBank = bankCode;
        setPmProfile(profile);
        closeAddBankModal();
        renderPmView();
        const bName = BANK_NAMES[bankCode] || "Banco";
        showToast(`¡${bName} añadido con éxito!`);
        openPmDetailModal(bankCode);
    };
    window.addBankToProfile = addBankToProfile;

    const openAddBankModal = () => {
        const modal = document.getElementById("pm-modal-add-bank");
        const input = document.getElementById("pm-bank-search-input");
        const clearBtn = document.getElementById("pm-bank-search-clear");
        if (input) input.value = "";
        if (clearBtn) clearBtn.style.display = "none";
        renderBankSearchResults("");
        if (modal) modal.classList.add("active");
        
        // En mobile/touch evitamos autofoco para que no levante el teclado virtual cubriendo la lista de bancos
        const isMobile = window.matchMedia("(max-width: 768px), (pointer: coarse)").matches || ('ontouchstart' in window);
        if (!isMobile) {
            setTimeout(() => input?.focus(), 150);
        }
    };
    window.openAddBankModal = openAddBankModal;

    const closeAddBankModal = () => {
        const modal = document.getElementById("pm-modal-add-bank");
        const input = document.getElementById("pm-bank-search-input");
        if (input) input.blur();
        if (modal) modal.classList.remove("active");
    };
    window.closeAddBankModal = closeAddBankModal;

    const confirmDeleteCurrentBank = () => {
        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;

        const bankName = BANK_NAMES[profile.selectedBank] || profile.selectedBank;
        const desc = document.getElementById("pm-confirm-bank-desc");
        if (desc) {
            desc.innerHTML = `¿Estás seguro de que deseas eliminar <strong>${bankName}</strong> de tus pagos móviles?`;
        }
        const modal = document.getElementById("pm-modal-confirm-delete");
        if (modal) modal.classList.add("active");
    };
    window.confirmDeleteCurrentBank = confirmDeleteCurrentBank;
    window.deleteCurrentBank = confirmDeleteCurrentBank;

    const closePmConfirmDeleteModal = () => {
        const modal = document.getElementById("pm-modal-confirm-delete");
        if (modal) modal.classList.remove("active");
    };
    window.closePmConfirmDeleteModal = closePmConfirmDeleteModal;

    const executeDeleteCurrentBank = () => {
        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;

        const deletedBank = profile.selectedBank;
        profile.banks = profile.banks.filter(b => b !== deletedBank);
        if (profile.bankQrs && profile.bankQrs[deletedBank]) {
            delete profile.bankQrs[deletedBank];
        }
        closePmConfirmDeleteModal();
        closePmDetailModal();

        if (profile.banks.length > 0) {
            profile.selectedBank = profile.banks[0];
            setPmProfile(profile);
            renderPmView();
            showToast("¡Banco eliminado con éxito!");
        } else {
            localStorage.removeItem(PM_STORAGE_KEY);
            renderPmView();
            showToast("Perfil de Pago Móvil reiniciado.");
        }
    };
    window.executeDeleteCurrentBank = executeDeleteCurrentBank;

    const copyPmData = () => {
        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;

        const bankCode = profile.selectedBank;
        const bankName = BANK_SHORT[bankCode] || BANK_NAMES[bankCode] || "Banco";
        const phone = `${profile.phonePrefix}${profile.phoneNum}`;
        const docNum = profile.docNum;
        const docType = profile.docType || "V";
        const holder = profile.holder || "";

        const format = localStorage.getItem("pmCopyFormat") || "simple";
        let textToCopy = "";

        if (format === "complete") {
            // Formato Completo: Titular, Cédula, Banco y Teléfono
            const holderLine = holder ? `${holder}\n` : "";
            textToCopy = `${holderLine}${docType}-${docNum}\n${bankName} (${bankCode})\n${phone}`;
        } else if (format === "compact") {
            // Formato Compacto: todo en una sola línea
            const holderPart = holder ? ` • ${holder}` : "";
            textToCopy = `${bankName} (${bankCode}) • ${phone} • ${docType}-${docNum}${holderPart}`;
        } else {
            // Formato Simple (el actual de Cambi)
            textToCopy = `${bankName} (${bankCode})\n${phone}\n${docNum}`;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textToCopy).then(() => {
                showToast("¡Datos copiados al portapapeles!");
            }).catch(() => {
                fallbackCopy(textToCopy);
            });
        } else {
            fallbackCopy(textToCopy);
        }
    };
    window.copyPmData = copyPmData;

    const fallbackCopy = (text) => {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.top = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand("copy");
            showToast("¡Datos copiados al portapapeles!");
        } catch (err) {
            prompt("Copia tus datos aquí:", text);
        }
        document.body.removeChild(textArea);
    };

    const showPmQR = () => {
        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;

        const bankCode = profile.selectedBank;
        const bankName = BANK_NAMES[bankCode] || "Banco";
        const phone = `${profile.phonePrefix}-${profile.phoneNum}`;
        const doc = `${profile.docType}-${profile.docNum}`;
        const holder = profile.holder || "Pago Móvil";

        const modal = document.getElementById("pm-modal-qr");
        const activeView = document.getElementById("pm-qr-view-active");
        const emptyView = document.getElementById("pm-qr-view-empty");

        const qrTitle = document.getElementById("pm-qr-bank-title");
        const qrSubtitle = document.getElementById("pm-qr-holder-subtitle");
        const qrInfoText = document.getElementById("pm-qr-info-text");
        const qrContainer = document.getElementById("pm-qrcode-container");
        const emptyTitle = document.getElementById("pm-qr-empty-title");

        if (qrTitle) qrTitle.innerText = `${bankName} (${bankCode})`;
        if (qrSubtitle) qrSubtitle.innerText = holder;

        // Solo usar el QR oficial si el usuario realmente importó una captura de Mi QR para este banco específico
        const officialPayload = (profile.bankQrs && profile.bankQrs[bankCode]) ? profile.bankQrs[bankCode] : null;

        if (officialPayload) {
            if (activeView) activeView.style.display = "block";
            if (emptyView) emptyView.style.display = "none";

            if (qrInfoText) {
                qrInfoText.innerHTML = `<strong>${bankName}</strong> &bull; <strong>${phone}</strong> &bull; <strong>${doc}</strong>`;
            }

            if (qrContainer) {
                qrContainer.innerHTML = "";
                if (typeof QRCode !== "undefined") {
                    try {
                        new QRCode(qrContainer, {
                            text: officialPayload,
                            width: 200,
                            height: 200,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.Q
                        });
                    } catch (e) {
                        qrContainer.innerHTML = "";
                        new QRCode(qrContainer, {
                            text: officialPayload,
                            width: 200,
                            height: 200,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    }
                }
            }
        } else {
            if (activeView) activeView.style.display = "none";
            if (emptyView) emptyView.style.display = "block";
            if (emptyTitle) emptyTitle.innerText = `Sincroniza el QR de ${bankName}`;
            if (qrContainer) qrContainer.innerHTML = "";
        }

        if (modal) modal.classList.add("active");
    };
    window.showPmQR = showPmQR;

    const closePmQR = () => {
        const modal = document.getElementById("pm-modal-qr");
        if (modal) modal.classList.remove("active");
    };
    window.closePmQR = closePmQR;

    const handleImportBankQR = (event) => {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;
        const currentBank = profile.selectedBank;

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");
                let width = img.width;
                let height = img.height;
                const maxDim = 1200;
                if (width > maxDim || height > maxDim) {
                    if (width > height) {
                        height = Math.round((height * maxDim) / width);
                        width = maxDim;
                    } else {
                        width = Math.round((width * maxDim) / height);
                        height = maxDim;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                const imageData = ctx.getImageData(0, 0, width, height);

                let decodedData = null;

                if (typeof jsQR !== "undefined") {
                    const code = jsQR(imageData.data, width, height, { inversionAttempts: "dontInvert" });
                    if (code && code.data) {
                        decodedData = code.data;
                    } else {
                        const codeInv = jsQR(imageData.data, width, height, { inversionAttempts: "onlyInvert" });
                        if (codeInv && codeInv.data) decodedData = codeInv.data;
                    }
                }

                if (decodedData) {
                    profile.bankQrs = profile.bankQrs || {};
                    profile.bankQrs[currentBank] = decodedData;
                    setPmProfile(profile);
                    showToast("¡QR oficial del banco guardado exitosamente!");
                    showPmQR();
                } else {
                    showToast("No se pudo detectar el código QR en la imagen");
                }
                event.target.value = "";
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    };
    window.handleImportBankQR = handleImportBankQR;

    // Inicializar carrusel touch swipe
    const carouselEl = document.getElementById("pm-carousel");
    if (carouselEl) {
        let touchStartX = 0;
        let touchEndX = 0;
        carouselEl.addEventListener("touchstart", (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        carouselEl.addEventListener("touchend", (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 45 && currentPmSlide < 2) {
                goToPmSlide(currentPmSlide + 1);
            } else if (touchEndX - touchStartX > 45 && currentPmSlide > 0) {
                goToPmSlide(currentPmSlide - 1);
            }
        }, { passive: true });
    }

    // FAB estático: se mantiene fijo en su posición sin ocultarse en scroll
    const initPmFabScroll = () => {};
    window.initPmFabScroll = initPmFabScroll;

    /**
     * Navegación por gestos: Deslizar hacia abajo (swipe down) para cerrar modales/bottom sheets.
     * Proporciona respuesta táctil fluida 1:1, soporte de inercia/velocidad y snap back.
     */
    function initModalSwipeToDismiss() {
        document.querySelectorAll('.pm-modal-overlay').forEach(overlay => {
            const sheet = overlay.querySelector('.pm-modal-sheet');
            if (!sheet) return;

            let startY = 0;
            let startX = 0;
            let currentY = 0;
            let startTime = 0;
            let isDragging = false;
            let isHandleOrHeaderTouch = false;
            let canSwipe = false;
            let initialScrollTop = 0;

            const handle = sheet.querySelector('.pm-modal-handle');
            const header = sheet.querySelector('.pm-modal-header');

            const onTouchStart = (e) => {
                if (window.innerWidth >= 900) return;
                if (!overlay.classList.contains('active')) return;
                const point = e.touches ? e.touches[0] : e;
                if (e.touches && e.touches.length !== 1) return;

                startY = point.clientY;
                startX = point.clientX;
                currentY = startY;
                startTime = Date.now();
                initialScrollTop = sheet.scrollTop;

                const target = e.target;
                const sheetRect = sheet.getBoundingClientRect();
                isHandleOrHeaderTouch = (handle && handle.contains(target)) ||
                                        (header && header.contains(target)) ||
                                        (startY - sheetRect.top < 65);

                canSwipe = isHandleOrHeaderTouch || initialScrollTop <= 0;
                isDragging = false;

                if (!e.touches) {
                    window.addEventListener('mousemove', onTouchMove);
                    window.addEventListener('mouseup', onTouchEnd);
                }
            };

            const onTouchMove = (e) => {
                if (!canSwipe || !overlay.classList.contains('active')) return;
                const point = e.touches ? e.touches[0] : e;

                const deltaY = point.clientY - startY;
                const deltaX = point.clientX - startX;

                if (!isDragging) {
                    const isVertical = Math.abs(deltaY) > Math.abs(deltaX);
                    const isDownward = deltaY > 6;

                    // Si se toca en el cuerpo del modal y hay scroll interno, se permite scroll nativo
                    if (!isHandleOrHeaderTouch && sheet.scrollTop > 0) {
                        canSwipe = false;
                        return;
                    }

                    if (isVertical && isDownward) {
                        isDragging = true;
                        if (document.activeElement && typeof document.activeElement.blur === 'function') {
                            document.activeElement.blur();
                        }
                        sheet.style.transition = 'none';
                        overlay.style.transition = 'none';
                    }
                }

                if (isDragging) {
                    if (e.cancelable) e.preventDefault();
                    currentY = point.clientY;

                    // Movimiento 1:1 hacia abajo, amortiguación si empuja hacia arriba
                    const translateY = deltaY > 0 ? deltaY : deltaY * 0.2;
                    sheet.style.transform = `translateY(${translateY}px)`;

                    // Atenuación suave del fondo
                    const sheetH = sheet.offsetHeight || 350;
                    const progress = Math.min(1, Math.max(0, deltaY / sheetH));
                    const alpha = Math.max(0.08, 0.55 * (1 - progress * 0.75));
                    overlay.style.backgroundColor = `rgba(0, 0, 0, ${alpha})`;
                }
            };

            const onTouchEnd = () => {
                window.removeEventListener('mousemove', onTouchMove);
                window.removeEventListener('mouseup', onTouchEnd);

                if (!isDragging) {
                    canSwipe = false;
                    return;
                }

                isDragging = false;
                canSwipe = false;

                const deltaY = currentY - startY;
                const elapsed = Math.max(1, Date.now() - startTime);
                const velocity = deltaY / elapsed;
                const sheetH = sheet.offsetHeight || 350;

                // Umbral de descarte: >110px de recorrido, >25% de la tarjeta o flick veloz hacia abajo
                const shouldDismiss = deltaY > 110 || (deltaY > sheetH * 0.25) || (deltaY > 40 && velocity > 0.35);

                if (shouldDismiss) {
                    sheet.style.transition = 'transform 0.22s cubic-bezier(0.25, 1, 0.5, 1)';
                    overlay.style.transition = 'opacity 0.22s cubic-bezier(0.25, 1, 0.5, 1), background-color 0.22s ease';
                    sheet.style.transform = 'translateY(100%)';
                    overlay.style.opacity = '0';

                    setTimeout(() => {
                        sheet.style.transform = '';
                        sheet.style.transition = '';
                        overlay.style.opacity = '';
                        overlay.style.transition = '';
                        overlay.style.backgroundColor = '';

                        const closeBtn = overlay.querySelector('.pm-modal-close, .pm-modal-close-btn');
                        if (closeBtn) {
                            closeBtn.click();
                        } else {
                            overlay.classList.remove('active');
                        }
                    }, 220);
                } else {
                    // Rebote suave a la posición abierta
                    sheet.style.transition = 'transform 0.26s cubic-bezier(0.2, 0.9, 0.3, 1)';
                    overlay.style.transition = 'background-color 0.26s ease';
                    sheet.style.transform = '';
                    overlay.style.backgroundColor = '';

                    setTimeout(() => {
                        sheet.style.transition = '';
                        overlay.style.transition = '';
                    }, 260);
                }
            };

            sheet.addEventListener('touchstart', onTouchStart, { passive: true });
            sheet.addEventListener('touchmove', onTouchMove, { passive: false });
            sheet.addEventListener('touchend', endDrag = onTouchEnd, { passive: true });
            sheet.addEventListener('touchcancel', onTouchEnd, { passive: true });

            // Soporte mouse drag en el handle para desktop
            if (handle) {
                handle.addEventListener('mousedown', onTouchStart);
            }
        });
    }
    window.initModalSwipeToDismiss = initModalSwipeToDismiss;

    /**
     * Navegación por gestos: Carrusel horizontal 1:1 estilo WhatsApp/Telegram.
     * La pista completa acompaña al dedo en tiempo real y realiza snap elástico al soltar.
     */
    function initTabSwipeNavigation() {
        const mainContent = document.getElementById('main-content');
        const tabsTrack = document.getElementById('tabs-track');
        if (!mainContent || !tabsTrack) return;

        const tabsOrder = ['rates', 'pagomovil', 'calculator'];
        let currentTabIndex = 0;
        let startX = 0;
        let startY = 0;
        let startTime = 0;
        let isDragging = false;
        let isHorizontalSwipe = false;
        let trackWidth = 0;
        let currentPercent = 0;

        mainContent.addEventListener('touchstart', (e) => {
            if (window.innerWidth >= 900) return;
            if (document.querySelector('.pm-modal-overlay.active')) return;
            if (document.getElementById('history-tab-content')?.classList.contains('active')) return;
            if (e.touches.length !== 1) return;

            const touch = e.touches[0];
            const initialX = touch.clientX;
            const screenW = window.innerWidth;

            // Evitar bordes externos (gestos del sistema operativo para navegación atrás/adelante)
            if (initialX < 18 || initialX > screenW - 18) return;

            // Evitar conflicto con carrusel de bienvenida en pago móvil
            const carousel = document.getElementById('pm-carousel');
            if (carousel && carousel.contains(e.target)) return;

            // Evitar conflicto con menú contextual
            if (document.querySelector('.calc-context-menu.active')) return;

            const activeNav = document.querySelector('.bottom-navigation .nav-item.active');
            const activeTabId = activeNav ? activeNav.dataset.tab : 'rates';
            currentTabIndex = tabsOrder.indexOf(activeTabId);
            if (currentTabIndex === -1) currentTabIndex = 0;

            startX = initialX;
            startY = touch.clientY;
            startTime = Date.now();
            isDragging = false;
            isHorizontalSwipe = false;

            trackWidth = mainContent.clientWidth || window.innerWidth;
            currentPercent = -(currentTabIndex * (100 / 3));
        }, { passive: true });

        mainContent.addEventListener('touchmove', (e) => {
            if (startX === 0 && startY === 0) return;
            if (document.querySelector('.pm-modal-overlay.active')) return;
            if (e.touches.length !== 1) return;

            const touch = e.touches[0];
            const deltaX = touch.clientX - startX;
            const deltaY = touch.clientY - startY;

            if (!isDragging && !isHorizontalSwipe) {
                // Si el gesto es vertical, se deja correr el scroll nativo de la pestaña
                if (Math.abs(deltaY) > Math.abs(deltaX)) {
                    startX = 0;
                    startY = 0;
                    return;
                }

                // Si el gesto es predominantemente horizontal (>8px)
                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 8) {
                    isHorizontalSwipe = true;
                    isDragging = true;
                    tabsTrack.style.transition = 'none';
                    if (document.activeElement && typeof document.activeElement.blur === 'function') {
                        document.activeElement.blur();
                    }
                    if (currentTabIndex === 1) {
                        const fab = document.getElementById('pm-fab-container');
                        if (fab) fab.classList.remove('active');
                    }
                }
            }

            if (isDragging) {
                if (e.cancelable) e.preventDefault();

                // Movimiento 1:1 en tiempo real acompañando al dedo expresado en %
                const basePercent = -(currentTabIndex * (100 / 3));
                let deltaPercent = (deltaX / trackWidth) * (100 / 3);

                // Amortiguación suave en los bordes extremos (rebote elástico)
                if (currentTabIndex === 0 && deltaX > 0) {
                    deltaPercent = (deltaX * 0.25 / trackWidth) * (100 / 3);
                } else if (currentTabIndex === (tabsOrder.length - 1) && deltaX < 0) {
                    deltaPercent = (deltaX * 0.25 / trackWidth) * (100 / 3);
                }

                currentPercent = basePercent + deltaPercent;
                tabsTrack.style.transform = `translate3d(${currentPercent}%, 0, 0)`;
            }
        }, { passive: false });

        const endSwipe = (e) => {
            if (!isDragging) {
                startX = 0;
                startY = 0;
                return;
            }

            isDragging = false;
            isHorizontalSwipe = false;

            const touch = e.changedTouches ? e.changedTouches[0] : null;
            const deltaX = touch ? (touch.clientX - startX) : 0;
            const elapsed = Math.max(1, Date.now() - startTime);
            const velocity = Math.abs(deltaX) / elapsed;

            startX = 0;
            startY = 0;

            let targetIdx = currentTabIndex;
            const distanceThreshold = trackWidth * 0.20;
            const isFastFlick = velocity > 0.25 && Math.abs(deltaX) > 25;

            if (deltaX < 0) {
                // Arrastró a la izquierda -> pestaña siguiente a la derecha
                if (Math.abs(deltaX) > distanceThreshold || isFastFlick) {
                    if (currentTabIndex < tabsOrder.length - 1) {
                        targetIdx = currentTabIndex + 1;
                    }
                }
            } else if (deltaX > 0) {
                // Arrastró a la derecha -> pestaña previa a la izquierda
                if (deltaX > distanceThreshold || isFastFlick) {
                    if (currentTabIndex > 0) {
                        targetIdx = currentTabIndex - 1;
                    }
                }
            }

            // Duración dinámica: más ágil en flick rápido (260ms), suave y elegante en arrastre lento (320ms)
            const duration = isFastFlick ? 0.26 : 0.32;
            const targetPercent = -(targetIdx * (100 / 3));

            tabsTrack.style.transition = `transform ${duration}s cubic-bezier(0.22, 1, 0.36, 1)`;
            void tabsTrack.offsetWidth; // Forzar commit de transición para garantizar interpolación GPU inmediata
            tabsTrack.style.transform = `translate3d(${targetPercent}%, 0, 0)`;

            // Sincronizar estado y barra de navegación de inmediato sin reiniciar ni romper la transición
            const targetTabId = tabsOrder[targetIdx];
            navItems.forEach(nav => {
                nav.classList.toggle('active', nav.dataset.tab === targetTabId);
            });
            tabContents.forEach(content => {
                content.classList.toggle('active', content.id === `${targetTabId}-tab-content`);
            });

            if (targetTabId === 'calculator' && inputTop) convert(inputTop);
            if (targetTabId === 'pagomovil') {
                renderPmView();
            } else {
                const fab = document.getElementById('pm-fab-container');
                if (fab) fab.classList.remove('active');
            }
        };

        mainContent.addEventListener('touchend', endSwipe, { passive: true });
        mainContent.addEventListener('touchcancel', endSwipe, { passive: true });
    }
    window.initTabSwipeNavigation = initTabSwipeNavigation;



    initCustomSelects();
    syncAllCustomSelects();
    renderPmView();
    initPmFabScroll();
    initModalSwipeToDismiss();
    initTabSwipeNavigation();

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            switchTab(item.dataset.tab);
        });
    });

    // Navegación en Desktop (Header)
    document.querySelectorAll('.desktop-nav-item').forEach(item => {
        item.addEventListener('click', () => {
            switchTab(item.dataset.tab);
        });
    });

    // Control de redimensionamiento responsivo
    window.addEventListener('resize', () => {
        const tabsTrack = document.getElementById('tabs-track');
        if (!tabsTrack) return;
        if (window.innerWidth >= 900) {
            tabsTrack.style.transform = '';
            tabsTrack.style.transition = '';
            if (!document.body.classList.contains('desktop-view-pagomovil') &&
                !document.body.classList.contains('desktop-view-history')) {
                document.body.classList.add('desktop-view-dashboard');
            }
        } else {
            const activeNav = document.querySelector('.bottom-navigation .nav-item.active');
            const activeTabId = activeNav ? activeNav.dataset.tab : 'rates';
            const idx = TABS_ORDER.indexOf(activeTabId);
            if (idx !== -1) {
                tabsTrack.style.transform = `translate3d(-${idx * (100 / 3)}%, 0, 0)`;
            }
        }
    });

    // Soporte tecla Escape para cerrar modales y ajustes en desktop
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSettingsDrawer();
            if (typeof closeAllSettingsCustomSelects === 'function') closeAllSettingsCustomSelects();
            if (typeof closeCalcRateModal === 'function') closeCalcRateModal();
            if (typeof closePmModal === 'function') closePmModal();
            if (typeof closePmDetailModal === 'function') closePmDetailModal();
            if (typeof closePmQR === 'function') closePmQR();
            if (typeof closePmConfirmDeleteModal === 'function') closePmConfirmDeleteModal();
        }
    });

    if (window.innerWidth >= 900) {
        document.body.classList.add('desktop-view-dashboard');
    }

    let startTabPref = localStorage.getItem("startTab") || "rates";
    if (startTabPref === "dashboard") startTabPref = "rates";
    if (startTabPref === "pagomovil") {
        switchTab("pagomovil", false);
    } else if (startTabPref === "calculator") {
        switchTab("calculator", false);
    } else {
        switchTab("rates", false);
    }
    // Mostrar de inmediato los datos locales si existen para arranque instantáneo (0ms)
    loadInitialCachedRates();
    // Consultar tasas frescas del servidor y plataformas en segundo plano
    fetchRate();
    updateInputColors();
</script>
