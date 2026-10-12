<div id="rates-tab-content" class="tab-content active">
    <div class="container rates-dashboard-container">
        <!-- Bloque 1: Tasas Oficiales -->
        <div class="rates-block rates-official-block">
            <div class="rates-section-header">
                <span class="rates-section-label">Tasas Oficiales</span>
            </div>

            <div class="rates-cards-grid rates-official-grid">
                <div class="card">
                    <div class="rate-card-title">
                        <img src="public/bcv.svg" alt="BCV" class="rate-platform-logo">
                        <span class="label">Precio del Dólar</span>
                    </div>
                    <div class="rate-amount-row">
                        <div id="rate" class="rate-display">--.--</div>
                        <button type="button" class="rate-copy-btn" onclick="event.stopPropagation(); copyRateCard('rate', this)" title="Copiar tasa" aria-label="Copiar tasa">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                    </div>
                    <div id="date" class="footer-info">Consultando BCV...</div>
                </div>

                <div class="card">
                    <div class="rate-card-title">
                        <img src="public/bcv.svg" alt="BCV" class="rate-platform-logo">
                        <span class="label">Precio del Euro</span>
                    </div>
                    <div class="rate-amount-row">
                        <div id="rate-eur" class="rate-display">--.--</div>
                        <button type="button" class="rate-copy-btn" onclick="event.stopPropagation(); copyRateCard('rate-eur', this)" title="Copiar tasa" aria-label="Copiar tasa">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                    </div>
                    <div id="date-eur" class="footer-info">Esperando datos...</div>
                </div>
            </div>
        </div>

        <!-- Bloque 2: Mercado P2P -->
        <div class="rates-block rates-p2p-block">
            <div class="rates-section-header rates-p2p-header">
                <span class="rates-section-label">Mercado P2P</span>
            </div>

            <div class="rates-cards-grid rates-p2p-grid">
                <div class="card">
                    <div class="rate-card-title">
                        <img src="public/crypto/binance.svg" alt="Binance" class="rate-platform-logo">
                        <span class="label">Binance USDT</span>
                    </div>
                    <div class="rate-amount-row">
                        <div id="rate-binance-usdt" class="rate-display">--.--</div>
                        <button type="button" class="rate-copy-btn" onclick="event.stopPropagation(); copyRateCard('rate-binance-usdt', this)" title="Copiar tasa" aria-label="Copiar tasa">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                    </div>
                    <div id="date-binance-usdt" class="footer-info">Consultando Binance...</div>
                </div>

                <div class="card">
                    <div class="rate-card-title">
                        <img src="public/crypto/binance.svg" alt="Binance" class="rate-platform-logo">
                        <span class="label">Binance USDC</span>
                    </div>
                    <div class="rate-amount-row">
                        <div id="rate-binance-usdc" class="rate-display">--.--</div>
                        <button type="button" class="rate-copy-btn" onclick="event.stopPropagation(); copyRateCard('rate-binance-usdc', this)" title="Copiar tasa" aria-label="Copiar tasa">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                    </div>
                    <div id="date-binance-usdc" class="footer-info">Consultando Binance...</div>
                </div>

                <div class="card">
                    <div class="rate-card-title">
                        <img src="public/crypto/okx.svg" alt="OKX" class="rate-platform-logo">
                        <span class="label">OKX USDT</span>
                    </div>
                    <div class="rate-amount-row">
                        <div id="rate-okx-usdt" class="rate-display">--.--</div>
                        <button type="button" class="rate-copy-btn" onclick="event.stopPropagation(); copyRateCard('rate-okx-usdt', this)" title="Copiar tasa" aria-label="Copiar tasa">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                    </div>
                    <div id="date-okx-usdt" class="footer-info">Consultando OKX...</div>
                </div>
            </div>
        </div>

        <!-- Bloque 3: Acciones -->
        <div class="rates-actions-row">
            <button type="button" class="refresh-btn rates-history-btn" onclick="switchTab('history')">
                <span class="material-symbols-rounded">history</span> Historial
            </button>
            <button type="button" class="refresh-btn rates-refresh-btn" onclick="fetchRate(true)">
                <span class="material-symbols-rounded">refresh</span> Actualizar
            </button>
        </div>
    </div>
</div>
