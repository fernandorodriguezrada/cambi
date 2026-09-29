<!-- MODAL 1: CONFIGURAR / EDITAR PERFIL PRINCIPAL -->
<div id="pm-modal-profile" class="pm-modal-overlay">
    <div class="pm-modal-sheet">
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header">
            <h3 id="pm-modal-title">Configurar mi primer Pago Móvil</h3>
            <button type="button" class="pm-modal-close" onclick="closePmModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <p id="pm-modal-subtitle" class="pm-modal-subtext">Configura tus datos base una sola vez y añade tu primer banco para comenzar.</p>
        <form id="pm-form-profile" onsubmit="savePmProfile(event)">
            <div class="pm-form-group">
                <label for="pm-input-holder">Titular o Nombre del Negocio</label>
                <input type="text" id="pm-input-holder" placeholder="Ej: Fernando Rodríguez" required maxlength="50" autocomplete="name">
            </div>

            <div class="pm-form-row">
                <div class="pm-form-group" style="flex: 0 0 100px;">
                    <label for="pm-input-doc-type">Tipo</label>
                    <select id="pm-input-doc-type" class="pm-styled-select">
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
                    <select id="pm-input-phone-prefix" class="pm-styled-select">
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
                <select id="pm-input-bank" class="pm-styled-select" required>
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

<!-- MODAL 2: AÑADIR OTRO BANCO CON BUSCADOR -->
<div id="pm-modal-add-bank" class="pm-modal-overlay" onclick="if(event.target===this) closeAddBankModal()">
    <div class="pm-modal-sheet">
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header">
            <h3>Añadir Otro Banco</h3>
            <button type="button" class="pm-modal-close" onclick="closeAddBankModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <p class="pm-modal-subtext" style="margin-bottom: 12px;">Busca o selecciona el nuevo banco para vincular a tus datos:</p>
        
        <div class="pm-bank-search-bar">
            <span class="material-symbols-rounded pm-bank-search-icon">search</span>
            <input type="text" id="pm-bank-search-input" class="pm-bank-search-field" placeholder="Buscar por banco o código (ej: 0102, Mercantil)..." autocomplete="off" oninput="filterBankSearchResults(this.value)">
            <button type="button" id="pm-bank-search-clear" class="pm-bank-search-clear" onclick="clearBankSearch()" style="display: none;" title="Limpiar búsqueda">
                <span class="material-symbols-rounded">cancel</span>
            </button>
        </div>

        <div id="pm-bank-search-results" class="pm-bank-search-list">
            <!-- Resultados renderizados dinámicamente -->
        </div>

        <div class="pm-modal-actions" style="margin-top: 14px; justify-content: center;">
            <button type="button" class="pm-btn-secondary" onclick="closeAddBankModal()">Cerrar</button>
        </div>
    </div>
</div>


