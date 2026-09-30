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
            <button type="button" class="keypad-btn calc-rate-btn" id="calc-rate-picker-btn" onclick="openCalcRateModal()" title="Seleccionar tasa" aria-label="Seleccionar tasa">
                <img id="calc-picker-active-icon" src="public/bcv.svg" alt="Tasa activa" class="calc-picker-icon">
            </button>
        </div>
    </div>
    <!-- Medidor invisible para posición del caret -->
    <span id="text-measurer"></span>
</div>
