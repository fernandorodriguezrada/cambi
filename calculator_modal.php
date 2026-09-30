<!-- Modal Selector de Tasa para la Calculadora -->
<div class="pm-modal-overlay" id="calc-rate-modal" onclick="if(event.target===this) closeCalcRateModal()">
    <div class="pm-modal-sheet">
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header" style="margin-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-rounded" style="color: var(--md-sys-color-primary);">currency_exchange</span>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700;">Tasa de Conversión</h3>
            </div>
            <button type="button" class="pm-modal-close" onclick="closeCalcRateModal()" aria-label="Cerrar">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        
        <div class="calc-rate-options-list">
            <!-- 1. Dólar Oficial BCV -->
            <div class="calc-rate-option-item active" data-type="usd" onclick="selectCalcRateFromModal('usd')">
                <div class="calc-rate-option-left">
                    <img src="public/bcv.svg" alt="BCV" class="calc-rate-option-logo">
                    <div class="calc-rate-option-texts">
                        <span class="calc-rate-option-name">Dólar BCV</span>
                        <span class="calc-rate-option-source">Tasa oficial BCV</span>
                    </div>
                </div>
                <div class="calc-rate-option-right">
                    <span class="calc-rate-option-value" id="modal-rate-val-usd">--.--</span>
                    <span class="material-symbols-rounded calc-rate-option-check">check_circle</span>
                </div>
            </div>

            <!-- 2. Euro Oficial BCV -->
            <div class="calc-rate-option-item" data-type="eur" onclick="selectCalcRateFromModal('eur')">
                <div class="calc-rate-option-left">
                    <img src="public/bcv.svg" alt="BCV" class="calc-rate-option-logo">
                    <div class="calc-rate-option-texts">
                        <span class="calc-rate-option-name">Euro BCV</span>
                        <span class="calc-rate-option-source">Tasa oficial BCV</span>
                    </div>
                </div>
                <div class="calc-rate-option-right">
                    <span class="calc-rate-option-value" id="modal-rate-val-eur">--.--</span>
                    <span class="material-symbols-rounded calc-rate-option-check">check_circle</span>
                </div>
            </div>

            <!-- 3. Binance USDT -->
            <div class="calc-rate-option-item" data-type="binance_usdt" onclick="selectCalcRateFromModal('binance_usdt')">
                <div class="calc-rate-option-left">
                    <img src="public/crypto/binance.svg" alt="Binance" class="calc-rate-option-logo">
                    <div class="calc-rate-option-texts">
                        <span class="calc-rate-option-name">Binance USDT</span>
                        <span class="calc-rate-option-source">Mercado P2P</span>
                    </div>
                </div>
                <div class="calc-rate-option-right">
                    <span class="calc-rate-option-value" id="modal-rate-val-binance-usdt">--.--</span>
                    <span class="material-symbols-rounded calc-rate-option-check">check_circle</span>
                </div>
            </div>

            <!-- 4. Binance USDC -->
            <div class="calc-rate-option-item" data-type="binance_usdc" onclick="selectCalcRateFromModal('binance_usdc')">
                <div class="calc-rate-option-left">
                    <img src="public/crypto/binance.svg" alt="Binance" class="calc-rate-option-logo">
                    <div class="calc-rate-option-texts">
                        <span class="calc-rate-option-name">Binance USDC</span>
                        <span class="calc-rate-option-source">Mercado P2P</span>
                    </div>
                </div>
                <div class="calc-rate-option-right">
                    <span class="calc-rate-option-value" id="modal-rate-val-binance-usdc">--.--</span>
                    <span class="material-symbols-rounded calc-rate-option-check">check_circle</span>
                </div>
            </div>

            <!-- 5. OKX USDT -->
            <div class="calc-rate-option-item" data-type="okx_usdt" onclick="selectCalcRateFromModal('okx_usdt')">
                <div class="calc-rate-option-left">
                    <img src="public/crypto/okx.svg" alt="OKX" class="calc-rate-option-logo">
                    <div class="calc-rate-option-texts">
                        <span class="calc-rate-option-name">OKX USDT</span>
                        <span class="calc-rate-option-source">Mercado P2P</span>
                    </div>
                </div>
                <div class="calc-rate-option-right">
                    <span class="calc-rate-option-value" id="modal-rate-val-okx-usdt">--.--</span>
                    <span class="material-symbols-rounded calc-rate-option-check">check_circle</span>
                </div>
            </div>
        </div>
    </div>
</div>