<!-- MODAL 4: DETALLE DE PAGO MÓVIL SELECCIONADO -->
<div id="pm-modal-detail" class="pm-modal-overlay">
    <div class="pm-modal-sheet">
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header">
            <div id="pm-detail-badge" class="pm-detail-bank-badge">
                <div id="pm-detail-logo-container" class="pm-detail-logo">
                    <!-- SVG logo del banco -->
                </div>
                <div>
                    <h3 id="pm-detail-bank-name" class="pm-detail-title">Banco</h3>
                    <span id="pm-detail-bank-code" class="pm-detail-code">0000</span>
                </div>
            </div>
            <button type="button" class="pm-modal-close" onclick="closePmDetailModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        <div class="pm-detail-fields">
            <!-- Fila Teléfono -->
            <div class="pm-detail-field-box">
                <div class="pm-detail-field-info">
                    <span class="pm-detail-field-label">Teléfono</span>
                    <span id="pm-detail-phone" class="pm-detail-field-val">04XX-XXXXXXX</span>
                </div>
                <button type="button" class="pm-detail-copy-single" title="Copiar Teléfono" onclick="copySinglePmField('phone')">
                    <span class="material-symbols-rounded">content_copy</span>
                </button>
            </div>

            <!-- Fila Cédula / RIF -->
            <div class="pm-detail-field-box">
                <div class="pm-detail-field-info">
                    <span class="pm-detail-field-label">Cédula / RIF</span>
                    <span id="pm-detail-doc" class="pm-detail-field-val">V-00000000</span>
                </div>
                <button type="button" class="pm-detail-copy-single" title="Copiar Cédula" onclick="copySinglePmField('doc')">
                    <span class="material-symbols-rounded">content_copy</span>
                </button>
            </div>

            <!-- Fila Titular -->
            <div class="pm-detail-field-box">
                <div class="pm-detail-field-info">
                    <span class="pm-detail-field-label">Titular</span>
                    <span id="pm-detail-holder" class="pm-detail-field-val">--</span>
                </div>
            </div>
        </div>

        <!-- Acciones Principales -->
        <div class="pm-detail-actions">
            <button type="button" class="pm-btn-primary" onclick="copyPmData()">
                <span class="material-symbols-rounded">content_copy</span>
                <span>Copiar Datos</span>
            </button>
            <button type="button" class="pm-btn-secondary" onclick="showPmQR()">
                <span class="material-symbols-rounded">qr_code_2</span>
                <span>Ver QR</span>
            </button>
            <button type="button" class="pm-btn-delete-icon" title="Eliminar este banco" onclick="confirmDeleteCurrentBank()">
                <span class="material-symbols-rounded">delete</span>
            </button>
        </div>
    </div>
</div>

