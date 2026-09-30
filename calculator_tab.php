<div id="calculator-tab-content" class="tab-content">
    <div class="container">
        <div class="combined-card">
            <span class="operation-result" id="operation-result"></span>
            
            <div class="input-row">
                <span class="currency-prefix" id="label-top">USD</span>
                <input type="text" id="input-top" placeholder="0,00" value="" readonly inputmode="none">
                <div class="custom-caret" id="custom-caret"></div>
            </div>
            
            <div class="input-divider"></div>
            
            <div class="input-row">
                <span class="currency-prefix" id="label-bottom">VES</span>
                <input type="text" id="input-bottom" placeholder="0,00" value="" readonly inputmode="none">
            </div>
        </div>

        <div class="keypad">
            <button class="keypad-btn key-cotton" data-key="C">C</button>
            <button class="keypad-btn action" data-key="backspace"><span class="material-symbols-rounded">backspace</span></button>
            <button class="keypad-btn operator" data-key="/">/</button>
            <button class="keypad-btn operator" data-key="x">x</button>
            
            <button class="keypad-btn" data-key="7">7</button>
            <button class="keypad-btn" data-key="8">8</button>
            <button class="keypad-btn" data-key="9">9</button>
            <button class="keypad-btn operator" data-key="-">-</button>
            
            <button class="keypad-btn" data-key="4">4</button>
            <button class="keypad-btn" data-key="5">5</button>
            <button class="keypad-btn" data-key="6">6</button>
            <button class="keypad-btn operator" data-key="+">+</button>
            
            <button class="keypad-btn" data-key="1">1</button>
            <button class="keypad-btn" data-key="2">2</button>
            <button class="keypad-btn" data-key="3">3</button>
            <button class="keypad-btn key-mint" id="swap-btn" data-key="swap" title="Invertir monedas">
                <span class="material-symbols-rounded">swap_vert</span>
            </button>
            
            <button class="keypad-btn key-zero" data-key="0">0</button>
            <button class="keypad-btn" data-key=",">,</button>
            <button class="keypad-btn key-mint" id="calc-rate-picker-btn" onclick="openCalcRateModal()" title="Seleccionar tasa" aria-label="Seleccionar tasa">
                <img id="calc-picker-active-icon" src="public/bcv.svg" alt="Tasa activa" class="calc-picker-icon">
            </button>
        </div>
    </div>
    <!-- Medidor invisible para posición del caret -->
    <span id="text-measurer"></span>

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
                <!-- Dólar Oficial BCV -->
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

                <!-- Binance USDT -->
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

                <!-- Binance USDC -->
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

                <!-- OKX USDT -->
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

                <!-- Euro Oficial BCV -->
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
            </div>
        </div>
    </div>
</div>
