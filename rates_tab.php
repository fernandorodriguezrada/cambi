<div id="rates-tab-content" class="tab-content active">
    <div class="container">
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
        <div style="display: flex; gap: 12px; justify-content: center; width: 100%;">
            <button class="refresh-btn" style="background-color: var(--md-sys-color-surface-container); color: var(--md-sys-color-primary); flex: 1; max-width: 160px;" onclick="switchTab('history')">
                <span class="material-symbols-rounded">history</span> Historial
            </button>
            <button class="refresh-btn" style="flex: 1; max-width: 160px;" onclick="fetchRate(true)">
                <span class="material-symbols-rounded">refresh</span> Actualizar
            </button>
        </div>
    </div>
</div>
