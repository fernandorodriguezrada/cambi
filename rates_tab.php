<div id="rates-tab-content" class="tab-content active">
    <div class="container">
        <!-- SECCIÓN 1: TASAS OFICIALES BCV -->
        <div class="rates-section-header">
            <div class="rates-section-title-wrap">
                <span class="material-symbols-rounded">account_balance</span>
                <span class="rates-section-title">Oficial BCV</span>
            </div>
        </div>

        <div class="card" onclick="selectRateForCalc('usd')" role="button" tabindex="0" title="Toca para calcular con Dólar BCV">
            <div class="rate-card-top">
                <span class="label">Dólar Oficial (USD)</span>
                <span class="rate-badge bcv">BCV</span>
            </div>
            <div id="rate" class="rate-display">--.--</div>
            <div id="date" class="footer-info">Consultando BCV...</div>
        </div>

        <div class="card" onclick="selectRateForCalc('eur')" role="button" tabindex="0" title="Toca para calcular con Euro BCV">
            <div class="rate-card-top">
                <span class="label">Euro Oficial (EUR)</span>
                <span class="rate-badge bcv">BCV</span>
            </div>
            <div id="rate-eur" class="rate-display">--.--</div>
            <div id="date-eur" class="footer-info">Esperando datos...</div>
        </div>

        <!-- SECCIÓN 2: MERCADO CRIPTO / P2P -->
        <div class="rates-section-header" style="margin-top: 14px;">
            <div class="rates-section-title-wrap">
                <span class="material-symbols-rounded">currency_exchange</span>
                <span class="rates-section-title">Mercado P2P (Pago Móvil)</span>
            </div>
            <span class="live-pulse-badge"><span class="pulse-dot"></span>En Vivo</span>
        </div>

        <div class="p2p-cards-list">
            <!-- Binance USDT -->
            <div class="card p2p-card" onclick="selectRateForCalc('binance_usdt')" role="button" tabindex="0" title="Toca para calcular con USDT Binance">
                <div class="rate-card-top">
                    <div class="p2p-title-wrap">
                        <img src="public/crypto/binance.svg" alt="Binance" class="p2p-logo">
                        <div>
                            <div class="p2p-title">Binance USDT</div>
                            <div class="p2p-subtitle">Pago Móvil • Oferta y Demanda</div>
                        </div>
                    </div>
                    <span class="rate-badge binance">P2P</span>
                </div>
                <div class="p2p-card-body">
                    <div id="rate-binance-usdt" class="rate-display">--.--</div>
                    <span class="material-symbols-rounded p2p-calc-icon">calculate</span>
                </div>
                <div id="date-binance-usdt" class="footer-info">Actualizando Binance...</div>
            </div>

            <!-- Binance USDC -->
            <div class="card p2p-card" onclick="selectRateForCalc('binance_usdc')" role="button" tabindex="0" title="Toca para calcular con USDC Binance">
                <div class="rate-card-top">
                    <div class="p2p-title-wrap">
                        <img src="public/crypto/binance.svg" alt="Binance" class="p2p-logo">
                        <div>
                            <div class="p2p-title">Binance USDC</div>
                            <div class="p2p-subtitle">Pago Móvil • Oferta y Demanda</div>
                        </div>
                    </div>
                    <span class="rate-badge binance">P2P</span>
                </div>
                <div class="p2p-card-body">
                    <div id="rate-binance-usdc" class="rate-display">--.--</div>
                    <span class="material-symbols-rounded p2p-calc-icon">calculate</span>
                </div>
                <div id="date-binance-usdc" class="footer-info">Actualizando Binance...</div>
            </div>

            <!-- OKX USDT -->
            <div class="card p2p-card" onclick="selectRateForCalc('okx_usdt')" role="button" tabindex="0" title="Toca para calcular con USDT OKX">
                <div class="rate-card-top">
                    <div class="p2p-title-wrap">
                        <img src="public/crypto/okx.svg" alt="OKX" class="p2p-logo">
                        <div>
                            <div class="p2p-title">OKX USDT</div>
                            <div class="p2p-subtitle">Pago Móvil • Libro de Órdenes</div>
                        </div>
                    </div>
                    <span class="rate-badge okx">P2P</span>
                </div>
                <div class="p2p-card-body">
                    <div id="rate-okx-usdt" class="rate-display">--.--</div>
                    <span class="material-symbols-rounded p2p-calc-icon">calculate</span>
                </div>
                <div id="date-okx-usdt" class="footer-info">Actualizando OKX...</div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; width: 100%; margin: 16px 0 24px 0;">
            <button class="refresh-btn" style="background-color: var(--md-sys-color-surface-container); color: var(--md-sys-color-primary); flex: 1; max-width: 160px;" onclick="switchTab('history')">
                <span class="material-symbols-rounded">history</span> Historial
            </button>
            <button class="refresh-btn" style="flex: 1; max-width: 160px;" onclick="fetchRate(true)">
                <span class="material-symbols-rounded">refresh</span> Actualizar
            </button>
        </div>
    </div>
</div>
