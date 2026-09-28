<div id="pagomovil-tab-content" class="tab-content">
    <div class="container pm-container">
        <!-- VISTA 1: ONBOARDING / TOUR CON SLIDES (Cuando no hay datos guardados) -->
        <div id="pm-onboarding-view" class="pm-view">
            <div class="pm-tour-card">
                <div class="pm-carousel-wrapper">
                    <div id="pm-carousel" class="pm-carousel">
                        <!-- Slide 1 -->
                        <div class="pm-slide active" data-slide="0">
                            <div class="pm-slide-icon-wrap" style="background-color: rgba(163, 241, 203, 0.25); color: #00875a;">
                                <span class="material-symbols-rounded">payments</span>
                            </div>
                            <h3 class="pm-slide-title">Cobra en un toque</h3>
                            <p class="pm-slide-desc">Olvídate de dictar tu cédula, banco y teléfono una y otra vez. Ten tus datos siempre listos para copiar y cobrar de inmediato.</p>
                        </div>
                        <!-- Slide 2 -->
                        <div class="pm-slide" data-slide="1">
                            <div class="pm-slide-icon-wrap" style="background-color: rgba(177, 211, 254, 0.3); color: #1f84e8;">
                                <span class="material-symbols-rounded">account_balance</span>
                            </div>
                            <h3 class="pm-slide-title">Múltiples bancos</h3>
                            <p class="pm-slide-desc">¿Tienes Banesco, Venezuela y Mercantil? Tus datos principales se configuran una sola vez; añade otros bancos con solo un clic.</p>
                        </div>
                        <!-- Slide 3 -->
                        <div class="pm-slide" data-slide="2">
                            <div class="pm-slide-icon-wrap" style="background-color: rgba(223, 184, 255, 0.35); color: #9e2abe;">
                                <span class="material-symbols-rounded">qr_code_2</span>
                            </div>
                            <h3 class="pm-slide-title">Código QR instantáneo</h3>
                            <p class="pm-slide-desc">Genera tu código QR sin necesidad de conexión a internet para que cualquier cliente lo escanee directamente con su banco.</p>
                        </div>
                    </div>
                </div>

                <!-- Indicadores de slide -->
                <div class="pm-indicators">
                    <span class="pm-dot active" onclick="goToPmSlide(0)"></span>
                    <span class="pm-dot" onclick="goToPmSlide(1)"></span>
                    <span class="pm-dot" onclick="goToPmSlide(2)"></span>
                </div>

                <!-- Botón principal para iniciar -->
                <button id="pm-btn-start" class="pm-btn-primary" onclick="openPmModal('create')">
                    <span class="material-symbols-rounded">add_circle</span>
                    Configurar mi Pago Móvil
                </button>
            </div>
        </div>

        <!-- VISTA 2: PERFIL ACTIVO & SELECTOR DE BANCOS -->
        <div id="pm-active-view" class="pm-view" style="display: none;">
            <!-- Cabecera del Perfil -->
            <div class="pm-profile-header">
                <div class="pm-user-meta">
                    <span class="pm-user-tag">Beneficiario</span>
                    <h2 id="pm-display-holder" class="pm-user-name">--</h2>
                    <span id="pm-display-doc" class="pm-user-doc">--</span>
                </div>
                <button class="pm-icon-btn" title="Editar Perfil" onclick="openPmModal('edit')">
                    <span class="material-symbols-rounded">edit</span>
                </button>
            </div>

            <!-- Carrusel horizontal de Bancos Registrados -->
            <div class="pm-banks-section">
                <span class="pm-section-label">Bancos Registrados</span>
                <div class="pm-chips-scroll">
                    <div id="pm-chips-list" class="pm-chips-list">
                        <!-- Chips renderizados dinámicamente con JS -->
                    </div>
                    <button class="pm-chip-add" title="Añadir otro banco" onclick="openAddBankModal()">
                        <span class="material-symbols-rounded">add</span>
                        <span>Banco</span>
                    </button>
                </div>
            </div>

            <!-- Tarjeta Principal del Pago Móvil Seleccionado -->
            <div id="pm-current-card" class="pm-card">
                <div class="pm-card-top">
                    <div class="pm-card-bank-badge">
                        <span class="material-symbols-rounded">account_balance</span>
                        <span id="pm-card-bank-name">Banco</span>
                    </div>
                    <span id="pm-card-bank-code" class="pm-bank-code-pill">0000</span>
                </div>

                <div class="pm-card-body">
                    <div class="pm-field-row">
                        <span class="pm-field-label">Teléfono:</span>
                        <span id="pm-card-phone" class="pm-field-value">04XX-XXXXXXX</span>
                    </div>
                    <div class="pm-field-row">
                        <span class="pm-field-label">Cédula / RIF:</span>
                        <span id="pm-card-doc" class="pm-field-value">V-00000000</span>
                    </div>
                    <div class="pm-field-row">
                        <span class="pm-field-label">Titular:</span>
                        <span id="pm-card-holder" class="pm-field-value">--</span>
                    </div>
                </div>

                <!-- Botones de Acción Rápida -->
                <div class="pm-card-actions">
                    <button id="pm-btn-copy" class="pm-action-btn pm-action-copy" onclick="copyPmData()">
                        <span class="material-symbols-rounded">content_copy</span>
                        <span>Copiar Datos</span>
                    </button>
                    <button id="pm-btn-qr" class="pm-action-btn pm-action-qr" onclick="showPmQR()">
                        <span class="material-symbols-rounded">qr_code_2</span>
                        <span>Ver QR</span>
                    </button>
                </div>

                <div class="pm-card-footer">
                    <button class="pm-delete-bank-btn" onclick="deleteCurrentBank()">
                        <span class="material-symbols-rounded">delete_outline</span>
                        <span>Eliminar este banco</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
