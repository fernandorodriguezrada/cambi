<div class="container">
    <div class="card">
        <span class="label">Precio del Dólar</span>
        <div id="rate" class="rate-display">--.--</div>
        <div id="date" class="footer-info">Cargando...</div>
    </div>

    <div class="input-group">
        <span class="label">Dólares (USD)</span>
        <span class="material-symbols-rounded input-icon">attach_money</span>
        <input type="text" id="usd-input" placeholder="0,00" inputmode="decimal">
    </div>

    <div class="input-group" style="text-align: center;">
        <span class="material-symbols-rounded" style="color: var(--md-sys-color-outline)">swap_vert</span>
    </div>

    <div class="input-group">
        <span class="label">Bolívares (VES)</span>
        <span class="material-symbols-rounded input-icon">payments</span>
        <input type="text" id="ves-input" placeholder="0,00" inputmode="decimal">
    </div>

    <button class="refresh-btn" onclick="fetchRate()">
        <span class="material-symbols-rounded">refresh</span> Actualizar
    </button>
</div>