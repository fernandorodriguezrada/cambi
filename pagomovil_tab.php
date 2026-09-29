<div id="pagomovil-tab-content" class="tab-content">
    <div class="container pm-container">
        <!-- VISTA 1: ONBOARDING / TOUR CON SLIDES (Cuando no hay datos guardados) -->
        <div id="pm-onboarding-view" class="pm-view">
            <div class="pm-tour-card">
                <div class="pm-carousel-wrapper">
                    <div id="pm-carousel" class="pm-carousel">
                        <!-- Slide 1 -->
                        <div class="pm-slide active" data-slide="0">
                            <div class="pm-slide-icon-wrap icon-mint">
                                <span class="material-symbols-rounded">payments</span>
                            </div>
                            <h3 class="pm-slide-title">Cobra en un toque</h3>
                            <p class="pm-slide-desc">Olvídate de dictar tu cédula, banco y teléfono una y otra vez. Ten tus datos siempre listos para copiar y cobrar de inmediato.</p>
                        </div>
                        <!-- Slide 2 -->
                        <div class="pm-slide" data-slide="1">
                            <div class="pm-slide-icon-wrap icon-blue">
                                <span class="material-symbols-rounded">account_balance</span>
                            </div>
                            <h3 class="pm-slide-title">Múltiples bancos</h3>
                            <p class="pm-slide-desc">¿Tienes Banesco, Venezuela y Mercantil? Tus datos principales se configuran una sola vez; añade otros bancos con solo un clic.</p>
                        </div>
                        <!-- Slide 3 -->
                        <div class="pm-slide" data-slide="2">
                            <div class="pm-slide-icon-wrap icon-mauve">
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
                    Configurar mi primer Pago Móvil
                </button>
            </div>
        </div>

        <!-- VISTA 2: PERFIL ACTIVO & BLOQUES DE BANCOS -->
        <div id="pm-active-view" class="pm-view" style="display: none;">
            <!-- Cabecera del Perfil -->
            <div class="pm-profile-header">
                <div class="pm-user-meta">
                    <span class="pm-user-tag">Beneficiario</span>
                    <h2 id="pm-display-holder" class="pm-user-name">--</h2>
                    <div class="pm-user-submeta">
                        <span id="pm-display-doc" class="pm-user-doc">--</span>
                        <span class="pm-meta-dot">•</span>
                        <span id="pm-display-phone" class="pm-user-phone">--</span>
                    </div>
                </div>
                <button class="pm-icon-btn" title="Editar Perfil" onclick="openPmModal('edit')">
                    <span class="material-symbols-rounded">edit</span>
                </button>
            </div>

            <!-- Sección de Bloques de Bancos -->
            <div class="pm-banks-blocks-section">
                <div class="pm-blocks-header">
                    <span class="pm-section-label">Bancos Registrados</span>
                    <span id="pm-banks-count-badge" class="pm-count-badge">0</span>
                </div>

                <!-- Grid de bloques de bancos agregados -->
                <div id="pm-banks-grid" class="pm-banks-grid">
                    <!-- Los bloques se renderizan dinámicamente con JS -->
                </div>
            </div>


        </div>
    </div>
</div>
