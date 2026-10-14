<header class="app-header">
    <div class="branding-container">
        <img src="public/logo.webp" alt="Cambi Logo" class="logo">
        <h1 class="branding">CAMBI</h1>
    </div>

    <!-- Navegación exclusiva para Desktop -->
    <nav class="desktop-navigation" id="desktop-nav" data-active="dashboard" aria-label="Navegación principal">
        <div class="desktop-nav-glider" id="desktop-nav-glider"></div>
        <button type="button" class="desktop-nav-item active" data-tab="dashboard">
            <span class="material-symbols-rounded">currency_exchange</span>
            <span>Tasas & Conversor</span>
        </button>
        <button type="button" class="desktop-nav-item" data-tab="pagomovil">
            <span class="material-symbols-rounded">qr_code_2</span>
            <span>Pago Móvil</span>
        </button>
    </nav>

    <div class="header-actions">
        <div class="network-status-btn" id="network-status-btn" aria-hidden="true">
            <span class="material-symbols-rounded status-icon" id="network-status-icon">cloud</span>
        </div>
        <span class="material-symbols-rounded settings-btn" id="open-settings" title="Ajustes" aria-label="Ajustes">settings</span>
    </div>
</header>

<!-- Pantalla de Ajustes (Overlay / Drawer) -->
<div id="settings-scrim" class="settings-scrim"></div>
<div id="settings-screen" class="settings-overlay" role="dialog" aria-modal="true" aria-label="Ajustes">
    <header>
        <span class="material-symbols-rounded settings-back-btn" id="close-settings" role="button" aria-label="Volver">arrow_back</span>
        <h1 class="branding" style="font-size: 1.3rem;">Ajustes</h1>
    </header>
    
    <div class="settings-scroll-area">
        <div class="settings-container">
            
            <!-- Grupo 1: Apariencia -->
            <div class="settings-group">
                <div class="settings-group-title">Apariencia</div>
                <div class="settings-card">
                    <div class="settings-row">
                        <span class="settings-title">Modo Oscuro</span>
                        <label class="md-switch">
                            <input type="checkbox" id="dark-mode-toggle">
                            <span class="md-slider"></span>
                        </label>
                    </div>
                    <div class="settings-divider"></div>
                    <div class="settings-row">
                        <span class="settings-title">Color de Acento</span>
                        <div class="accent-palette-picker" id="accent-palette-picker">
                            <button type="button" class="accent-color-btn active" data-color="mint" title="Magic Mint" style="--btn-color: #A3F1CB;"></button>
                            <button type="button" class="accent-color-btn" data-color="blue" title="Baby Blue" style="--btn-color: #B1D3FE;"></button>
                            <button type="button" class="accent-color-btn" data-color="mauve" title="Mauve" style="--btn-color: #DFB8FF;"></button>
                            <button type="button" class="accent-color-btn" data-color="cotton" title="Cotton Candy" style="--btn-color: #FFB7D3;"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grupo 2: Conversor & Calculadora -->
            <div class="settings-group">
                <div class="settings-group-title">Conversor & Calculadora</div>
                <div class="settings-card">
                    <div class="settings-row">
                        <div class="settings-row-text">
                            <span class="settings-title">Tasa por Defecto</span>
                            <span class="settings-subtitle">Tasa inicial al abrir la app</span>
                        </div>
                        <select id="settings-default-rate" class="settings-select">
                            <option value="usd">Dólar BCV</option>
                            <option value="eur">Euro BCV</option>
                            <option value="binance_usdt">Binance USDT</option>
                            <option value="binance_usdc">Binance USDC</option>
                            <option value="okx_usdt">OKX USDT</option>
                        </select>
                    </div>
                    <div class="settings-divider"></div>
                    <div class="settings-row">
                        <div class="settings-row-text">
                            <span class="settings-title">Vibración Háptica</span>
                            <span class="settings-subtitle">Respuesta táctil al pulsar teclas</span>
                        </div>
                        <label class="md-switch">
                            <input type="checkbox" id="haptic-toggle" checked>
                            <span class="md-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Grupo 3: Pago Móvil -->
            <div class="settings-group">
                <div class="settings-group-title">Pago Móvil</div>
                <div class="settings-card">
                    <div class="settings-row">
                        <div class="settings-row-text">
                            <span class="settings-title">Pantalla de Inicio</span>
                            <span class="settings-subtitle">Pestaña activa al iniciar Cambi</span>
                        </div>
                        <select id="settings-start-tab" class="settings-select">
                            <option value="rates">Tasas de Cambio</option>
                            <option value="calculator">Calculadora</option>
                            <option value="pagomovil">Pago Móvil</option>
                        </select>
                    </div>
                    <div class="settings-divider"></div>
                    <div class="settings-row flex-column" style="gap: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <div class="settings-row-text">
                                <span class="settings-title">Formato de Copiado</span>
                                <span class="settings-subtitle">Estructura del texto al copiar</span>
                            </div>
                            <select id="settings-copy-format" class="settings-select">
                                <option value="simple">Simple</option>
                                <option value="complete">Completo</option>
                                <option value="compact">Compacto</option>
                            </select>
                        </div>
                        <div class="settings-copy-preview-box">
                            <div class="settings-copy-preview-header">
                                <span class="material-symbols-rounded">content_copy</span>
                                <span>Vista previa</span>
                            </div>
                            <div class="settings-copy-preview" id="settings-copy-preview"></div>
                        </div>
                    </div>
                    <div class="settings-divider"></div>
                    <div class="settings-row flex-column" style="gap: 10px;">
                        <div class="settings-row-text" style="width: 100%;">
                            <span class="settings-title">Copia de Seguridad</span>
                            <span class="settings-subtitle">Respalda o restaura tus bancos guardados</span>
                        </div>
                        <div style="display: flex; gap: 10px; width: 100%;">
                            <button type="button" class="settings-btn-action" id="btn-export-backup" style="flex: 1;">
                                <span class="material-symbols-rounded">download</span> Exportar
                            </button>
                            <button type="button" class="settings-btn-action" id="btn-import-backup" style="flex: 1;">
                                <span class="material-symbols-rounded">upload</span> Importar
                            </button>
                            <input type="file" id="backup-file-input" accept=".json,application/json" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grupo 4: Sistema & Estado -->
            <div class="settings-group">
                <div class="settings-group-title">Sistema</div>
                <div class="settings-card">
                    <div class="settings-row">
                        <div class="settings-row-text">
                            <span class="settings-title">Versión de Cambi</span>
                            <span class="settings-subtitle" id="settings-version-label">v109</span>
                        </div>
                        <button type="button" class="settings-btn-action" id="btn-check-update" style="padding: 8px 14px;">
                            <span class="material-symbols-rounded">refresh</span> Actualizar
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
