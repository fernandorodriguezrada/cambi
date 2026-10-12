<div id="history-tab-content" class="tab-content">
    <div class="container">
        <div class="card" style="text-align: left; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-family: 'Inter', sans-serif; margin: 0; font-size: 1.2rem; font-weight: 800;">Historial BCV</h2>
                <button type="button" class="refresh-btn history-back-btn" style="padding: 6px 14px; font-size: 0.85rem; border-radius: 14px; background-color: var(--md-sys-color-surface-container-high); color: var(--md-sys-color-primary); margin: 0;" onclick="switchTab('dashboard')">
                    <span class="material-symbols-rounded" style="font-size: 18px;">arrow_back</span> Volver
                </button>
            </div>
            
            <div id="history-list" style="max-height: 60vh; overflow-y: auto;">
                <div class="history-header">
                    <span>Fecha / Hora</span><span style="text-align: right">USD</span><span style="text-align: right">EUR</span>
                </div>
                <p class="footer-info" style="text-align: center; margin-top: 20px;">No hay registros significativos aún.</p>
            </div>
        </div>
    </div>
</div>