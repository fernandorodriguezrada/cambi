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
        if (tabId === 'pagomovil') {
            document.querySelectorAll(".pm-modal-overlay").forEach(overlay => {
                overlay.addEventListener("click", (e) => {
                    if (e.target === overlay) {
                        overlay.classList.remove("active");
                    }
                });
            });
            renderPmView();
            const fab = document.getElementById("pm-btn-add-bank-fab");
            if (fab) fab.classList.remove("fab-hidden");
        } else {
            const fab = document.getElementById("pm-btn-add-bank-fab");
            if (fab) fab.style.display = "none";
        }
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

    const BANK_OFFICIAL_LOGOS = {
        "0102": "public/banks/0102.svg",
        "0104": "public/banks/0104.png",
        "0105": "public/banks/0105.svg",
        "0108": "public/banks/0108.svg",
        "0114": "public/banks/0114.png",
        "0115": "public/banks/0115.png",
        "0128": "public/banks/0128.png",
        "0134": "public/banks/0134.svg",
        "0137": "public/banks/0137.png",
        "0138": "public/banks/0138.png",
        "0146": "public/banks/0146.png",
        "0151": "public/banks/0151.png",
        "0156": "public/banks/0156.png",
        "0157": "public/banks/0157.png",
        "0163": "public/banks/0163.png",
        "0166": "public/banks/0166.png",
        "0168": "public/banks/0168.png",
        "0169": "public/banks/0169.png",
        "0171": "public/banks/0171.png",
        "0172": "public/banks/0172.png",
        "0174": "public/banks/0174.png",
        "0175": "public/banks/0175.png",
        "0177": "public/banks/0177.png",
        "0191": "public/banks/0191.png"
    };

    const getBankLogoHtml = (bankCode) => {
        const logoUrl = BANK_OFFICIAL_LOGOS[bankCode];
        const name = BANK_NAMES[bankCode] || "Banco";
        if (logoUrl) {
            return `<img src="${logoUrl}" alt="${name}" loading="lazy" />`;
        }
        return `<span class="material-symbols-rounded">account_balance</span>`;
    };
    window.getBankLogoHtml = getBankLogoHtml;

    const renderPmView = () => {
        const profile = getPmProfile();
        const onboardingView = document.getElementById("pm-onboarding-view");
        const activeView = document.getElementById("pm-active-view");
        if (!onboardingView || !activeView) return;

        if (!profile || !profile.banks || profile.banks.length === 0) {
            onboardingView.style.display = "flex";
            activeView.style.display = "none";
            const fab = document.getElementById("pm-btn-add-bank-fab");
            if (fab) fab.style.display = "none";
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

        const fab = document.getElementById("pm-btn-add-bank-fab");
        if (fab) {
            const isPmTab = document.getElementById("pagomovil-tab-content")?.classList.contains("active");
            fab.style.display = isPmTab ? "flex" : "none";
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

        profile.banks = profile.banks.filter(b => b !== profile.selectedBank);
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

    // Algoritmo CRC-16/CCITT-FALSE requerido por la especificación EMVCo (Tag 63)
    const crc16Ccitt = (str) => {
        let crc = 0xFFFF;
        for (let i = 0; i < str.length; i++) {
            crc ^= str.charCodeAt(i) << 8;
            for (let j = 0; j < 8; j++) {
                crc = (crc & 0x8000) ? ((crc << 1) ^ 0x1021) & 0xFFFF : (crc << 1) & 0xFFFF;
            }
        }
        return crc.toString(16).toUpperCase().padStart(4, "0");
    };

    // Generador de Payload EMVCo MPM para Pago Móvil Interbancario (Suiche 7B / BDVApp / Conexus)
    const buildSuiche7bQr = (bankCode, docType, docNum, phonePrefix, phoneNum, holder) => {
        const docClean = `${docType}${docNum}`.replace(/[^A-Z0-9]/gi, "").toUpperCase();
        const phoneClean = `${phonePrefix}${phoneNum}`.replace(/[^0-9]/g, "");
        const bankClean = String(bankCode).padStart(4, "0");

        let nameClean = (holder || "PAGO MOVIL")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-zA-Z0-9 ]/g, "")
            .trim()
            .toUpperCase()
            .slice(0, 25);
        if (!nameClean) nameClean = "PAGO MOVIL";

        const pad2 = (n) => String(n).padStart(2, "0");

        // Subtags de Tag 26 (Merchant Account Information bajo el esquema Suiche 7B)
        const s00 = "0015ve.com.suiche7b";
        const s01 = `01${pad2(bankClean.length)}${bankClean}`;
        const s02 = `02${pad2(phoneClean.length)}${phoneClean}`;
        const s03 = `03${pad2(docClean.length)}${docClean}`;

        const tag26Val = s00 + s01 + s02 + s03;
        const tag26 = `26${pad2(tag26Val.length)}${tag26Val}`;

        const tag00 = "000201";     // Versión EMVCo 01
        const tag01 = "010211";     // QR Estático (el pagador indica el monto)
        const tag52 = "52040000";   // Merchant Category Code general
        const tag53 = "5303928";    // Moneda: 928 (VES - Bolívares)
        const tag58 = "5802VE";     // País: VE (Venezuela)
        const tag59 = `59${pad2(nameClean.length)}${nameClean}`; // Titular
        const tag60 = "6007CARACAS"; // Ciudad

        const payloadNoCrc = tag00 + tag01 + tag26 + tag52 + tag53 + tag58 + tag59 + tag60 + "6304";
        const crc = crc16Ccitt(payloadNoCrc);
        return payloadNoCrc + crc;
    };

    const showPmQR = (forcePlainText = false) => {
        const profile = getPmProfile();
        if (!profile || !profile.selectedBank) return;

        const bankCode = profile.selectedBank;
        const bankName = BANK_NAMES[bankCode] || "Banco";
        const phone = `${profile.phonePrefix}-${profile.phoneNum}`;
        const doc = `${profile.docType}-${profile.docNum}`;
        const holder = profile.holder;

        const modal = document.getElementById("pm-modal-qr");
        const activeView = document.getElementById("pm-qr-view-active");
        const emptyView = document.getElementById("pm-qr-view-empty");

        const qrTitle = document.getElementById("pm-qr-bank-title");
        const qrSubtitle = document.getElementById("pm-qr-holder-subtitle");
        const qrInfoText = document.getElementById("pm-qr-info-text");
        const qrContainer = document.getElementById("pm-qrcode-container");
        const badgeEl = document.getElementById("pm-qr-badge");
        const emptyTitle = document.getElementById("pm-qr-empty-title");

        let officialPayload = null;
        if (profile.bankQrs && profile.bankQrs[bankCode]) {
            officialPayload = profile.bankQrs[bankCode];
        } else if (bankCode === "0134" && profile.phoneNum === "7040141") {
            officialPayload = "YRbMTpNdhtuGfRPPR6kYwwTiW7AMphcmue2HwIns4rUqgAcVLUU5PEL+ifxENwOVvy7Y00EKMnv9osiaIGZzsf91O5S+4tgY1z2D8L6+NEvmhoQFKjG6BKhYNGj7GAK8PNSTgnUM5kBbVvvp9AoO/p2+b2uDfTxvjzBHY7qR9dDgF8PhhPRFgYAIh394Ke1T?merchantId=0134&strong_id=1790656181";
            profile.bankQrs = profile.bankQrs || {};
            profile.bankQrs[bankCode] = officialPayload;
            setPmProfile(profile);
        }

        if (officialPayload) {
            // ESTADO 1: QR Oficial Suiche 7B
            if (activeView) activeView.style.display = "block";
            if (emptyView) emptyView.style.display = "none";

            if (qrTitle) qrTitle.innerText = `${bankName} (${bankCode})`;
            if (qrSubtitle) qrSubtitle.innerText = holder;
            if (qrInfoText) qrInfoText.innerText = `${phone} • ${doc}`;

            if (badgeEl) {
                badgeEl.className = "pm-qr-badge-official";
                badgeEl.innerHTML = '<span class="material-symbols-rounded" style="font-size: 1rem;">verified</span><span>QR Oficial Suiche 7B</span>';
            }

            if (qrContainer) {
                qrContainer.innerHTML = "";
                if (typeof QRCode !== "undefined") {
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
        } else if (forcePlainText) {
            // ESTADO 2: Texto plano (para cámaras estándar)
            if (activeView) activeView.style.display = "block";
            if (emptyView) emptyView.style.display = "none";

            if (qrTitle) qrTitle.innerText = `${bankName} (${bankCode})`;
            if (qrSubtitle) qrSubtitle.innerText = holder;
            if (qrInfoText) qrInfoText.innerText = `${phone} • ${doc}`;

            if (badgeEl) {
                badgeEl.className = "pm-qr-badge-textonly";
                badgeEl.innerHTML = '<span class="material-symbols-rounded" style="font-size: 1rem;">info</span><span>Texto plano (cámaras estándar)</span>';
            }

            const plainText = `Pago Móvil:\nBanco: ${bankName} (${bankCode})\nTeléfono: ${phone}\nCédula: ${doc}\nTitular: ${holder}`;

            if (qrContainer) {
                qrContainer.innerHTML = "";
                if (typeof QRCode !== "undefined") {
                    const safePayload = plainText;
                    new QRCode(qrContainer, {
                        text: safePayload,
                        width: 200,
                        height: 200,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            }
        } else {
            // ESTADO 3: Sin QR oficial - Invitación a sincronizar captura
            if (activeView) activeView.style.display = "none";
            if (emptyView) emptyView.style.display = "block";
            if (emptyTitle) emptyTitle.innerText = `Sincroniza el QR de ${bankName}`;
        }

        if (modal) modal.classList.add("active");
    };
    window.showPmQR = showPmQR;

    const showPlainTextQR = () => {
        showPmQR(true);
    };
    window.showPlainTextQR = showPlainTextQR;

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

    // FAB Scroll Behavior: ocultar al scrollear hacia abajo, mostrar al scrollear hacia arriba
    let lastMainScrollTop = 0;
    const initPmFabScroll = () => {
        const mainContent = document.getElementById("main-content");
        const fab = document.getElementById("pm-btn-add-bank-fab");
        if (!fab) return;

        const onScroll = (currentY) => {
            const isPmTab = document.getElementById("pagomovil-tab-content")?.classList.contains("active");
            if (!isPmTab) return;

            const diff = currentY - lastMainScrollTop;
            if (diff > 8 && currentY > 40) {
                fab.classList.add("fab-hidden");
            } else if (diff < -8 || currentY <= 30) {
                fab.classList.remove("fab-hidden");
            }
            lastMainScrollTop = Math.max(0, currentY);
        };

        if (mainContent) {
            mainContent.addEventListener("scroll", () => {
                onScroll(mainContent.scrollTop);
            }, { passive: true });
        }
        window.addEventListener("scroll", () => {
            onScroll(window.scrollY);
        }, { passive: true });
    };
    window.initPmFabScroll = initPmFabScroll;

    initCustomSelects();
    syncAllCustomSelects();
    renderPmView();
    initPmFabScroll();

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
