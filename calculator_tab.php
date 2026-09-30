<div id="calculator-tab-content" class="tab-content">
    <div class="container">
        <!-- Selector de tasa para la calculadora -->
        <div class="calc-rate-selector" id="calc-rate-selector">
            <button type="button" class="rate-chip active" data-rate-type="usd" onclick="setCalcRateType('usd')">
                <span>Dólar BCV</span>
                <span class="rate-chip-val" id="chip-val-usd">--</span>
            </button>
            <button type="button" class="rate-chip" data-rate-type="binance_usdt" onclick="setCalcRateType('binance_usdt')">
                <img src="public/crypto/binance.svg" alt="Binance" class="rate-chip-icon">
                <span>USDT</span>
                <span class="rate-chip-val" id="chip-val-binance-usdt">--</span>
            </button>
            <button type="button" class="rate-chip" data-rate-type="binance_usdc" onclick="setCalcRateType('binance_usdc')">
                <img src="public/crypto/binance.svg" alt="Binance" class="rate-chip-icon">
                <span>USDC</span>
                <span class="rate-chip-val" id="chip-val-binance-usdc">--</span>
            </button>
            <button type="button" class="rate-chip" data-rate-type="okx_usdt" onclick="setCalcRateType('okx_usdt')">
                <img src="public/crypto/okx.svg" alt="OKX" class="rate-chip-icon">
                <span>OKX</span>
                <span class="rate-chip-val" id="chip-val-okx-usdt">--</span>
            </button>
            <button type="button" class="rate-chip" data-rate-type="eur" onclick="setCalcRateType('eur')">
                <span>Euro BCV</span>
                <span class="rate-chip-val" id="chip-val-eur">--</span>
            </button>
        </div>

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
            <button class="keypad-btn key-mint" data-key="=">=</button>
            
            <button class="keypad-btn key-zero" data-key="0">0</button>
            <button class="keypad-btn" data-key=",">,</button>
            <button class="keypad-btn key-mint" id="swap-btn" data-key="swap">
                <span class="material-symbols-rounded">swap_vert</span>
            </button>
        </div>
    </div>
    <!-- Medidor invisible para posición del caret -->
    <span id="text-measurer"></span>
</div>
