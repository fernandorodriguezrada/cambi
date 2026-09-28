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

    <!-- MODAL 1: CONFIGURAR / EDITAR PERFIL PRINCIPAL -->
    <div id="pm-modal-profile" class="pm-modal-overlay">
        <div class="pm-modal-sheet">
            <div class="pm-modal-header">
                <h3 id="pm-modal-title">Configurar Pago Móvil</h3>
                <button class="pm-modal-close" onclick="closePmModal()">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <form id="pm-form-profile" onsubmit="savePmProfile(event)">
                <div class="pm-form-group">
                    <label for="pm-input-holder">Titular o Nombre del Negocio</label>
                    <input type="text" id="pm-input-holder" placeholder="Ej: Fernando Rodríguez" required maxlength="50" autocomplete="name">
                </div>

                <div class="pm-form-row">
                    <div class="pm-form-group" style="flex: 0 0 80px;">
                        <label for="pm-input-doc-type">Tipo</label>
                        <select id="pm-input-doc-type">
                            <option value="V">V</option>
                            <option value="E">E</option>
                            <option value="J">J</option>
                            <option value="G">G</option>
                        </select>
                    </div>
                    <div class="pm-form-group" style="flex: 1;">
                        <label for="pm-input-doc-num">Cédula / RIF</label>
                        <input type="tel" id="pm-input-doc-num" placeholder="12345678" required maxlength="10" inputmode="numeric">
                    </div>
                </div>

                <div class="pm-form-row">
                    <div class="pm-form-group" style="flex: 0 0 100px;">
                        <label for="pm-input-phone-prefix">Prefijo</label>
                        <select id="pm-input-phone-prefix">
                            <option value="0412">0412</option>
                            <option value="0414">0414</option>
                            <option value="0424">0424</option>
                            <option value="0416">0416</option>
                            <option value="0426">0426</option>
                        </select>
                    </div>
                    <div class="pm-form-group" style="flex: 1;">
                        <label for="pm-input-phone-num">Teléfono</label>
                        <input type="tel" id="pm-input-phone-num" placeholder="1234567" required maxlength="7" inputmode="numeric">
                    </div>
                </div>

                <!-- Selección de Banco inicial (visible solo al crear perfil) -->
                <div id="pm-bank-select-group" class="pm-form-group">
                    <label for="pm-input-bank">Banco Principal</label>
                    <select id="pm-input-bank" required>
                        <option value="">Selecciona tu banco</option>
                        <option value="0102">0102 - Banco de Venezuela</option>
                        <option value="0105">0105 - Banco Mercantil</option>
                        <option value="0108">0108 - BBVA Banco Provincial</option>
                        <option value="0134">0134 - Banesco</option>
                        <option value="0172">0172 - Bancamiga</option>
                        <option value="0114">0114 - Bancaribe</option>
                        <option value="0191">0191 - Banco Nacional de Crédito (BNC)</option>
                        <option value="0115">0115 - Banco Exterior</option>
                        <option value="0175">0175 - Banco Bicentenario</option>
                        <option value="0163">0163 - Banco del Tesoro</option>
                        <option value="0171">0171 - Banco Activo</option>
                        <option value="0174">0174 - Banplus</option>
                        <option value="0157">0157 - DelSur</option>
                        <option value="0151">0151 - BFC Banco Fondo Común</option>
                        <option value="0168">0168 - Bancrecer</option>
                        <option value="0177">0177 - BANFANB</option>
                        <option value="0104">0104 - Venezolano de Crédito</option>
                        <option value="0128">0128 - Banco Caroní</option>
                        <option value="0137">0137 - Banco Sofitasa</option>
                        <option value="0138">0138 - Banco Plaza</option>
                        <option value="0156">0156 - 100% Banco</option>
                        <option value="0166">0166 - Banco Agrícola de Venezuela</option>
                        <option value="0169">0169 - Mi Banco</option>
                        <option value="0146">0146 - Bangente</option>
                    </select>
                </div>

                <div class="pm-modal-actions">
                    <button type="button" class="pm-btn-secondary" onclick="closePmModal()">Cancelar</button>
                    <button type="submit" class="pm-btn-primary">Guardar Datos</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: AÑADIR OTRO BANCO RÁPIDAMENTE -->
    <div id="pm-modal-add-bank" class="pm-modal-overlay">
        <div class="pm-modal-sheet">
            <div class="pm-modal-header">
                <h3>Añadir Otro Banco</h3>
                <button class="pm-modal-close" onclick="closeAddBankModal()">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <p class="pm-modal-subtext">Tu titular, cédula y teléfono se mantendrán iguales. Solo elige el nuevo banco:</p>
            <form id="pm-form-add-bank" onsubmit="saveNewBank(event)">
                <div class="pm-form-group">
                    <label for="pm-new-bank-select">Banco a Añadir</label>
                    <select id="pm-new-bank-select" required>
                        <option value="">Selecciona un banco</option>
                        <option value="0102">0102 - Banco de Venezuela</option>
                        <option value="0105">0105 - Banco Mercantil</option>
                        <option value="0108">0108 - BBVA Banco Provincial</option>
                        <option value="0134">0134 - Banesco</option>
                        <option value="0172">0172 - Bancamiga</option>
                        <option value="0114">0114 - Bancaribe</option>
                        <option value="0191">0191 - Banco Nacional de Crédito (BNC)</option>
                        <option value="0115">0115 - Banco Exterior</option>
                        <option value="0175">0175 - Banco Bicentenario</option>
                        <option value="0163">0163 - Banco del Tesoro</option>
                        <option value="0171">0171 - Banco Activo</option>
                        <option value="0174">0174 - Banplus</option>
                        <option value="0157">0157 - DelSur</option>
                        <option value="0151">0151 - BFC Banco Fondo Común</option>
                        <option value="0168">0168 - Bancrecer</option>
                        <option value="0177">0177 - BANFANB</option>
                        <option value="0104">0104 - Venezolano de Crédito</option>
                        <option value="0128">0128 - Banco Caroní</option>
                        <option value="0137">0137 - Banco Sofitasa</option>
                        <option value="0138">0138 - Banco Plaza</option>
                        <option value="0156">0156 - 100% Banco</option>
                        <option value="0166">0166 - Banco Agrícola de Venezuela</option>
                        <option value="0169">0169 - Mi Banco</option>
                        <option value="0146">0146 - Bangente</option>
                    </select>
                </div>
                <div class="pm-modal-actions">
                    <button type="button" class="pm-btn-secondary" onclick="closeAddBankModal()">Cancelar</button>
                    <button type="submit" class="pm-btn-primary">Añadir Banco</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: VISOR DE CÓDIGO QR -->
    <div id="pm-modal-qr" class="pm-modal-overlay">
        <div class="pm-modal-sheet" style="text-align: center;">
            <div class="pm-modal-header" style="justify-content: flex-end;">
                <button class="pm-modal-close" onclick="closePmQR()">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <h3 id="pm-qr-bank-title" style="margin-top: -10px; margin-bottom: 4px;">Código QR Pago Móvil</h3>
            <p id="pm-qr-holder-subtitle" class="pm-modal-subtext" style="margin-bottom: 16px;">Escanea para pagar</p>
            
            <div class="pm-qr-canvas-wrapper">
                <div id="pm-qrcode-container"></div>
            </div>

            <div class="pm-qr-info-box">
                <p id="pm-qr-info-text">--</p>
            </div>

            <div class="pm-modal-actions" style="justify-content: center; margin-top: 18px;">
                <button type="button" class="pm-btn-secondary" onclick="closePmQR()">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICACIÓN -->
    <div id="pm-toast" class="pm-toast">
        <span class="material-symbols-rounded">check_circle</span>
        <span id="pm-toast-msg">¡Datos copiados al portapapeles!</span>
    </div>
</div>
