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

<!-- MODAL 2: AÑADIR OTRO BANCO RÁPIDAMENTE -->
<div id="pm-modal-add-bank" class="pm-modal-overlay">
    <div class="pm-modal-sheet">
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header">
            <h3>Añadir Otro Banco</h3>
            <button type="button" class="pm-modal-close" onclick="closeAddBankModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <p class="pm-modal-subtext">Tu titular, cédula y teléfono se mantendrán iguales. Solo elige el nuevo banco:</p>
        <form id="pm-form-add-bank" onsubmit="saveNewBank(event)">
            <div class="pm-form-group">
                <label for="pm-new-bank-select">Banco a Añadir</label>
                <select id="pm-new-bank-select" class="pm-styled-select" required>
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

<!-- TOAST NOTIFICACIÓN -->
<div id="pm-toast" class="pm-toast">
    <span class="material-symbols-rounded">check_circle</span>
    <span id="pm-toast-msg">¡Datos copiados al portapapeles!</span>
</div>

<!-- BOTÓN FLOTANTE PARA AÑADIR PAGO MÓVIL (FAB) -->
<button id="pm-btn-add-bank-fab" class="pm-fab-add-bank" style="display: none;" title="Añadir Pago Móvil" onclick="openAddBankModal()">
    <span class="material-symbols-rounded">add</span>
</button>
