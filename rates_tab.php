<div id="rates-tab-content" class="tab-content active">
    <div class="container">
        <!-- Tasas Oficiales -->
        <div class="rates-section-header">
            <span class="rates-section-label">Tasas Oficiales</span>
        </div>

        <div class="card">
            <div class="rate-card-title">
                <img src="public/bcv.svg" alt="BCV" class="rate-platform-logo">
                <span class="label">Precio del Dólar</span>
            </div>
            <div id="rate" class="rate-display">--.--</div>
            <div id="date" class="footer-info">Consultando BCV...</div>
        </div>

        <div class="card">
            <div class="rate-card-title">
                <img src="public/bcv.svg" alt="BCV" class="rate-platform-logo">
                <span class="label">Precio del Euro</span>
            </div>
            <div id="rate-eur" class="rate-display">--.--</div>
            <div id="date-eur" class="footer-info">Esperando datos...</div>
        </div>

        <!-- Mercado P2P -->
        <div class="rates-section-header" style="margin-top: 20px;">
            <span class="rates-section-label">Mercado P2P</span>
        </div>

        <div class="card">
            <div class="rate-card-title">
                <img src="public/crypto/binance.svg" alt="Binance" class="rate-platform-logo">
                <span class="label">Binance USDT</span>
            </div>
            <div id="rate-binance-usdt" class="rate-display">--.--</div>
            <div id="date-binance-usdt" class="footer-info">Consultando Binance...</div>
        </div>

        <div class="card">
            <div class="rate-card-title">
                <img src="public/crypto/binance.svg" alt="Binance" class="rate-platform-logo">
                <span class="label">Binance USDC</span>
            </div>
            <div id="rate-binance-usdc" class="rate-display">--.--</div>
            <div id="date-binance-usdc" class="footer-info">Consultando Binance...</div>
        </div>

        <div class="card">
            <div class="rate-card-title">
                <img src="public/crypto/okx.svg" alt="OKX" class="rate-platform-logo">
                <span class="label">OKX USDT</span>
            </div>
            <div id="rate-okx-usdt" class="rate-display">--.--</div>
            <div id="date-okx-usdt" class="footer-info">Consultando OKX...</div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; width: 100%; margin: 20px 0 24px 0;">
            <button class="refresh-btn" style="background-color: var(--md-sys-color-surface-container); color: var(--md-sys-color-primary); flex: 1; max-width: 160px;" onclick="switchTab('history')">
                <span class="material-symbols-rounded">history</span> Historial
            </button>
            <button class="refresh-btn" style="flex: 1; max-width: 160px;" onclick="fetchRate(true)">
                <span class="material-symbols-rounded">refresh</span> Actualizar
            </button>
        </div>
    </div>
</div>
