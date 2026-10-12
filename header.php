<header class="app-header">
    <div class="branding-container">
        <img src="public/logo.webp" alt="Cambi Logo" class="logo">
        <h1 class="branding">CAMBI</h1>
    </div>

    <!-- Navegación exclusiva para Desktop -->
    <nav class="desktop-navigation" id="desktop-nav" aria-label="Navegación principal">
        <button type="button" class="desktop-nav-item active" data-tab="dashboard">
            <span class="material-symbols-rounded">dashboard</span>
            <span>Dashboard</span>
        </button>
        <button type="button" class="desktop-nav-item" data-tab="pagomovil">
            <span class="material-symbols-rounded">qr_code_2</span>
            <span>Pago Móvil</span>
        </button>
        <button type="button" class="desktop-nav-item" data-tab="history">
            <span class="material-symbols-rounded">history</span>
            <span>Historial</span>
        </button>
    </nav>

    <div class="header-actions">
        <span class="material-symbols-rounded settings-btn" id="open-settings" title="Ajustes" aria-label="Ajustes">settings</span>
    </div>
</header>

<!-- Pantalla de Ajustes (Overlay) -->
<div id="settings-screen" class="settings-overlay">
    <header>
        <span class="material-symbols-rounded settings-back-btn" id="close-settings">arrow_back</span>
        <h1 class="branding">Ajustes</h1>
    </header>
    <div class="container">
        <div class="settings-item">
            <div class="settings-label">Modo Oscuro</div>
            <label class="md-switch">
                <input type="checkbox" id="dark-mode-toggle">
                <span class="md-slider"></span>
            </label>
        </div>
    </div>
</div>