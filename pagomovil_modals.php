<!-- MODAL 1: CONFIGURAR / EDITAR PERFIL PRINCIPAL -->
<div id="pm-modal-profile" class="pm-modal-overlay">
    <div class="pm-modal-sheet">
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header">
            <h3 id="pm-modal-title">Configurar Pago Móvil</h3>
            <button type="button" class="pm-modal-close" onclick="closePmModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <form id="pm-form-profile" onsubmit="savePmProfile(event)">
            <div class="pm-form-group">
                <label for="pm-input-holder">Titular o Nombre del Negocio</label>
                <input type="text" id="pm-input-holder" placeholder="Ej: Fernando Rodríguez" required maxlength="50" autocomplete="name">
            </div>

            <div class="pm-form-row">
                <div class="pm-form-group" style="flex: 0 0 100px;">
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
        <div class="pm-modal-handle"></div>
        <div class="pm-modal-header" style="justify-content: flex-end;">
            <button type="button" class="pm-modal-close" onclick="closePmQR()">
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
