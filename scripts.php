<script>
    let currentRate = 0;
    let isTopUsd = true; // Estado para saber el sentido de la conversión
    let isResultPendingClear = false; // Control para limpiar el input tras un resultado
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
        
        // El input tiene 16px de padding-right + 8px de margen interno del row
        const rightOffset = 24 + textAfterWidth;
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
     * Cambia el color del texto a gris cuando el valor es 0,00 o está vacío.
     */
    const updateInputColors = () => {
        [inputTop, inputBottom].forEach(el => {
            if (!el) return;
            const isMuted = el.value === '0,00' || el.value === '';
            el.classList.toggle('value-zero', isMuted);
        });
    };

    const updateLoadingProgress = (percent) => {
        const bar = document.getElementById('progress-bar');
        if (bar) bar.style.width = `${percent}%`;
    };

    const hideSplashScreen = () => {
        const splash = document.getElementById('splash-screen');
        if (splash) {
            updateLoadingProgress(100);
            setTimeout(() => {
                splash.classList.add('hidden');
            }, 500);
        }
    };

    async function fetchRate(forceRefresh = false) {
        const splash = document.getElementById('splash-screen');
        const bar = document.getElementById('progress-bar');
        
        // Si el splash está oculto (es una recarga manual), lo mostramos de nuevo fluidamente
        if (splash && splash.classList.contains('hidden')) {
            if (bar) bar.style.transition = 'none'; // Reset instantáneo de la barra
            updateLoadingProgress(0);
            if (bar) void bar.offsetWidth; // Forzar renderizado
            if (bar) bar.style.transition = ''; // Restaurar animación
            splash.classList.remove('hidden');
        }

        updateLoadingProgress(30);
        try {
            updateLoadingProgress(60);
            const queryParam = forceRefresh ? `force=1&t=${Date.now()}` : `t=${Date.now()}`;
            const response = await fetch(`api.php?${queryParam}`, { cache: 'no-store' });
            if (!response.ok) throw new Error("Error en el servidor local");

            const data = await response.json();
            
            // Log de depuración
            console.log("--- DEBUG CAMBI ---");
            console.log("Fuente utilizada:", data.source || "Ninguna");
            console.log("Respuesta completa:", data);
            if (data.debug_bcv) console.warn("Razón fallo BCV:", data.debug_bcv);
            console.log("-------------------");

            // Renderizar Historial
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

            currentRate = data.usd;
            if (rateDisplay) rateDisplay.innerText = formatRAE(currentRate);
            
            // Indicador visual de fecha y fuente
            const sourceLabel = data.source ? ` (${data.source})` : '';
            const updateLabel = `Actualizado: ${data.last_update}${sourceLabel}`;
            
            if (dateDisplay) {
                dateDisplay.style.color = "";
                dateDisplay.innerText = updateLabel;
            }

            if (data.eur) {
                if (rateEurDisplay) rateEurDisplay.innerText = formatRAE(data.eur);
                if (dateEurDisplay) {
                    dateEurDisplay.style.color = "";
                    dateEurDisplay.innerText = updateLabel;
                }
            } else {
                if (rateEurDisplay) rateEurDisplay.innerText = "No disponible";
                if (dateEurDisplay) dateEurDisplay.innerText = "Fuente sin Euro";
            }

            if (inputTop && cleanValue(inputTop.value) !== 0) convert(inputTop);
            updateLoadingProgress(90);
        } catch (error) {
            if (rateDisplay) rateDisplay.innerText = "Error de Red";
            if (rateEurDisplay) rateEurDisplay.innerText = "Error de Red";
            console.error("Error fetching rate:", error);
        } finally {
            hideSplashScreen();
        }
    }

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
                    const label = isTopUsd ? "USD" : "Bs.";
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
        inputTop.addEventListener('input', (e) => {
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
            const eqBtn = document.querySelector('.keypad-btn[data-key="="]');
            if (eqBtn) eqBtn.click();
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

    // Lógica de la Pantalla de Ajustes
    const settingsScreen = document.getElementById('settings-screen');
    const openSettings = document.getElementById('open-settings');
    const closeSettings = document.getElementById('close-settings');
    const darkModeToggle = document.getElementById('dark-mode-toggle');

    const updateThemeColor = (isDark) => {
        const color = isDark ? '#3B454E' : '#F3EDF7';
        const themeColorMeta = document.querySelector('meta[name=\"theme-color\"]');
        if (themeColorMeta) {
            themeColorMeta.setAttribute('content', color);
        }
        document.documentElement.style.backgroundColor = color;
        document.body.style.backgroundColor = color;
        document.querySelectorAll('header').forEach(h => {
            h.style.backgroundColor = color;
        });
        document.querySelectorAll('.bottom-navigation').forEach(n => {
            n.style.backgroundColor = color;
        });
        const settings = document.getElementById('settings-screen');
        if (settings) settings.style.backgroundColor = isDark ? '#262C33' : '#FEF7FF';
    };

    if (openSettings && settingsScreen) openSettings.onclick = () => settingsScreen.classList.add('active');
    if (closeSettings && settingsScreen) closeSettings.onclick = () => settingsScreen.classList.remove('active');

    // Lógica de Modo Oscuro
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', () => {
            const isDark = darkModeToggle.checked;
            if (darkModeToggle.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            }
            updateThemeColor(isDark);
        });
    }

    const isDarkModeEnabled = localStorage.getItem('darkMode') === 'enabled';
    if (isDarkModeEnabled) {
        document.body.classList.add('dark-mode');
        if (darkModeToggle) darkModeToggle.checked = true;
    }
    updateThemeColor(isDarkModeEnabled);

    // Lógica del Teclado Numérico
    document.querySelectorAll('.keypad-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!inputTop) return;
            const key = btn.dataset.key;
            if (key === 'swap') return;
            
            // Botón C (Clear / Limpiar todo)
            if (key === 'C') {
                inputTop.value = '';
                if (inputBottom) inputBottom.value = '';
                const operationResult = document.getElementById('operation-result');
                if (operationResult) operationResult.innerText = '';
                isResultPendingClear = false;
                formatInputDisplay(inputTop);
                convert(inputTop);
                updateCaretPosition();
                updateInputColors();
                return;
            }

            // Teclas de navegación
            if (key === 'left' || key === 'right') {
                const currentPos = inputTop.selectionStart ?? inputTop.value.length;
                const newPos = key === 'left' ? currentPos - 1 : currentPos + 1;
                inputTop.setSelectionRange(newPos, newPos);
                updateCaretPosition();
                return;
            }

            if (key === 'backspace') {
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
                const operationResult = document.getElementById('operation-result');
                if (operationResult && operationResult.innerText.startsWith('=')) {
                    const cleanRes = operationResult.innerText.replace(/[^0-9,]/g, '');
                    inputTop.value = cleanRes;
                    operationResult.innerText = '';
                    isResultPendingClear = true;
                }
            } else {
                if (isResultPendingClear && !['+', '-', 'x', '/'].includes(key)) {
                    inputTop.value = '';
                }
                isResultPendingClear = false;

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

    const switchTab = (tabId) => {
        navItems.forEach(nav => {
            nav.classList.toggle('active', nav.dataset.tab === tabId);
        });
        tabContents.forEach(content => {
            content.classList.toggle('active', content.id === `${tabId}-tab-content`);
        });
        if (tabId === 'calculator' && inputTop) convert(inputTop);
        if (tabId === 'pagomovil')     // Cerrar modales al tocar el fondo oscuro (backdrop)
    document.querySelectorAll(".pm-modal-overlay").forEach(overlay => {
        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) {
                overlay.classList.remove("active");
            }
        });
    });

    renderPmView();
    };

    window.switchTab = switchTab;

    if (swapBtn) {
        swapBtn.onclick = () => {
            isResultPendingClear = false;
            isTopUsd = !isTopUsd;

            swapBtn.style.transform = isTopUsd ? 'rotate(0deg)' : 'rotate(180deg)';

            if (labelTop && labelBottom) {
                const tempLabel = labelTop.innerText;
                labelTop.innerText = labelBottom.innerText;
                labelBottom.innerText = tempLabel;
            }

            if (inputTop && inputBottom) {
                const valTop = inputTop.value;
                const valBottom = inputBottom.value;
                inputTop.value = valBottom;
                inputBottom.value = valTop;

                formatInputDisplay(inputTop);
                convert(inputTop);
                updateInputColors();
            }
        };
    }

    
    /* =========================================
       LÓGICA PAGO MÓVIL (LOCAL-FIRST)
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

    let currentPmSlide = 0;
    let pmModalMode = "create";

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

    const showToast = (message) => {
        const toast = document.getElementById("pm-toast");
        const toastMsg = document.getElementById("pm-toast-msg");
        if (!toast) return;
        if (toastMsg) toastMsg.innerText = message;
        toast.classList.add("active");
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.classList.remove("active");
        }, 2600);
    };
    window.showToast = showToast;

    const getBankLogoSvg = (bankCode) => {
        switch (bankCode) {
            case "0134": // Banesco
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#007A3D"/><path d="M10 26V10h8c3.4 0 5.8 1.8 5.8 4.6 0 1.8-1 3.2-2.7 3.8 2.1.6 3.6 2.2 3.6 4.5 0 3.1-2.5 5.1-6.4 5.1H10zm4.4-9.2h3.2c1.4 0 2.2-.7 2.2-1.9s-.8-1.9-2.2-1.9h-3.2v3.8zm0 6.2h3.6c1.5 0 2.5-.8 2.5-2.1s-1-2.1-2.5-2.1h-3.6V23z" fill="#FFFFFF"/><circle cx="27" cy="11" r="2.5" fill="#78BE20"/></svg>`;
            case "0105": // Mercantil
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#002D72"/><circle cx="18" cy="18" r="9" stroke="#FFFFFF" stroke-width="2" fill="none"/><path d="M18 9c-3 3-4.5 5.5-4.5 9s1.5 6 4.5 9c3-3 4.5-5.5 4.5-9s-1.5-6-4.5-9z" stroke="#FFFFFF" stroke-width="1.8" fill="none"/><line x1="9" y1="18" x2="27" y2="18" stroke="#FFFFFF" stroke-width="1.8"/><path d="M14 18a4 4 0 0 0 8 0" stroke="#FF6A00" stroke-width="2.5" stroke-linecap="round"/></svg>`;
            case "0102": // Banco de Venezuela
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#0B284B"/><path d="M10 11h6.5c3.8 0 6 2.4 6 6.5s-2.2 6.5-6 6.5H10V11zm4 3.5v6h2.2c1.8 0 2.8-1.1 2.8-3s-1-3-2.8-3H14z" fill="#FFFFFF"/><path d="M21 11h3.2l2.8 7-2.8 7H21l2.5-7-2.5-7z" fill="#D32F2F"/><circle cx="28" cy="18" r="1.5" fill="#FBC02D"/></svg>`;
            case "0108": // BBVA Banco Provincial
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#004481"/><text x="18" y="21.5" font-family="sans-serif" font-weight="900" font-size="8.5" fill="#FFFFFF" text-anchor="middle" letter-spacing="-0.3">BBVA</text></svg>`;
            case "0172": // Bancamiga
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#008B8B"/><circle cx="18" cy="18" r="5" stroke="#FFFFFF" stroke-width="2.2" fill="none"/><path d="M11 18c0 3.9 3.1 7 7 7s7-3.1 7-7" stroke="#66D9D9" stroke-width="2.2" stroke-linecap="round" fill="none"/><circle cx="18" cy="18" r="2" fill="#FFFFFF"/></svg>`;
            case "0191": // BNC
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#006837"/><text x="18" y="22" font-family="sans-serif" font-weight="900" font-size="9.5" fill="#FFFFFF" text-anchor="middle">BNC</text></svg>`;
            case "0114": // Bancaribe
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#003882"/><path d="M10 13c5-3 12-2 16 3" stroke="#FF7900" stroke-width="3" stroke-linecap="round"/><path d="M9 22c4 4 11 4 16 0" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/></svg>`;
            case "0115": // Banco Exterior
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#0277BD"/><polygon points="18,9 21,15.5 28,18 21,20.5 18,27 15,20.5 8,18 15,15.5" fill="#FFFFFF"/></svg>`;
            case "0175": // Banco Bicentenario
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#C62828"/><polygon points="18,10 20.2,15 25.5,15.8 21.6,19.5 22.6,24.8 18,22.2 13.4,24.8 14.4,19.5 10.5,15.8 15.8,15" fill="#FFFFFF"/></svg>`;
            case "0163": // Banco del Tesoro
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#B26A00"/><path d="M11 14h14l-2 10h-10l-2-10z" stroke="#FFFFFF" stroke-width="2" fill="none"/><line x1="18" y1="14" x2="18" y2="24" stroke="#FFFFFF" stroke-width="2"/><circle cx="18" cy="11.5" r="2" fill="#FCD34D"/></svg>`;
            case "0174": // Banplus
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#1C3870"/><path d="M12 11v14h5c2.6 0 4.2-1.3 4.2-3.3 0-1.3-.7-2.3-2-2.7 1.1-.4 1.7-1.4 1.7-2.6 0-2-1.6-3.4-4.2-3.4H12zm2.6 2.6h2.2c1 0 1.6.5 1.6 1.4s-.6 1.4-1.6 1.4h-2.2v-2.8zm0 5h2.5c1.1 0 1.8.6 1.8 1.6s-.7 1.6-1.8 1.6h-2.5v-3.2z" fill="#FFFFFF"/><path d="M26 15v6m-3-3h6" stroke="#F97316" stroke-width="2.5" stroke-linecap="round"/></svg>`;
            case "0168": // Bancrecer
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="#6A1B9A"/><path d="M18 25V13m0 0c-3-2-6.5 0-6.5 3.8 0 3.8 4.5 4.8 6.5 1.8m0-5.6c3-2 6.5 0 6.5 3.8 0 3.8-4.5 4.8-6.5 1.8" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" fill="none"/></svg>`;
            default:
                return `<svg viewBox="0 0 36 36" width="30" height="30" fill="none"><rect width="36" height="36" rx="9" fill="rgba(0,0,0,0.12)"/><path d="M10 15l8-5 8 5v2H10v-2zm1 4h2v6h-2v-6zm5 0h2v6h-2v-6zm5 0h2v6h-2v-6zM9 26h18v2H9v-2z" fill="currentColor"/></svg>`;
        }
    };
    window.getBankLogoSvg = getBankLogoSvg;

    const renderPmView = () => {
        const profile = getPmProfile();
        const onboardingView = document.getElementById("pm-onboarding-view");
        const activeView = document.getElementById("pm-active-view");
        if (!onboardingView || !activeView) return;

        if (!profile || !profile.banks || profile.banks.length === 0) {
            onboardingView.style.display = "flex";
            activeView.style.display = "none";
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
                const logoSvg = getBankLogoSvg(bankCode);

                block.innerHTML = `
                    <div class="pm-block-top">
                        <div class="pm-block-badge">
                            <div class="pm-block-logo">
                                ${logoSvg}
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
    };
    window.renderPmView = renderPmView;

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
            logoContainer.innerHTML = getBankLogoSvg(bankCode);
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

    const openAddBankModal = () => {
        const modal = document.getElementById("pm-modal-add-bank");
        const select = document.getElementById("pm-new-bank-select");
        if (select) select.value = "";
        syncAllCustomSelects();
        closeAllCustomSelects();
        if (modal) modal.classList.add("active");
    };
    window.openAddBankModal = openAddBankModal;

    const closeAddBankModal = () => {
        closeAllCustomSelects();
        const modal = document.getElementById("pm-modal-add-bank");
        if (modal) modal.classList.remove("active");
    };
    window.closeAddBankModal = closeAddBankModal;

    const saveNewBank = (e) => {
        e.preventDefault();
        const select = document.getElementById("pm-new-bank-select");
        const newBank = select ? select.value : "";
        if (!newBank) return;

        const profile = getPmProfile();
        if (!profile) return;

        if (profile.banks.includes(newBank)) {
            alert("Este banco ya se encuentra en tu lista.");
            profile.selectedBank = newBank;
            setPmProfile(profile);
            closeAddBankModal();
            renderPmView();
            return;
        }

        profile.banks.push(newBank);
        profile.selectedBank = newBank;
        setPmProfile(profile);
        closeAddBankModal();
        renderPmView();
        showToast("¡Nuevo banco añadido!");
        openPmDetailModal(newBank);
    };
    window.saveNewBank = saveNewBank;

    const deleteCurrentBank = () => {
        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;

        const bankName = BANK_NAMES[profile.selectedBank] || profile.selectedBank;
        if (!confirm(`¿Eliminar ${bankName} de tus pagos móviles?`)) return;

        profile.banks = profile.banks.filter(b => b !== profile.selectedBank);
        closePmDetailModal();
        if (profile.banks.length > 0) {
            profile.selectedBank = profile.banks[0];
            setPmProfile(profile);
            renderPmView();
            showToast("Banco eliminado.");
        } else {
            localStorage.removeItem(PM_STORAGE_KEY);
            renderPmView();
            showToast("Perfil de Pago Móvil reiniciado.");
        }
    };
    window.deleteCurrentBank = deleteCurrentBank;

    const copyPmData = () => {
        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;

        const bankCode = profile.selectedBank;
        const bankName = BANK_NAMES[bankCode] || "Banco";
        const phoneFormatted = `${profile.phonePrefix}-${profile.phoneNum}`;
        const doc = `${profile.docType}-${profile.docNum}`;
        const holder = profile.holder;

        const textToCopy = `Pago Móvil:\nBanco: ${bankName} (${bankCode})\nTeléfono: ${phoneFormatted}\nCédula: ${doc}\nTitular: ${holder}`;

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
        const phone = `${profile.phonePrefix}${profile.phoneNum}`;
        const doc = `${profile.docType}${profile.docNum}`;
        const holder = profile.holder;

        const qrTitle = document.getElementById("pm-qr-bank-title");
        const qrSubtitle = document.getElementById("pm-qr-holder-subtitle");
        const qrInfoText = document.getElementById("pm-qr-info-text");
        const qrContainer = document.getElementById("pm-qrcode-container");
        const modal = document.getElementById("pm-modal-qr");

        if (qrTitle) qrTitle.innerText = `${bankName} (${bankCode})`;
        if (qrSubtitle) qrSubtitle.innerText = holder;
        if (qrInfoText) qrInfoText.innerText = `${profile.phonePrefix}-${profile.phoneNum} • ${profile.docType}-${profile.docNum}`;

        if (qrContainer) {
            qrContainer.innerHTML = "";
            const qrPayload = `PAGO MOVIL\nBanco: ${bankCode}\nCI: ${doc}\nTel: ${phone}\nTitular: ${holder}`;
            
            if (typeof QRCode !== "undefined") {
                new QRCode(qrContainer, {
                    text: qrPayload,
                    width: 200,
                    height: 200,
                    colorDark: "#1e252b",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            } else {
                qrContainer.innerHTML = '<p style="color:var(--md-sys-color-outline); font-size:0.85rem;">Generador QR no disponible offline todavía.</p>';
            }
        }

        if (modal) modal.classList.add("active");
    };
    window.showPmQR = showPmQR;

    const closePmQR = () => {
        const modal = document.getElementById("pm-modal-qr");
        if (modal) modal.classList.remove("active");
    };
    window.closePmQR = closePmQR;

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

    initCustomSelects();
    syncAllCustomSelects();
    renderPmView();

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            switchTab(item.dataset.tab);
        });
    });

    const ratesTabEl = document.getElementById('rates-tab-content');
    if (ratesTabEl) ratesTabEl.classList.add('active');
    updateLoadingProgress(10);
    fetchRate();
    updateInputColors();
</script>
