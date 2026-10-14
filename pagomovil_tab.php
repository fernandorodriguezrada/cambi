<div id="pagomovil-tab-content" class="tab-content">
    <div class="container pm-container">

        <!-- SELECTOR DE SUB-PESTAÑA BILLETERA (Material 3 Segmented Pills) -->
        <div class="wallet-subnav-wrapper">
            <div class="wallet-subnav-track" id="wallet-subnav-track">
                <button type="button" class="wallet-subnav-btn active" id="subtab-btn-cards" onclick="switchWalletSubTab('cards')">
                    <span class="material-symbols-rounded">credit_card</span>
                    <span>Tarjetas</span>
                </button>
                <button type="button" class="wallet-subnav-btn" id="subtab-btn-pm" onclick="switchWalletSubTab('pagomovil')">
                    <span class="material-symbols-rounded">payments</span>
                    <span>Pago Móvil</span>
                </button>
            </div>
        </div>

        <!-- SECCIÓN 1: VISTA DE TARJETAS (Google Wallet Deck) -->
        <div id="wallet-cards-section" class="wallet-subview active">
            <!-- VISTA 1A: ONBOARDING / TOUR CUANDO NO HAY TARJETAS -->
            <div id="wallet-cards-onboarding" class="pm-view" style="display: none;">
                <div class="pm-tour-card">
                    <div class="pm-carousel-wrapper">
                        <div id="wallet-carousel" class="pm-carousel">
                            <!-- Slide 1 -->
                            <div class="pm-slide active" data-slide="0">
                                <div class="pm-slide-icon-wrap icon-mint">
                                    <span class="material-symbols-rounded">credit_card</span>
                                </div>
                                <h3 class="pm-slide-title">Tus tarjetas a la mano</h3>
                                <p class="pm-slide-desc">Lleva tus tarjetas virtuales y físicas de Binance, Wally, Zinli, Bancamiga o personalizadas en un solo lugar seguro.</p>
                            </div>
                            <!-- Slide 2 -->
                            <div class="pm-slide" data-slide="1">
                                <div class="pm-slide-icon-wrap icon-blue">
                                    <span class="material-symbols-rounded">layers</span>
                                </div>
                                <h3 class="pm-slide-title">Estilo Google Wallet</h3>
                                <p class="pm-slide-desc">Diseño apilado interactivo. Toca cualquier tarjeta para expandirla, copiar tus números o ver tu CVV al instante.</p>
                            </div>
                            <!-- Slide 3 -->
                            <div class="pm-slide" data-slide="2">
                                <div class="pm-slide-icon-wrap icon-mauve">
                                    <span class="material-symbols-rounded">lock</span>
                                </div>
                                <h3 class="pm-slide-title">100% Privado y Local</h3>
                                <p class="pm-slide-desc">Tus datos nunca salen de tu dispositivo. Se guardan cifrados localmente en tu navegador con soporte offline completo.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Indicadores de slide -->
                    <div class="pm-indicators" id="wallet-indicators">
                        <span class="pm-dot active" onclick="goToWalletSlide(0)"></span>
                        <span class="pm-dot" onclick="goToWalletSlide(1)"></span>
                        <span class="pm-dot" onclick="goToWalletSlide(2)"></span>
                    </div>

                    <!-- Botón principal para iniciar -->
                    <button class="pm-btn-primary" onclick="openWalletCardModal()">
                        <span class="material-symbols-rounded">add_card</span>
                        Añadir mi primera tarjeta
                    </button>
                </div>
            </div>

            <!-- VISTA 1B: DECK DE TARJETAS (Cuando hay tarjetas guardadas) -->
            <div id="wallet-cards-active-view" style="display: none; width: 100%;">
                <!-- Cabecera de Tarjetas -->
                <div class="wallet-cards-header">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="pm-section-label">Mis Tarjetas</span>
                        <span id="wallet-cards-count-badge" class="pm-count-badge">0</span>
                    </div>
                    <div class="wallet-cards-header-actions" style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" class="pm-add-bank-desktop-btn" onclick="openWalletCardModal()">
                            <span class="material-symbols-rounded">add_card</span>
                            <span>Añadir Tarjeta</span>
                        </button>
                        <button type="button" id="wallet-deck-toggle-btn" class="pm-grid-toggle-btn" onclick="toggleWalletDeckMode()" title="Alternar vista apilada / expandida" aria-label="Alternar vista apilada">
                            <span id="wallet-deck-toggle-icon" class="material-symbols-rounded">layers</span>
                        </button>
                    </div>
                </div>

                <!-- Contenedor del Stack de Tarjetas estilo Google Wallet -->
                <div id="wallet-cards-deck" class="wallet-cards-deck mode-stacked">
                    <!-- Se renderizan dinámicamente con JS -->
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: VISTA DE PAGO MÓVIL (Existente) -->
        <div id="wallet-pm-section" class="wallet-subview" style="display: none;">
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
                    <div class="pm-indicators" id="pm-indicators">
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
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="pm-section-label">Bancos Registrados</span>
                            <span id="pm-banks-count-badge" class="pm-count-badge">0</span>
                        </div>
                        <div class="pm-blocks-header-actions" style="display: flex; align-items: center; gap: 8px;">
                            <button type="button" id="pm-add-bank-desktop-btn" class="pm-add-bank-desktop-btn" onclick="openAddBankModal()">
                                <span class="material-symbols-rounded">add</span>
                                <span>Añadir Banco</span>
                            </button>
                            <button type="button" id="pm-grid-toggle-btn" class="pm-grid-toggle-btn" tabindex="-1" onclick="togglePmGridLayout()" title="Cambiar a vista de 1 columna" aria-label="Cambiar vista de columnas">
                                <span id="pm-grid-toggle-icon" class="material-symbols-rounded">view_agenda</span>
                            </button>
                        </div>
                    </div>

                    <!-- Grid de bloques de bancos agregados -->
                    <div id="pm-banks-grid" class="pm-banks-grid">
                        <!-- Los bloques se renderizan dinámicamente con JS -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