<!-- MODAL 3: VISOR DE CÓDIGO QR -->
<div id="pm-modal-qr" class="pm-modal-overlay" onclick="if(event.target===this) closePmQR()">
    <div class="pm-modal-sheet" style="text-align: center;">
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header" style="justify-content: flex-end;">
            <button type="button" class="pm-modal-close" onclick="closePmQR()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        <input type="file" id="pm-qr-file-input" accept="image/*" style="display:none;" onchange="handleImportBankQR(event)">

        <!-- ESTADO 1: QR ACTIVO (OFICIAL O TEXTO PLANO) -->
        <div id="pm-qr-view-active">
            <h3 id="pm-qr-bank-title" style="margin-top: -10px; margin-bottom: 4px;">Código QR Pago Móvil</h3>
            <p id="pm-qr-holder-subtitle" class="pm-modal-subtext" style="margin-bottom: 8px;">Escanea para pagar</p>
            
            <div id="pm-qr-badge-container">
                <span id="pm-qr-badge" class="pm-qr-badge-official">
                    <span class="material-symbols-rounded" style="font-size: 1rem;">verified</span>
                    <span id="pm-qr-badge-text">QR Oficial Suiche 7B</span>
                </span>
            </div>

            <div class="pm-qr-canvas-wrapper">
                <div id="pm-qrcode-container"></div>
            </div>

            <div class="pm-qr-info-box">
                <p id="pm-qr-info-text">--</p>
            </div>

            <div style="margin-top: 14px; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                <button type="button" class="pm-btn-secondary" style="font-size: 0.82rem; padding: 6px 16px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;" onclick="document.getElementById('pm-qr-file-input').click()">
                    <span class="material-symbols-rounded" style="font-size: 1.15rem;">photo_camera</span>
                    <span>Reemplazar captura de Mi QR</span>
                </button>
            </div>

            <div class="pm-modal-actions" style="justify-content: center; margin-top: 14px;">
                <button type="button" class="pm-btn-secondary" onclick="closePmQR()">Cerrar</button>
            </div>
        </div>

        <!-- ESTADO 2: SIN QR OFICIAL (INVITACIÓN A IMPORTAR CAPTURA) -->
        <div id="pm-qr-view-empty" style="display: none; padding: 8px 8px 14px 8px;">
            <div class="pm-qr-empty-icon">
                <span class="material-symbols-rounded">qr_code_scanner</span>
            </div>
            <h3 id="pm-qr-empty-title" style="margin-top: 0; margin-bottom: 6px; font-size: 1.18rem; font-weight: 700;">Sincroniza el QR</h3>
            <p style="font-size: 0.88rem; color: var(--md-sys-color-on-surface-variant); margin-bottom: 20px; line-height: 1.45; max-width: 320px; margin-left: auto; margin-right: auto;">
                Para que BDVApp, Banesco y otros bancos lo lean directamente, importa una sola vez la captura de <strong>"Mi QR"</strong> de tu banco.
            </p>

            <div style="display: flex; flex-direction: column; gap: 10px; align-items: center; max-width: 280px; margin: 0 auto;">
                <button type="button" class="pm-btn-primary" style="width: 100%; justify-content: center; padding: 12px 18px;" onclick="document.getElementById('pm-qr-file-input').click()">
                    <span class="material-symbols-rounded">photo_camera</span>
                    <span>Importar captura de Mi QR</span>
                </button>

                <button type="button" class="pm-btn-secondary" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 8px 14px;" onclick="showPlainTextQR()">
                    <span class="material-symbols-rounded">text_snippet</span>
                    <span>Ver QR en texto plano</span>
                </button>

                <button type="button" class="pm-btn-secondary" style="font-size: 0.82rem; padding: 6px 14px; margin-top: 4px;" onclick="closePmQR()">
                    <span>Cerrar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 5. MODAL DE CONFIRMACIÓN PARA ELIMINAR BANCO -->
<div id="pm-modal-confirm-delete" class="pm-modal-overlay pm-modal-overlay-confirm" onclick="if(event.target===this) closePmConfirmDeleteModal()">
    <div class="pm-modal-sheet pm-modal-confirm-sheet">
        <div class="pm-modal-handle"></div>
        <div class="pm-confirm-icon-box">
            <span class="material-symbols-rounded">delete</span>
        </div>
        <h3 class="pm-confirm-title">¿Eliminar este banco?</h3>
        <p id="pm-confirm-bank-desc" class="pm-confirm-desc">
            ¿Estás seguro de que deseas eliminar este banco de tus pagos móviles?
        </p>
        <div class="pm-confirm-actions">
            <button type="button" class="pm-btn-secondary pm-btn-cancel-delete" onclick="closePmConfirmDeleteModal()">
                Cancelar
            </button>
            <button type="button" class="pm-btn-destructive" onclick="executeDeleteCurrentBank()">
                <span class="material-symbols-rounded">delete</span>
                <span>Eliminar</span>
            </button>
        </div>
    </div>
</div>

<!-- MODAL 6: ESCÁNER QR PARA PAGAR -->
<div id="pm-modal-scanner" class="pm-modal-overlay" onclick="if(event.target===this) closeQrScannerModal()">
    <div class="pm-modal-sheet" style="max-width: 420px;">
        <div class="pm-modal-handle"></div>

        <!-- VISTA A: CÁMARA ESCANEANDO -->
        <div id="pm-scanner-view-camera">
            <div class="pm-modal-header" style="margin-bottom: 12px;">
                <h3>Escanear QR para pagar</h3>
                <button type="button" class="pm-modal-close" onclick="closeQrScannerModal()">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <p class="pm-modal-subtext" style="margin-bottom: 14px;">Apunta tu cámara al código QR de pago:</p>
            
            <div class="pm-scanner-viewport">
                <video id="pm-scanner-video" playsinline autoplay muted></video>
                <div class="pm-scanner-target-box">
                    <div class="pm-scanner-laser"></div>
                </div>
            </div>

            <input type="file" id="pm-scanner-file-input" accept="image/*" style="display: none;" onchange="handleScannerFile(event)">

            <div style="display: flex; flex-direction: column; gap: 8px; align-items: center; margin-top: 14px;">
                <button type="button" class="pm-btn-secondary" style="font-size: 0.85rem; padding: 8px 16px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;" onclick="document.getElementById('pm-scanner-file-input').click()">
                    <span class="material-symbols-rounded" style="font-size: 1.15rem;">photo_library</span>
                    <span>O subir captura del QR</span>
                </button>
                <button type="button" class="pm-btn-secondary" style="font-size: 0.82rem; padding: 6px 14px; margin-top: 2px;" onclick="closeQrScannerModal()">
                    <span>Cancelar</span>
                </button>
            </div>
        </div>

        <!-- VISTA B: RESULTADO DEL ESCANEO Y LANZADOR DE BANCOS -->
        <div id="pm-scanner-view-result" style="display: none; padding: 4px 4px 10px 4px;">
            <div style="text-align: center; margin-bottom: 14px;">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                    <span class="material-symbols-rounded" style="font-size: 28px;">check</span>
                </div>
                <h3 style="margin: 0 0 6px 0; font-size: 1.2rem; font-weight: 700;">¡Código QR detectado!</h3>
                <span class="pm-qr-badge-official" style="font-size: 0.78rem;">✓ Datos copiados al portapapeles</span>
            </div>

            <!-- Resumen de los datos detectados -->
            <div id="pm-scanner-parsed-box" style="background: var(--md-sys-color-surface-container-high); border: 1px solid var(--md-sys-color-outline-variant); border-radius: 16px; padding: 12px 14px; margin-bottom: 16px; font-size: 0.88rem;">
                <!-- Rellenado dinámicamente con JS -->
            </div>

            <div style="margin-bottom: 10px;">
                <span style="font-size: 0.84rem; font-weight: 700; color: var(--md-sys-color-on-surface); display: block; margin-bottom: 8px;">
                    ¿Con cuál de tus bancos deseas pagar?
                </span>
                <div id="pm-scanner-bank-shortcuts" style="display: flex; flex-direction: column; gap: 8px;">
                    <!-- Botones de bancos agregados por el usuario -->
                </div>
            </div>

            <div style="display: flex; gap: 8px; justify-content: center; margin-top: 14px;">
                <button type="button" class="pm-btn-secondary" style="font-size: 0.82rem; padding: 8px 14px;" onclick="resetQrScanner()">
                    <span class="material-symbols-rounded" style="font-size: 1rem;">restart_alt</span>
                    <span>Escanear otro</span>
                </button>
                <button type="button" class="pm-btn-secondary" style="font-size: 0.82rem; padding: 8px 14px;" onclick="closeQrScannerModal()">
                    <span>Cerrar</span>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- TOAST NOTIFICACIÓN -->
<div id="pm-toast" class="pm-toast">
    <span class="material-symbols-rounded">check_circle</span>
    <span id="pm-toast-msg">¡Datos copiados al portapapeles!</span>
</div>

<!-- SPEED-DIAL FAB MATERIAL 3: MENÚ DE ACCIONES (AÑADIR BANCO & ESCANEAR QR) -->
<div id="pm-speed-dial" class="pm-speed-dial" style="display: none;">
    <!-- Backdrop sutil para cerrar al tocar fuera -->
    <div id="pm-speed-dial-backdrop" class="pm-speed-dial-backdrop" onclick="togglePmSpeedDial(false)"></div>

    <!-- Opciones desplegables hacia arriba -->
    <div class="pm-speed-dial-options">
        <!-- Opción 1: Escanear QR -->
        <button type="button" class="pm-speed-dial-item pm-speed-dial-item-scan" onclick="handleSpeedDialAction('scan')" title="Escanear QR para pagar">
            <span class="pm-speed-dial-label">Escanear QR</span>
            <div class="pm-speed-dial-btn pm-speed-dial-btn-scan">
                <span class="material-symbols-rounded">qr_code_scanner</span>
            </div>
        </button>

        <!-- Opción 2: Añadir Banco -->
        <button type="button" class="pm-speed-dial-item pm-speed-dial-item-add" onclick="handleSpeedDialAction('add')" title="Añadir nuevo banco">
            <span class="pm-speed-dial-label">Añadir banco</span>
            <div class="pm-speed-dial-btn pm-speed-dial-btn-add">
                <span class="material-symbols-rounded">add</span>
            </div>
        </button>
    </div>

    <!-- Botón Principal Disparador (Menú Hamburguesa) -->
    <button type="button" id="pm-speed-dial-btn" class="pm-fab-main" onclick="togglePmSpeedDial()" title="Menú de acciones" aria-label="Menú de Pago Móvil">
        <span class="material-symbols-rounded pm-fab-icon-menu">menu</span>
        <span class="material-symbols-rounded pm-fab-icon-close">close</span>
    </button>
</div>
