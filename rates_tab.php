<div id="rates-tab-content" class="tab-content active">
    <div class="container">
        <!-- Tasas Oficiales -->
        <div class="rates-section-header">
            <span class="rates-section-label">Tasas Oficiales</span>
        </div>

        <div class="card">
            <span class="label">Precio del Dólar</span>
            <div id="rate" class="rate-display">--.--</div>
            <div id="date" class="footer-info">Consultando BCV...</div>
        </div>

        <div class="card">
            <span class="label">Precio del Euro</span>
            <div id="rate-eur" class="rate-display">--.--</div>
            <div id="date-eur" class="footer-info">Esperando datos...</div>
        </div>

        <!-- Mercado P2P -->
        <div class="rates-section-header" style="margin-top: 20px;">
            <span class="rates-section-label">Mercado P2P</span>
        </div>

        <div class="card">
            <span class="label">Binance USDT</span>
            <div id="rate-binance-usdt" class="rate-display">--.--</div>
            <div id="date-binance-usdt" class="footer-info">Pago Móvil</div>
        </div>

        <div class="card">
            <span class="label">Binance USDC</span>
            <div id="rate-binance-usdc" class="rate-display">--.--</div>
            <div id="date-binance-usdc" class="footer-info">Pago Móvil</div>
        </div>

        <div class="card">
            <span class="label">OKX USDT</span>
            <div id="rate-okx-usdt" class="rate-display">--.--</div>
            <div id="date-okx-usdt" class="footer-info">Pago Móvil</div>
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
