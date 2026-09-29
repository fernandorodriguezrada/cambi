<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Cambi - Tasa Oficial</title>
<link rel="manifest" href="manifest.json" crossorigin="use-credentials">
<meta name="theme-color" content="#F3EDF7">
<link rel="apple-touch-icon" href="public/512.png">
<link rel="icon" type="image/x-icon" href="public/logo.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Product+Sans:wght@400;700&family=Inter:wght@800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
    * {
        box-sizing: border-box;
        /* Elimina el recuadro azul que aparece al tocar elementos en móviles */
        -webkit-tap-highlight-color: transparent;
    }

    *:focus {
        /* Elimina el borde de enfoque (azul/naranja) que ponen los navegadores por defecto */
        outline: none !important;
    }

    :root {
        /* Tipografía estilo Google Sans / Pixel / Material You */
        --font-google-sans: 'Google Sans', 'Product Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-brand: 'Inter', sans-serif;

        /* Paleta Cambi */
        --cambi-magic-mint: #A3F1CB;
        --cambi-baby-blue: #B1D3FE;
        --cambi-mauve: #DFB8FF;
        --cambi-cotton-candy: #FFB7D3;
        --cambi-dark-electric: #617285;

        /* Colores para mejor contraste en modo claro */
        --cambi-magic-mint-on-surface: #00ccb4; /* Oscuro para #A3F1CB */
        --cambi-baby-blue-on-surface: #1f84e8; /* Oscuro para #B1D3FE */
        --cambi-mauve-on-surface: #9e2abe; /* Oscuro para #DFB8FF */
        --cambi-cotton-candy-on-surface: #f83b7a; /* Oscuro para #FFB7D3 */
        
        --md-sys-color-primary: var(--cambi-dark-electric); /* Sincronizado con el branding en modo claro */
        --md-sys-color-on-primary: #FFFFFF;
        --md-sys-color-surface: #FEF7FF; /* Volvemos al blanco roto Material Design */
        --md-sys-color-on-surface: #313033; /* Gris carbón oscuro en lugar de negro puro */
        --md-sys-color-surface-container: #F3EDF7; /* Volvemos al contenedor de superficie Material Design */
        --md-sys-color-outline: #79747E;
        --md-sys-color-on-surface-variant: #49454F;
        --transition-speed: 0.3s;
        
        /* Colores dinámicos para elementos de marca */
        --branding-color: var(--cambi-dark-electric);

        /* Colores para las tasas, cambian según el tema */
        --rate-usd-color: var(--cambi-magic-mint-on-surface);
        --rate-eur-color: var(--cambi-baby-blue-on-surface);

        /* Colores para los botones de la calculadora */
        --key-default-text: var(--cambi-baby-blue-on-surface);
        --key-mint-bg: var(--cambi-magic-mint);
        --key-mint-text: var(--cambi-magic-mint-on-surface);
        --key-cotton-bg: var(--cambi-cotton-candy);
        --key-cotton-text: var(--cambi-cotton-candy-on-surface);
        --key-mauve-bg: var(--cambi-mauve);
        --key-mauve-text: var(--cambi-mauve-on-surface);
    }

    /* Modo Oscuro basado en Oscuro Eléctrico Azul */
    body.dark-mode {
        --md-sys-color-surface: #262C33; /* Una variante profunda del azul eléctrico */
        --md-sys-color-on-surface: #F4F4F4;
        --md-sys-color-surface-container: #3B454E; /* Tono intermedio */
        --md-sys-color-outline: #617285;
        --md-sys-color-primary: #A3F1CB; /* Magic Mint para acentos en dark mode */
        --md-sys-color-on-primary: #444746; /* Suavizamos el texto del botón en modo oscuro */
        --md-sys-color-on-surface-variant: #B1D3FE; /* Baby Blue para etiquetas secundarias */
        
        /* Ajuste de branding para modo oscuro */
        --branding-color: var(--cambi-magic-mint);

        --rate-usd-color: var(--cambi-magic-mint);
        --rate-eur-color: var(--cambi-baby-blue);

        --key-default-text: var(--md-sys-color-on-surface);
        --key-mint-text: var(--md-sys-color-on-primary);
        --key-cotton-text: var(--md-sys-color-on-primary);
        --key-mauve-text: var(--md-sys-color-on-primary);
    }

    html {
        /* Evita que se vea un color distinto en los bordes de la pantalla */
        background-color: var(--md-sys-color-surface-container);
        transition: background-color var(--transition-speed);
    }

    body {
        margin: 0;
        font-family: var(--font-google-sans);
        background-color: var(--md-sys-color-surface-container);
        color: var(--md-sys-color-on-surface);
        display: flex;
        flex-direction: column;
        height: 100dvh; /* Altura dinámica exacta para evitar huecos */
        width: 100%;
        position: fixed; /* Evita el scroll elástico del sistema que causa líneas */
        overflow: hidden;
        transition: background-color var(--transition-speed), color var(--transition-speed);
        /* Evita que el usuario seleccione texto de la interfaz, lo cual se siente muy "web" */
        user-select: none;
    }

    button, input, select, textarea {
        font-family: inherit;
    }

    header {
        position: relative;
        flex-shrink: 0;
        width: 100%;
        padding-top: env(safe-area-inset-top, 0px);
        height: calc(64px + env(safe-area-inset-top, 0px));
        padding-left: 16px;
        padding-right: 16px;
        background-color: var(--md-sys-color-surface-container);
        border-bottom: none; /* Quitamos el borde inferior que podía aparecer */
        border-left: none;
        border-right: none;
        outline: none;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-sizing: border-box;
        transition: background-color var(--transition-speed);
        transform: translateZ(0); /* Forzamos capa de GPU para evitar parpadeos */
    }
    /* Parche para eliminar la línea de la status bar */
    header::before {
        content: "";
        position: absolute;
        top: -100px; /* Se extiende hacia arriba fuera de la pantalla */
        left: 0;
        right: 0;
        height: 100px;
        background-color: inherit;
        pointer-events: none;
    }

    .branding-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo {
        height: 32px;
        width: auto;
    }

    .branding {
        font-family: var(--font-brand);
        font-weight: 900;
        letter-spacing: -1px;
        color: var(--branding-color);
        margin: 0;
        font-size: 1.5rem;
        transition: color var(--transition-speed);
    }

   .settings-btn {
        cursor: pointer;
        color: var(--branding-color);
        transition: transform var(--transition-speed);
    }

    .settings-btn:hover {
        transform: rotate(45deg);
    }

    /* Pantalla de Ajustes Overlay */
    .settings-overlay {
        position: fixed;
        top: 0;
        left: 100%; /* Empieza fuera de pantalla */
        width: 100%;
        height: 100%;
        background-color: var(--md-sys-color-surface);
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        transition: left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1),
                    opacity var(--transition-speed) ease,
                    visibility var(--transition-speed);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .settings-overlay header {
        position: relative;
        width: 100%;
        padding-top: env(safe-area-inset-top, 0px);
        height: calc(64px + env(safe-area-inset-top, 0px));
        padding-left: 16px;
        padding-right: 16px;
        background-color: var(--md-sys-color-surface-container);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: flex-start; /* Align items to the start */
        box-sizing: border-box;
        transform: translateZ(0);
    }

    .settings-back-btn {
        cursor: pointer;
        color: var(--branding-color); /* Use branding color for consistency */
        margin-right: 12px; /* Space between back button and title */
    }

    .settings-overlay.active {
        left: 0;
        opacity: 1;
        visibility: visible;
    }

    .settings-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background-color: var(--md-sys-color-surface-container);
        border-radius: 16px;
        margin-top: 20px;
        transition: background-color var(--transition-speed);
        width: 100%; /* Asegurar que el item de ajuste ocupe todo el ancho del container */
    }

    /* Material Switch Styles */
    .md-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 32px;
    }

    .md-switch input { opacity: 0; width: 0; height: 0; }

    .md-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: var(--md-sys-color-outline);
        transition: .4s;
        border-radius: 32px;
    }

    .md-slider:before {
        position: absolute;
        content: "";
        height: 24px; width: 24px;
        left: 4px; bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .md-slider { background-color: var(--md-sys-color-primary); }
    input:checked + .md-slider:before { transform: translateX(20px); }

    .container {
        width: 90%;
        max-width: 400px;
        margin: 0 auto;
    }

    .card {
        background-color: var(--md-sys-color-surface-container);
        border-radius: 28px;
        padding: 24px;
        margin-bottom: 16px;
        text-align: center;
        transition: background-color var(--transition-speed);
        width: 100%;
    }

    .rate-display {
        font-family: var(--font-google-sans);
        font-size: 2.85rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        margin: 8px 0;
        transition: color var(--transition-speed); /* Añadir transición para suavidad */
    }

    .input-group {
        width: 100%;
        margin-bottom: 12px;
    }

    .input-wrapper {
        position: relative;
        width: 100%;
    }

    input {
        width: 100%;
        height: 56px; /* Altura estándar Material Design */
        padding: 0 16px 0 52px;
        border: 1px solid var(--md-sys-color-outline);
        border-radius: 12px;
        background: transparent;
        font-size: 1.1rem;
        box-sizing: border-box;
        color: var(--md-sys-color-on-surface);
        transition: border-color 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        user-select: text;
    }

    input.value-zero {
        color: var(--md-sys-color-outline) !important;
    }

    input:focus {
        outline: none;
        border-color: var(--md-sys-color-primary);
        border-width: 2px;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--md-sys-color-primary);
        font-size: 24px;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    input::placeholder {
        color: var(--md-sys-color-outline);
        opacity: 0.7;
    }

    .label {
        font-family: var(--font-google-sans);
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--md-sys-color-on-surface-variant);\n        margin-bottom: 8px;
        display: block;
        text-align: left;
        transition: color var(--transition-speed);
        margin-left: 4px;
    }
    
    #rate { color: var(--rate-usd-color); }
    #rate-eur { color: var(--rate-eur-color); }

    /* Estilos para el nuevo diseño unificado */
    .combined-card {
        background-color: var(--md-sys-color-surface-container);
        border-radius: 20px;
        border: 1px solid var(--md-sys-color-outline);
        overflow: hidden;
        margin-top: 0;
        width: 100%;
    }

    .operation-result {
        display: block;
        font-family: var(--font-google-sans);
        min-height: 26px; /* Altura fija para evitar el salto visual */
        text-align: right;
        padding: 8px 16px 0;
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--cambi-magic-mint);
        opacity: 1;
    }

    .input-row {
        display: flex;
        align-items: center;
        padding: 4px 16px;
        height: 64px;
        cursor: text;
    }

    .currency-prefix {
        font-family: var(--font-google-sans);
        font-weight: 700;
        color: var(--md-sys-color-primary);
        min-width: 45px;
        font-size: 1.2rem;
    }

    .combined-card input {
        font-family: var(--font-google-sans);
        border: none !important;
        height: 100%;
        padding: 0 8px;
        background: transparent;
        flex: 1;
        text-align: right;
        font-size: 1.8rem;
        font-weight: 500;
        caret-color: transparent; /* Ocultamos el cursor nativo */
        cursor: text;
    }

    #input-bottom:not(.value-zero) {
        color: var(--cambi-magic-mint);
    }

    .input-divider {
        height: 1px;
        background-color: var(--md-sys-color-outline);
        opacity: 0.2;
        margin: 0 16px;
    }

    /* Estilos del Teclado Numérico */
    .keypad {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin: 20px auto 0;
        width: 100%;
        max-width: 320px;
    }

    .keypad-btn {
        font-family: var(--font-google-sans);
        aspect-ratio: 1 / 1;
        height: auto;
        border-radius: 16px;
        border: none;
        background-color: var(--md-sys-color-surface-container);
        color: var(--key-default-text);
        font-size: 1.25rem;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.1s, transform 0.1s;
    }

    .keypad-btn.key-zero {
        grid-column: span 2;
        aspect-ratio: auto;
        height: 100%;
    }

    .keypad-btn:active {
        background-color: var(--md-sys-color-outline);
        transform: scale(0.95);
    }

    .keypad-btn.operator {
        background-color: var(--key-mauve-bg);
        color: var(--key-mauve-text);
        font-size: 1.55rem;
        font-weight: 600;
    }

    .keypad-btn.key-mint[data-key="="] {
        font-size: 1.55rem;
        font-weight: 600;
    }

    .keypad-btn .material-symbols-rounded {
        font-size: 1.5rem;
    }

    .keypad-btn.action {
        background-color: var(--cambi-dark-electric);
        color: white;
    }

    .keypad-btn.key-cotton {
        background-color: var(--key-cotton-bg);
        color: var(--key-cotton-text);
        font-size: 1.35rem;
        font-weight: 600;
    }

    .keypad-btn.key-mint {
        background-color: var(--key-mint-bg);
        color: var(--key-mint-text);
    }

    body.dark-mode .keypad-btn.action { background-color: var(--cambi-dark-electric); color: white; }

    .refresh-btn {
        font-family: var(--font-google-sans);
        background-color: var(--md-sys-color-primary);
        color: var(--md-sys-color-on-primary);
        border: none;
        padding: 12px 24px;
        border-radius: 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 0 auto;
        cursor: pointer;
    }

    .footer-info {
        font-family: var(--font-google-sans);
        margin-top: 12px;
        font-size: 0.72rem;
        font-weight: 500;
        letter-spacing: 0.01em;
        color: var(--md-sys-color-outline);
    }

    /* Main content area to push content above bottom nav */
    .main-content {
        flex-grow: 1;
        width: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow-y: auto; /* Solo el contenido tiene scroll */
        -webkit-overflow-scrolling: touch;
        background-color: var(--md-sys-color-surface);
        /* Ajuste dinámico del padding inferior basado en la nueva altura de la nav */
        padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px) + 16px);
    }

    /* Tab Content Styling */
    .tab-content {
        display: flex;
        width: 100%;
        flex-grow: 1;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding-top: 16px;
        
        /* Material You Transition Properties */
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0;
        visibility: hidden;
        transform: scale(0.96);
        transition: 
            opacity 0.3s cubic-bezier(0.2, 0, 0, 1), 
            transform 0.3s cubic-bezier(0.2, 0, 0, 1);
        pointer-events: none;
        z-index: 0;
    }
    .tab-content.active {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
        position: relative;
        pointer-events: auto;
        z-index: 1;
    }

    /* Centrado vertical equilibrado para la pestaña de calculadora */
    #calculator-tab-content.active {
        justify-content: center;
        padding-top: 0;
    }

    /* Pestaña Pago Móvil: aprovecha toda la altura disponible y permite desplazamiento natural */
    #pagomovil-tab-content.active {
        width: 100%;
        min-height: 100%;
        padding-top: 14px;
        box-sizing: border-box;
    }

    /* Bottom Navigation Bar */
    .bottom-navigation {
        position: fixed;
        bottom: 0;
        /* Forzamos solapamiento lateral de 1px para sellar micro-huecos en las paredes */
        left: -1px;
        right: -1px;
        width: auto; 
        margin: 0;
        /* Altura estándar M3 (80px) + área segura inferior del sistema */
        height: calc(80px + env(safe-area-inset-bottom, 0px));
        padding-bottom: env(safe-area-inset-bottom, 0px);
        background-color: var(--md-sys-color-surface-container);
        border: none;
        box-shadow: 0 -1px 3px rgba(0,0,0,0.05); /* Sombra más sutil y pegada */
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 100;
        transition: background-color var(--transition-speed);
        transform: translateZ(0); /* Forzar renderizado de capa limpia */
    }

    /* SELLO HERMÉTICO INFERIOR: El color "sangra" 100px hacia abajo */
    .bottom-navigation::after {
        content: "";
        position: absolute;
        bottom: -100px;
        left: 0;
        right: 0;
        height: 100px;
        background-color: inherit;
        z-index: -1;
    }

    .nav-item {
        font-family: var(--font-google-sans);
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: none;
        border: none;
        color: var(--md-sys-color-on-surface-variant); /* Color inactivo */
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0;
        cursor: pointer;
        transition: color 0.3s cubic-bezier(0.2, 0, 0, 1);
    }

    .nav-icon-container {
        position: relative;
        width: 64px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
    }

    .nav-indicator {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: var(--md-sys-color-primary);
        border-radius: 16px;
        opacity: 0;
        transform: scaleX(0.5);
        transition: opacity 0.3s cubic-bezier(0.2, 0, 0, 1), transform 0.3s cubic-bezier(0.2, 0, 0, 1);
        z-index: -1;
    }

    .nav-item.active .nav-indicator {
        opacity: 1;
        transform: scaleX(1);
    }

    .nav-item.active {
        color: var(--md-sys-color-on-surface);
    }

    .nav-item .material-symbols-rounded {
        font-size: 24px;
        transition: font-variation-settings 0.3s cubic-bezier(0.2, 0, 0, 1), transform 0.3s cubic-bezier(0.2, 0, 0, 1), color 0.3s cubic-bezier(0.2, 0, 0, 1);
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .nav-item.active .material-symbols-rounded {
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        transform: scale(1.05);
        color: var(--md-sys-color-on-primary);
    }

    /* Splash Screen Styles */
    .splash-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: var(--md-sys-color-surface); /* Sincronizar con el color del header/body */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease, visibility 0.5s;
    }

    .splash-screen.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .splash-content {
        text-align: center;
        width: 60%;
        max-width: 250px;
    }

    .splash-logo {
        height: 80px;
        width: auto;
        margin-bottom: 32px;
        animation: splash-pulse 2s infinite ease-in-out;
    }

    @keyframes splash-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
    }

    .progress-container {
        width: 100%;
        height: 4px;
        background-color: var(--md-sys-color-surface-container);
        border-radius: 2px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        width: 0%;
        background-color: var(--cambi-magic-mint);
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom Caret (Cursor) */
    .input-row {
        position: relative; /* Necesario para posicionar el cursor */
    }

    .custom-caret {
        position: absolute;
        width: 2px;
        height: 24px;
        background-color: var(--cambi-magic-mint);
        pointer-events: none;
        display: none;
        z-index: 5;
        transition: opacity 0.2s;
        animation: caret-pulsate 1s infinite;
    }

    @keyframes caret-pulsate {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }

    /* Medidor invisible para calcular posición del texto */
    #text-measurer {
        position: absolute;
        visibility: hidden;
        white-space: pre;
        font-family: var(--font-google-sans);
        font-size: 1.8rem;
        font-weight: 500;
    }

    /* Estilos para el Historial */
    .history-item {
        font-family: var(--font-google-sans);
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        padding: 14px 0;
        border-bottom: 1px solid var(--md-sys-color-surface-container);
        font-size: 0.95rem;
    }
    .history-item:last-child { border-bottom: none; }
    .history-item span:first-child { font-weight: 500; color: var(--md-sys-color-outline); }
    .history-item .usd-val { color: var(--rate-usd-color); text-align: right; font-weight: 700; }
    .history-item .eur-val { color: var(--rate-eur-color); text-align: right; font-weight: 700; }
    .history-header {
        font-family: var(--font-google-sans);
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        padding-bottom: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--md-sys-color-outline);
        border-bottom: 2px solid var(--md-sys-color-surface-container);
    }

    /* =========================================
       ESTILOS PESTAÑA PAGO MÓVIL
       ========================================= */
    .pm-container {
        display: flex;
        flex-direction: column;
        width: 100% !important;
        max-width: 440px !important;
        margin: 0 auto;
        height: auto;
        min-height: 100%;
        flex: 1;
        padding-top: 0;
        padding-left: 14px;
        padding-right: 14px;
        padding-bottom: 76px;
        box-sizing: border-box;
    }

    .pm-view {
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: opacity 0.3s cubic-bezier(0.2, 0, 0, 1);
        box-sizing: border-box;
    }

    /* Onboarding / Tour Card que abarca toda la pantalla disponible (sin fondo) */
    .pm-tour-card {
        background-color: transparent;
        border-radius: 0;
        padding: 16px 4px 8px;
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        box-shadow: none;
        border: none;
        box-sizing: border-box;
    }

    .pm-carousel-wrapper {
        width: 100%;
        flex: 1;
        display: flex;
        align-items: center;
        overflow: hidden;
        position: relative;
    }

    .pm-carousel {
        display: flex;
        transition: transform 0.4s cubic-bezier(0.2, 0, 0, 1);
        width: 100%;
        height: 100%;
        align-items: center;
    }

    .pm-slide {
        min-width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 8px 12px;
        box-sizing: border-box;
    }

    .pm-slide-icon-wrap {
        width: 88px;
        height: 88px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        transition: background-color 0.2s, color 0.2s;
    }

    .pm-slide-icon-wrap.icon-mint {
        background-color: rgba(163, 241, 203, 0.45);
        color: #005a36;
    }

    .pm-slide-icon-wrap.icon-blue {
        background-color: rgba(177, 211, 254, 0.45);
        color: #0b57d0;
    }

    .pm-slide-icon-wrap.icon-mauve {
        background-color: rgba(223, 184, 255, 0.5);
        color: #6b21a8;
    }

    body.dark-mode .pm-slide-icon-wrap.icon-mint {
        background-color: rgba(163, 241, 203, 0.16);
        color: #A3F1CB;
    }

    body.dark-mode .pm-slide-icon-wrap.icon-blue {
        background-color: rgba(177, 211, 254, 0.16);
        color: #B1D3FE;
    }

    body.dark-mode .pm-slide-icon-wrap.icon-mauve {
        background-color: rgba(223, 184, 255, 0.18);
        color: #DFB8FF;
    }

    .pm-slide-icon-wrap .material-symbols-rounded {
        font-size: 46px;
    }

    .pm-slide-title {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        margin: 0 0 10px 0;
        color: var(--md-sys-color-on-surface);
    }

    .pm-slide-desc {
        font-size: 1rem;
        line-height: 1.5;
        color: var(--md-sys-color-on-surface-variant);
        margin: 0;
        max-width: 300px;
    }

    .pm-indicators {
        display: flex;
        gap: 10px;
        justify-content: center;
        align-items: center;
        margin: 16px 0 20px;
    }

    .pm-dot {
        width: 8px;
        height: 8px;
        border-radius: 4px;
        background-color: var(--md-sys-color-outline);
        opacity: 0.35;
        transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
        cursor: pointer;
    }

    .pm-dot.active {
        width: 30px;
        background-color: var(--cambi-magic-mint-on-surface);
        opacity: 1;
    }

    body.dark-mode .pm-dot.active {
        background-color: var(--cambi-magic-mint);
    }

    /* Botón Principal */
    .pm-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 16px 20px;
        border-radius: 22px;
        border: none;
        background-color: var(--cambi-magic-mint);
        color: #004d34;
        font-family: var(--font-google-sans);
        font-size: 1.02rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.15s, filter 0.15s;
    }

    .pm-btn-primary:active {
        transform: scale(0.98);
        filter: brightness(0.96);
    }

    .pm-btn-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 14px 20px;
        border-radius: 20px;
        border: none;
        background-color: var(--md-sys-color-surface-container);
        color: var(--md-sys-color-on-surface);
        font-family: var(--font-google-sans);
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: filter 0.15s;
    }

    /* Header del perfil activo */
    .pm-profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--md-sys-color-surface-container);
        border-radius: 24px;
        padding: 16px 20px;
        margin-top: 6px;
        margin-bottom: 16px;
        width: 100%;
        box-sizing: border-box;
    }

    .pm-user-meta {
        display: flex;
        flex-direction: column;
    }

    .pm-user-tag {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--md-sys-color-outline);
    }

    .pm-user-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--md-sys-color-on-surface);
        margin: 2px 0 0 0;
    }

    .pm-user-doc {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--md-sys-color-outline);
    }

    .pm-icon-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: none;
        background: rgba(0, 0, 0, 0.04);
        color: var(--md-sys-color-on-surface);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.15s;
    }

    body.dark-mode .pm-icon-btn {
        background: rgba(255, 255, 255, 0.08);
    }

    .pm-icon-btn:active {
        transform: scale(0.92);
    }

    /* Submeta del perfil */
    .pm-user-submeta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.86rem;
        color: var(--md-sys-color-outline);
        font-weight: 500;
        margin-top: 3px;
    }

    .pm-meta-dot {
        opacity: 0.5;
    }

    /* Sección de Bloques de Bancos */
    .pm-banks-blocks-section {
        width: 100%;
        display: flex;
        flex-direction: column;
        flex: 1;
        padding-bottom: 72px;
    }

    .pm-blocks-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-left: 2px;
    }

    .pm-section-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--md-sys-color-outline);
    }

    .pm-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        border-radius: 11px;
        background-color: var(--md-sys-color-surface-container-high);
        color: var(--md-sys-color-on-surface);
        font-size: 0.76rem;
        font-weight: 700;
    }

    /* Grid de Bloques de Bancos */
    .pm-banks-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 24px;
        width: 100%;
        box-sizing: border-box;
    }

    @media (max-width: 320px) {
        .pm-banks-grid {
            grid-template-columns: 1fr;
        }
    }

    .pm-bank-block {
        background-color: var(--b-pastel-bg, var(--md-sys-color-surface-container));
        color: var(--b-pastel-text, var(--md-sys-color-on-surface));
        border: none !important;
        border-radius: 18px;
        padding: 12px 10px;
        min-height: 102px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        box-sizing: border-box;
        box-shadow: none !important;
        transition: transform 0.16s cubic-bezier(0.2, 0, 0, 1), opacity 0.16s;
        user-select: none;
        -webkit-user-select: none;
        min-width: 0;
        width: 100%;
        overflow: hidden;
    }

    .pm-bank-block:active {
        transform: scale(0.97);
        opacity: 0.9;
    }

    .pm-block-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 4px;
        min-width: 0;
        width: 100%;
    }

    .pm-block-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        flex: 1;
        overflow: hidden;
    }

    .pm-block-logo {
        width: 66px;
        height: 28px;
        padding: 3px 5px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-sizing: border-box;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    body.dark-mode .pm-block-logo {
        background-color: rgba(255, 255, 255, 0.94);
    }

    .pm-block-logo img {
        width: 100%;
        height: 100%;
        max-height: 20px;
        max-width: 56px;
        object-fit: contain;
        display: block;
        margin: auto;
    }

    .pm-block-logo .material-symbols-rounded {
        font-size: 18px;
        color: #1a1a1a;
    }

    .pm-block-code {
        font-family: monospace;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--b-pastel-text, var(--md-sys-color-on-surface));
        background-color: var(--b-pastel-pill, rgba(0, 0, 0, 0.08));
        padding: 2px 6px;
        border-radius: 8px;
        letter-spacing: -0.01em;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .pm-block-chevron {
        font-size: 18px;
        color: var(--b-pastel-text, var(--md-sys-color-on-surface));
        opacity: 0.45;
        flex-shrink: 0;
    }

    .pm-block-bottom {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-top: 8px;
        min-width: 0;
        width: 100%;
    }

    .pm-block-name {
        margin: 0;
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--b-pastel-text, var(--md-sys-color-on-surface));
        letter-spacing: -0.01em;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pm-block-sub {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--b-pastel-sub, var(--md-sys-color-outline));
        letter-spacing: 0.02em;
        opacity: 0.85;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* =========================================
       PALETA PASTEL DE TODOS LOS BANCOS (CERO GLOW)
       ========================================= */

    /* 0102 - Banco de Venezuela (Azul Pastel Institucional) */
    .pm-bank-pastel-0102 {
        --b-pastel-bg: #dbe8f9;
        --b-pastel-text: #0c2d54;
        --b-pastel-sub: #274f7b;
        --b-pastel-pill: rgba(12, 45, 84, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0102 {
        --b-pastel-bg: #14253a;
        --b-pastel-text: #a9caf4;
        --b-pastel-sub: #75a2d8;
        --b-pastel-pill: rgba(169, 202, 244, 0.15);
    }

    /* 0104 - Venezolano de Crédito (Verde Pino Suave) */
    .pm-bank-pastel-0104 {
        --b-pastel-bg: #d7ede5;
        --b-pastel-text: #0d4a36;
        --b-pastel-sub: #246b52;
        --b-pastel-pill: rgba(13, 74, 54, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0104 {
        --b-pastel-bg: #112d24;
        --b-pastel-text: #9fe2cb;
        --b-pastel-sub: #6cc7a7;
        --b-pastel-pill: rgba(159, 226, 203, 0.15);
    }

    /* 0105 - Mercantil (Azul Marino Pastel) */
    .pm-bank-pastel-0105 {
        --b-pastel-bg: #d7e7fa;
        --b-pastel-text: #002d62;
        --b-pastel-sub: #164d8a;
        --b-pastel-pill: rgba(0, 45, 98, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0105 {
        --b-pastel-bg: #13263e;
        --b-pastel-text: #a6cbf7;
        --b-pastel-sub: #74a6e2;
        --b-pastel-pill: rgba(166, 203, 247, 0.15);
    }

    /* 0108 - BBVA Banco Provincial (Azul BBVA Pastel) */
    .pm-bank-pastel-0108 {
        --b-pastel-bg: #d7e5f8;
        --b-pastel-text: #003366;
        --b-pastel-sub: #1b538e;
        --b-pastel-pill: rgba(0, 51, 102, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0108 {
        --b-pastel-bg: #13253b;
        --b-pastel-text: #a3c6f5;
        --b-pastel-sub: #6fa0de;
        --b-pastel-pill: rgba(163, 198, 245, 0.15);
    }

    /* 0114 - Bancaribe (Turquesa / Celeste Pastel) */
    .pm-bank-pastel-0114 {
        --b-pastel-bg: #daf3f2;
        --b-pastel-text: #0a4d4b;
        --b-pastel-sub: #1e6d6b;
        --b-pastel-pill: rgba(10, 77, 75, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0114 {
        --b-pastel-bg: #123232;
        --b-pastel-text: #96e7e4;
        --b-pastel-sub: #60c5c2;
        --b-pastel-pill: rgba(150, 231, 228, 0.15);
    }

    /* 0115 - Banco Exterior (Celeste Suave Pastel) */
    .pm-bank-pastel-0115 {
        --b-pastel-bg: #daedf8;
        --b-pastel-text: #0e3d54;
        --b-pastel-sub: #215c7a;
        --b-pastel-pill: rgba(14, 61, 84, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0115 {
        --b-pastel-bg: #112c3a;
        --b-pastel-text: #8dceee;
        --b-pastel-sub: #5bafd8;
        --b-pastel-pill: rgba(141, 206, 238, 0.15);
    }

    /* 0128 - Banco Caroní (Azul Océano Suave) */
    .pm-bank-pastel-0128 {
        --b-pastel-bg: #d8f1f7;
        --b-pastel-text: #0b4554;
        --b-pastel-sub: #226475;
        --b-pastel-pill: rgba(11, 69, 84, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0128 {
        --b-pastel-bg: #102e38;
        --b-pastel-text: #8fe2f3;
        --b-pastel-sub: #58bfd5;
        --b-pastel-pill: rgba(143, 226, 243, 0.15);
    }

    /* 0134 - Banesco (Verde Pastel) */
    .pm-bank-pastel-0134 {
        --b-pastel-bg: #d5f2e1;
        --b-pastel-text: #064d2b;
        --b-pastel-sub: #176d41;
        --b-pastel-pill: rgba(6, 77, 43, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0134 {
        --b-pastel-bg: #153322;
        --b-pastel-text: #96e5b8;
        --b-pastel-sub: #67c590;
        --b-pastel-pill: rgba(150, 229, 184, 0.15);
    }

    /* 0137 - Banco Sofitasa (Rojo Teja Suave) */
    .pm-bank-pastel-0137 {
        --b-pastel-bg: #fce2de;
        --b-pastel-text: #5e1c15;
        --b-pastel-sub: #852f26;
        --b-pastel-pill: rgba(94, 28, 21, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0137 {
        --b-pastel-bg: #351714;
        --b-pastel-text: #fca79d;
        --b-pastel-sub: #db766b;
        --b-pastel-pill: rgba(252, 167, 157, 0.15);
    }

    /* 0138 - Banco Plaza (Lavanda Índigo Suave) */
    .pm-bank-pastel-0138 {
        --b-pastel-bg: #e6e3f8;
        --b-pastel-text: #2f2566;
        --b-pastel-sub: #4d408c;
        --b-pastel-pill: rgba(47, 37, 102, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0138 {
        --b-pastel-bg: #1e1939;
        --b-pastel-text: #beb5f5;
        --b-pastel-sub: #9283e5;
        --b-pastel-pill: rgba(190, 181, 245, 0.15);
    }

    /* 0146 - Bangente (Menta Lima Suave) */
    .pm-bank-pastel-0146 {
        --b-pastel-bg: #e5f5dd;
        --b-pastel-text: #225213;
        --b-pastel-sub: #3d722c;
        --b-pastel-pill: rgba(34, 82, 19, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0146 {
        --b-pastel-bg: #193312;
        --b-pastel-text: #ade89a;
        --b-pastel-sub: #7bc762;
        --b-pastel-pill: rgba(173, 232, 154, 0.15);
    }

    /* 0151 - BFC Banco Fondo Común (Azul Acero Suave) */
    .pm-bank-pastel-0151 {
        --b-pastel-bg: #dceaf6;
        --b-pastel-text: #0d385c;
        --b-pastel-sub: #275680;
        --b-pastel-pill: rgba(13, 56, 92, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0151 {
        --b-pastel-bg: #13273a;
        --b-pastel-text: #a0cbf1;
        --b-pastel-sub: #6aa1d4;
        --b-pastel-pill: rgba(160, 203, 241, 0.15);
    }

    /* 0156 - 100% Banco (Celeste Suave) */
    .pm-bank-pastel-0156 {
        --b-pastel-bg: #daf0fb;
        --b-pastel-text: #0d435e;
        --b-pastel-sub: #276282;
        --b-pastel-pill: rgba(13, 67, 94, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0156 {
        --b-pastel-bg: #122c3b;
        --b-pastel-text: #96d8fb;
        --b-pastel-sub: #60b3df;
        --b-pastel-pill: rgba(150, 216, 251, 0.15);
    }

    /* 0157 - DelSur Banco Universal (Turquesa Suave) */
    .pm-bank-pastel-0157 {
        --b-pastel-bg: #d6f3ef;
        --b-pastel-text: #0a4f47;
        --b-pastel-sub: #217066;
        --b-pastel-pill: rgba(10, 79, 71, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0157 {
        --b-pastel-bg: #11322e;
        --b-pastel-text: #93e8de;
        --b-pastel-sub: #5dc3b6;
        --b-pastel-pill: rgba(147, 232, 222, 0.15);
    }

    /* 0163 - Banco del Tesoro (Ámbar Dorado Suave) */
    .pm-bank-pastel-0163 {
        --b-pastel-bg: #fbedcd;
        --b-pastel-text: #663d00;
        --b-pastel-sub: #8c570d;
        --b-pastel-pill: rgba(102, 61, 0, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0163 {
        --b-pastel-bg: #35240f;
        --b-pastel-text: #f8c876;
        --b-pastel-sub: #d8a246;
        --b-pastel-pill: rgba(248, 200, 118, 0.15);
    }

    /* 0166 - Banco Agrícola de Venezuela (Verde Campo / Oliva Suave) */
    .pm-bank-pastel-0166 {
        --b-pastel-bg: #e2f0d9;
        --b-pastel-text: #2c5218;
        --b-pastel-sub: #46702c;
        --b-pastel-pill: rgba(44, 82, 24, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0166 {
        --b-pastel-bg: #1b3012;
        --b-pastel-text: #aee096;
        --b-pastel-sub: #7ebc60;
        --b-pastel-pill: rgba(174, 224, 150, 0.15);
    }

    /* 0168 - Bancrecer (Malva Suave) */
    .pm-bank-pastel-0168 {
        --b-pastel-bg: #eedcf5;
        --b-pastel-text: #48165e;
        --b-pastel-sub: #6c298c;
        --b-pastel-pill: rgba(72, 22, 94, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0168 {
        --b-pastel-bg: #2c1635;
        --b-pastel-text: #d6a6eb;
        --b-pastel-sub: #b074cb;
        --b-pastel-pill: rgba(214, 166, 235, 0.15);
    }

    /* 0169 - Mi Banco (Naranja Cálido Suave) */
    .pm-bank-pastel-0169 {
        --b-pastel-bg: #fde8d7;
        --b-pastel-text: #662f00;
        --b-pastel-sub: #8c460d;
        --b-pastel-pill: rgba(102, 47, 0, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0169 {
        --b-pastel-bg: #351c0d;
        --b-pastel-text: #f7b483;
        --b-pastel-sub: #d78b54;
        --b-pastel-pill: rgba(247, 180, 131, 0.15);
    }

    /* 0171 - Banco Activo (Ámbar Naranja Suave) */
    .pm-bank-pastel-0171 {
        --b-pastel-bg: #fef0db;
        --b-pastel-text: #664100;
        --b-pastel-sub: #8c5d0d;
        --b-pastel-pill: rgba(102, 65, 0, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0171 {
        --b-pastel-bg: #35260f;
        --b-pastel-text: #f9cb79;
        --b-pastel-sub: #d8a74b;
        --b-pastel-pill: rgba(249, 203, 121, 0.15);
    }

    /* 0172 - Bancamiga (Menta / Verde Claro Suave) */
    .pm-bank-pastel-0172 {
        --b-pastel-bg: #cef4ed;
        --b-pastel-text: #004d43;
        --b-pastel-sub: #136f62;
        --b-pastel-pill: rgba(0, 77, 67, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0172 {
        --b-pastel-bg: #11322d;
        --b-pastel-text: #8be5d5;
        --b-pastel-sub: #56c3b0;
        --b-pastel-pill: rgba(139, 229, 213, 0.15);
    }

    /* 0174 - Banplus (Lavanda Suave) */
    .pm-bank-pastel-0174 {
        --b-pastel-bg: #dce3f8;
        --b-pastel-text: #1c306d;
        --b-pastel-sub: #344d93;
        --b-pastel-pill: rgba(28, 48, 109, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0174 {
        --b-pastel-bg: #17213b;
        --b-pastel-text: #a6b7ee;
        --b-pastel-sub: #7b91d8;
        --b-pastel-pill: rgba(166, 183, 238, 0.15);
    }

    /* 0175 - Banco Bicentenario / BDT (Rosa Coral Suave) */
    .pm-bank-pastel-0175 {
        --b-pastel-bg: #fcdede;
        --b-pastel-text: #6b1418;
        --b-pastel-sub: #92262c;
        --b-pastel-pill: rgba(107, 20, 24, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0175 {
        --b-pastel-bg: #371618;
        --b-pastel-text: #f5a0a4;
        --b-pastel-sub: #d66f74;
        --b-pastel-pill: rgba(245, 160, 164, 0.15);
    }

    /* 0177 - BANFANB (Oliva Militar Suave) */
    .pm-bank-pastel-0177 {
        --b-pastel-bg: #e6edd8;
        --b-pastel-text: #324718;
        --b-pastel-sub: #4c632e;
        --b-pastel-pill: rgba(50, 71, 24, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0177 {
        --b-pastel-bg: #202b13;
        --b-pastel-text: #b6d98c;
        --b-pastel-sub: #86b153;
        --b-pastel-pill: rgba(182, 217, 140, 0.15);
    }

    /* 0191 - BNC (Verde Esmeralda Suave) */
    .pm-bank-pastel-0191 {
        --b-pastel-bg: #d4eed8;
        --b-pastel-text: #0e4d20;
        --b-pastel-sub: #206d36;
        --b-pastel-pill: rgba(14, 77, 32, 0.1);
    }
    body.dark-mode .pm-bank-pastel-0191 {
        --b-pastel-bg: #14301a;
        --b-pastel-text: #96e0a8;
        --b-pastel-sub: #63ba7a;
        --b-pastel-pill: rgba(150, 224, 168, 0.15);
    }

    /* Default (Otros bancos) */
    .pm-bank-pastel-default {
        --b-pastel-bg: var(--md-sys-color-surface-container);
        --b-pastel-text: var(--md-sys-color-on-surface);
        --b-pastel-sub: var(--md-sys-color-outline);
        --b-pastel-pill: rgba(0, 0, 0, 0.08);
    }
    body.dark-mode .pm-bank-pastel-default {
        --b-pastel-pill: rgba(255, 255, 255, 0.1);
    }

    /* Modal Detalle de Banco Seleccionado */
    .pm-detail-bank-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 14px 6px 8px;
        border-radius: 18px;
        background-color: var(--b-pastel-bg, var(--md-sys-color-surface-container));
        color: var(--b-pastel-text, var(--md-sys-color-on-surface));
    }

    .pm-detail-logo {
        width: 104px;
        height: 38px;
        padding: 4px 8px;
        background-color: rgba(255, 255, 255, 0.92);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-sizing: border-box;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    }

    body.dark-mode .pm-detail-logo {
        background-color: rgba(255, 255, 255, 0.96);
    }

    .pm-detail-logo img {
        width: 100%;
        height: 100%;
        max-height: 26px;
        max-width: 88px;
        object-fit: contain;
        display: block;
        margin: auto;
    }

    .pm-detail-logo .material-symbols-rounded {
        font-size: 24px;
        color: #1a1a1a;
    }

    .pm-detail-title {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 700;
        color: var(--b-pastel-text, var(--md-sys-color-on-surface));
        line-height: 1.2;
    }

    .pm-detail-code {
        font-family: monospace;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--b-pastel-sub, var(--md-sys-color-outline));
    }

    /* Material 3 Speed Dial Floating Action Menu */
    .pm-speed-dial {
        position: fixed;
        bottom: calc(108px + env(safe-area-inset-bottom, 0px));
        right: max(20px, calc(50% - 200px + 20px));
        z-index: 105;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1), opacity 0.24s cubic-bezier(0.2, 0, 0, 1);
    }

    /* Ocultar en scroll abajo */
    .pm-speed-dial.fab-hidden {
        transform: translateY(80px) scale(0.7);
        opacity: 0;
        pointer-events: none;
    }

    /* Backdrop sutil cuando el menú está abierto */
    .pm-speed-dial-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.22s ease;
        z-index: 104;
    }
    .pm-speed-dial.open .pm-speed-dial-backdrop {
        opacity: 1;
        pointer-events: auto;
    }

    /* Botón disparador principal (FAB Hamburguesa) */
    .pm-fab-main {
        position: relative;
        width: 62px;
        height: 62px;
        border-radius: 20px;
        background-color: var(--cambi-magic-mint);
        color: #004d34;
        border: none !important;
        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.22) !important;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 106;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease, box-shadow 0.2s ease;
        -webkit-tap-highlight-color: transparent;
    }
    body.dark-mode .pm-fab-main {
        background-color: var(--cambi-magic-mint);
        color: #003825;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.45) !important;
    }
    .pm-fab-main:hover {
        transform: scale(1.06);
    }
    .pm-fab-main:active {
        transform: scale(0.94);
    }

    /* Iconos del botón principal con transición de rotación y fundido M3 */
    .pm-fab-main .pm-fab-icon-menu,
    .pm-fab-main .pm-fab-icon-close {
        position: absolute;
        font-size: 30px;
        font-weight: 600;
        transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1), opacity 0.2s cubic-bezier(0.2, 0, 0, 1);
    }
    .pm-fab-main .pm-fab-icon-close {
        opacity: 0;
        transform: rotate(-90deg) scale(0.6);
    }
    .pm-speed-dial.open .pm-fab-main {
        background-color: var(--md-sys-color-surface-container-highest, #e6e0e9);
        color: var(--md-sys-color-on-surface, #1d1b20);
        transform: scale(1.02);
    }
    body.dark-mode .pm-speed-dial.open .pm-fab-main {
        background-color: #3b434d;
        color: #ffffff;
    }
    .pm-speed-dial.open .pm-fab-main .pm-fab-icon-menu {
        opacity: 0;
        transform: rotate(90deg) scale(0.6);
    }
    .pm-speed-dial.open .pm-fab-main .pm-fab-icon-close {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    /* Contenedor de opciones (hacia arriba) */
    .pm-speed-dial-options {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
        margin-bottom: 14px;
        pointer-events: none;
        z-index: 106;
    }

    /* Ítem de opción (etiqueta + botón) */
    .pm-speed-dial-item {
        display: flex;
        align-items: center;
        gap: 12px;
        background: transparent;
        border: none;
        padding: 0;
        cursor: pointer;
        opacity: 0;
        transform: translateY(18px) scale(0.85);
        transition: transform 0.24s cubic-bezier(0.2, 0, 0, 1), opacity 0.2s cubic-bezier(0.2, 0, 0, 1);
        -webkit-tap-highlight-color: transparent;
    }
    .pm-speed-dial.open .pm-speed-dial-item {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Animación escalonada (Stagger effect) */
    .pm-speed-dial.open .pm-speed-dial-item-add {
        transition-delay: 0.03s;
    }
    .pm-speed-dial.open .pm-speed-dial-item-scan {
        transition-delay: 0.07s;
    }

    /* Etiquetas flotantes tipo Chip Material 3 */
    .pm-speed-dial-label {
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 12px;
        background: var(--md-sys-color-surface-container-high);
        color: var(--md-sys-color-on-surface);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        white-space: nowrap;
        pointer-events: none;
    }
    body.dark-mode .pm-speed-dial-label {
        background: #2b323b;
        color: #e5e9f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
    }

    /* Botones de las opciones */
    .pm-speed-dial-btn {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
        transition: transform 0.15s ease;
    }
    .pm-speed-dial-item:hover .pm-speed-dial-btn {
        transform: scale(1.08);
    }
    .pm-speed-dial-item:active .pm-speed-dial-btn {
        transform: scale(0.92);
    }

    /* Colores pastel de cada opción */
    .pm-speed-dial-btn-add {
        background-color: var(--cambi-magic-mint);
        color: #004d34;
    }
    body.dark-mode .pm-speed-dial-btn-add {
        background-color: var(--cambi-magic-mint);
        color: #003825;
    }
    .pm-speed-dial-btn-scan {
        background-color: var(--cambi-baby-blue);
        color: #0a3a60;
    }
    body.dark-mode .pm-speed-dial-btn-scan {
        background-color: var(--cambi-baby-blue);
        color: #052640;
    }
    .pm-speed-dial-btn .material-symbols-rounded {
        font-size: 26px;
        font-weight: 600;
    }


    .pm-detail-fields {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 14px;
        margin-bottom: 16px;
    }

    .pm-detail-field-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-radius: 16px;
        background-color: var(--md-sys-color-surface-container);
        border: 1px solid rgba(0, 0, 0, 0.04);
        box-sizing: border-box;
    }

    body.dark-mode .pm-detail-field-box {
        background-color: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .pm-detail-field-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .pm-detail-field-label {
        font-size: 0.74rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--md-sys-color-outline);
    }

    .pm-detail-field-val {
        font-size: 1.02rem;
        font-weight: 700;
        color: var(--md-sys-color-on-surface);
        letter-spacing: 0.01em;
    }

    .pm-detail-copy-single {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background-color: var(--md-sys-color-surface-container-high);
        color: var(--md-sys-color-on-surface);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.15s, background-color 0.2s;
    }

    .pm-detail-copy-single:active {
        transform: scale(0.9);
        background-color: var(--cambi-magic-mint);
        color: #004d34;
    }

    .pm-detail-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
        margin-bottom: 0;
    }

    .pm-detail-actions .pm-btn-primary,
    .pm-detail-actions .pm-btn-secondary {
        flex: 1;
        height: 48px;
        border-radius: 16px;
        font-size: 0.92rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: none !important;
        border: none !important;
        cursor: pointer;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }

    .pm-detail-actions .pm-btn-primary:active,
    .pm-detail-actions .pm-btn-secondary:active {
        transform: scale(0.97);
    }

    /* Botón eliminar banco: a la derecha, cuadrado en color rojo pastel */
    .pm-btn-delete-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 16px;
        background-color: #FCDEDE;
        color: #BA1A1A;
        border: none !important;
        box-shadow: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.15s ease, background-color 0.2s ease;
        padding: 0;
        flex-shrink: 0;
    }

    .pm-btn-delete-icon:hover {
        background-color: #F8C8C8;
    }

    .pm-btn-delete-icon:active {
        transform: scale(0.94);
    }

    .pm-btn-delete-icon .material-symbols-rounded {
        font-size: 22px;
    }

    body.dark-mode .pm-btn-delete-icon {
        background-color: #3E1A1D;
        color: #FFB4AB;
    }

    body.dark-mode .pm-btn-delete-icon:hover {
        background-color: #4D2024;
    }

    /* Modal de confirmación para eliminar banco */
    .pm-modal-overlay-confirm {
        z-index: 280 !important;
    }

    .pm-modal-confirm-sheet {
        text-align: center;
        padding-top: 14px !important;
        padding-bottom: calc(24px + env(safe-area-inset-bottom, 0px)) !important;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .pm-confirm-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background-color: #FCDEDE;
        color: #BA1A1A;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 6px auto 14px;
    }

    body.dark-mode .pm-confirm-icon-box {
        background-color: #3E1A1D;
        color: #FFB4AB;
    }

    .pm-confirm-icon-box .material-symbols-rounded {
        font-size: 28px;
    }

    .pm-confirm-title {
        margin: 0 0 8px 0;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--md-sys-color-on-surface);
        font-family: var(--font-google-sans);
    }

    .pm-confirm-desc {
        margin: 0 0 22px 0;
        font-size: 0.9rem;
        line-height: 1.45;
        color: var(--md-sys-color-outline);
        padding: 0 10px;
    }

    .pm-confirm-desc strong {
        color: var(--md-sys-color-on-surface);
    }

    .pm-confirm-actions {
        display: flex;
        gap: 12px;
        width: 100%;
        box-sizing: border-box;
    }

    .pm-confirm-actions button {
        flex: 1;
        height: 48px;
        border-radius: 16px;
        font-family: var(--font-google-sans);
        font-size: 0.94rem;
        font-weight: 700;
        border: none !important;
        box-shadow: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: transform 0.15s ease, background-color 0.2s ease;
    }

    .pm-confirm-actions button:active {
        transform: scale(0.96);
    }

    .pm-btn-cancel-delete {
        background-color: var(--md-sys-color-surface-container-high);
        color: var(--md-sys-color-on-surface);
    }

    .pm-btn-cancel-delete:hover {
        background-color: var(--md-sys-color-surface-container-highest);
    }

    .pm-btn-destructive {
        background-color: #BA1A1A;
        color: #FFFFFF;
    }

    .pm-btn-destructive:hover {
        background-color: #93000A;
    }

    body.dark-mode .pm-btn-destructive {
        background-color: #FFB4AB;
        color: #690005;
    }

    body.dark-mode .pm-btn-destructive:hover {
        background-color: #FFDAD6;
    }

    /* Modales Bottom Sheet M3 */
    .pm-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 250;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s cubic-bezier(0.2, 0, 0, 1);
    }

    .pm-modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .pm-modal-sheet {
        background-color: var(--md-sys-color-surface);
        width: 100%;
        max-width: 440px;
        border-radius: 32px 32px 0 0;
        padding: 12px 22px calc(20px + env(safe-area-inset-bottom, 0px));
        box-shadow: 0 -8px 36px rgba(0, 0, 0, 0.22);
        transform: translateY(100%);
        transition: transform 0.35s cubic-bezier(0.2, 0, 0, 1);
        max-height: 96vh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        box-sizing: border-box;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .pm-modal-sheet::-webkit-scrollbar {
        display: none;
    }

    .pm-modal-overlay.active .pm-modal-sheet {
        transform: translateY(0);
    }

    .pm-modal-handle {
        width: 36px;
        height: 4px;
        border-radius: 2px;
        background-color: var(--md-sys-color-outline);
        opacity: 0.35;
        margin: 0 auto 10px;
    }

    .pm-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .pm-modal-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--md-sys-color-on-surface);
    }

    .pm-modal-close {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: rgba(0, 0, 0, 0.05);
        color: var(--md-sys-color-on-surface);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.15s;
    }

    body.dark-mode .pm-modal-close {
        background: rgba(255, 255, 255, 0.1);
    }

    .pm-modal-close:active {
        transform: scale(0.92);
    }

    .pm-modal-subtext {
        font-size: 0.88rem;
        color: var(--md-sys-color-outline);
        margin: 0 0 12px;
        line-height: 1.4;
    }

    .pm-form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 12px;
    }

    .pm-form-row {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }

    .pm-form-group label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--md-sys-color-outline);
        letter-spacing: 0.01em;
    }

    .pm-form-group input, .pm-form-group select {
        width: 100%;
        height: 52px;
        line-height: normal;
        padding: 0 16px;
        border-radius: 16px;
        border: 1.5px solid transparent;
        background-color: var(--md-sys-color-surface-container);
        color: var(--md-sys-color-on-surface);
        font-family: var(--font-google-sans);
        font-size: 0.98rem;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    .pm-form-group select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' height='20' viewBox='0 -960 960 960' width='20' fill='%23617285'%3E%3Cpath d='M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
        padding-left: 14px;
        -webkit-appearance: none;
        appearance: none;
        cursor: pointer;
    }

    .pm-form-group select option {
        background-color: var(--md-sys-color-surface);
        color: var(--md-sys-color-on-surface);
    }

    .pm-form-group input:focus, .pm-form-group select:focus {
        border-color: var(--cambi-magic-mint-on-surface);
        box-shadow: none;
    }

    body.dark-mode .pm-form-group input:focus, body.dark-mode .pm-form-group select:focus {
        border-color: var(--cambi-magic-mint);
        box-shadow: none;
    }

    /* =========================================
       CUSTOM M3 SELECT DROPDOWNS PARA PAGO MÓVIL
       ========================================= */
    .pm-form-group select.pm-styled-select {
        display: none !important;
    }

    .pm-custom-select-wrap {
        position: relative;
        width: 100%;
        box-sizing: border-box;
    }

    .pm-custom-select-trigger {
        width: 100%;
        height: 52px;
        padding: 0 12px 0 16px;
        border-radius: 16px;
        border: 1.5px solid transparent;
        background-color: var(--md-sys-color-surface-container);
        color: var(--md-sys-color-on-surface);
        font-family: var(--font-google-sans);
        font-size: 0.98rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        outline: none;
        transition: border-color 0.22s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.22s cubic-bezier(0.2, 0, 0, 1), background-color 0.2s;
        box-sizing: border-box;
        text-align: left;
        user-select: none;
        -webkit-user-select: none;
    }

    .pm-custom-select-trigger:focus,
    .pm-custom-select-wrap.open .pm-custom-select-trigger {
        border-color: var(--cambi-magic-mint-on-surface);
        box-shadow: none;
    }

    body.dark-mode .pm-custom-select-trigger:focus,
    body.dark-mode .pm-custom-select-wrap.open .pm-custom-select-trigger {
        border-color: var(--cambi-magic-mint);
        box-shadow: none;
    }

    .pm-custom-select-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding-right: 6px;
        color: var(--md-sys-color-on-surface);
        font-size: 0.96rem;
    }

    .pm-custom-select-text.placeholder {
        color: var(--md-sys-color-outline);
    }

    .pm-custom-select-arrow {
        font-size: 22px;
        color: var(--md-sys-color-outline);
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1), color 0.2s;
        flex-shrink: 0;
    }

    .pm-custom-select-wrap.open .pm-custom-select-arrow {
        transform: rotate(180deg);
        color: var(--cambi-magic-mint-on-surface);
    }

    body.dark-mode .pm-custom-select-wrap.open .pm-custom-select-arrow {
        color: var(--cambi-magic-mint);
    }

    /* Menú Desplegable con animación fluida */
    .pm-custom-select-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        width: 100%;
        min-width: 100%;
        max-height: 220px;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background-color: var(--md-sys-color-surface-container-high);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 18px;
        padding: 6px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.18);
        z-index: 120;
        box-sizing: border-box;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.96);
        transform-origin: top center;
        transition: opacity 0.22s cubic-bezier(0.2, 0, 0, 1), transform 0.22s cubic-bezier(0.2, 0, 0, 1), visibility 0.22s;
        pointer-events: none;
        scrollbar-width: thin;
    }

    body.dark-mode .pm-custom-select-dropdown {
        background-color: #2b333c;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.5);
    }

    .pm-custom-select-wrap.open .pm-custom-select-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Despliegue hacia arriba cuando está cerca del borde inferior */
    .pm-custom-select-wrap.dropup .pm-custom-select-dropdown {
        top: auto;
        bottom: calc(100% + 6px);
        transform-origin: bottom center;
        transform: translateY(8px) scale(0.96);
    }

    .pm-custom-select-wrap.dropup.open .pm-custom-select-dropdown {
        transform: translateY(0) scale(1);
    }

    /* Items de selección */
    .pm-custom-select-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 12px;
        font-family: var(--font-google-sans);
        font-size: 0.92rem;
        font-weight: 500;
        color: var(--md-sys-color-on-surface);
        cursor: pointer;
        transition: background-color 0.15s cubic-bezier(0.2, 0, 0, 1), color 0.15s;
        box-sizing: border-box;
        user-select: none;
        -webkit-user-select: none;
    }

    .pm-custom-select-item:hover,
    .pm-custom-select-item:active {
        background-color: rgba(163, 241, 203, 0.18);
    }

    body.dark-mode .pm-custom-select-item:hover,
    body.dark-mode .pm-custom-select-item:active {
        background-color: rgba(163, 241, 203, 0.12);
    }

    .pm-custom-select-item.selected {
        background-color: var(--cambi-magic-mint);
        color: #004d34;
        font-weight: 700;
    }

    body.dark-mode .pm-custom-select-item.selected {
        background-color: var(--cambi-magic-mint);
        color: #004d34;
    }

    .pm-custom-select-item .pm-item-check {
        font-size: 18px;
        color: #004d34;
        opacity: 0;
        transform: scale(0.6);
        transition: opacity 0.15s cubic-bezier(0.2, 0, 0, 1), transform 0.15s;
        margin-left: 8px;
        flex-shrink: 0;
    }

    .pm-custom-select-item.selected .pm-item-check {
        opacity: 1;
        transform: scale(1);
    }

    .pm-modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }

    .pm-modal-actions button {
        flex: 1;
        padding: 13px 16px;
        border-radius: 18px;
        font-family: var(--font-google-sans);
        font-weight: 700;
    }


    /* Modal de Escáner QR */
    .pm-scanner-viewport {
        position: relative;
        width: 100%;
        max-width: 290px;
        height: 290px;
        margin: 0 auto 16px auto;
        border-radius: 24px;
        overflow: hidden;
        background-color: #000000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pm-scanner-viewport video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pm-scanner-target-box {
        position: absolute;
        width: 200px;
        height: 200px;
        border: 2px solid rgba(255, 255, 255, 0.85);
        border-radius: 20px;
        box-sizing: border-box;
        pointer-events: none;
        overflow: hidden;
    }
    .pm-scanner-laser {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background-color: var(--md-sys-color-primary, #6750a4);
        pointer-events: none;
        will-change: transform, opacity;
        animation: pmLaserAnim 2s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
    }
    @keyframes pmLaserAnim {
        0% { transform: translateY(8px); opacity: 0.35; }
        50% { opacity: 1; }
        100% { transform: translateY(190px); opacity: 0.35; }
    }

    .pm-scanner-bank-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 10px 14px;
        border-radius: 16px;
        background-color: var(--md-sys-color-surface-container-high);
        border: 1px solid var(--md-sys-color-outline-variant);
        color: var(--md-sys-color-on-surface);
        font-family: inherit;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.1s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .pm-scanner-bank-btn:hover {
        background-color: var(--md-sys-color-surface-container-highest);
    }
    .pm-scanner-bank-btn:active {
        transform: scale(0.985);
    }
    .pm-scanner-bank-btn-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pm-scanner-bank-btn-logo {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        flex-shrink: 0;
    }
    .pm-scanner-bank-btn-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Buscador de Bancos */
    .pm-bank-search-bar {
        position: relative;
        display: flex;
        align-items: center;
        background-color: var(--md-sys-color-surface-container-high);
        border: 1px solid var(--md-sys-color-outline-variant);
        border-radius: 24px;
        padding: 4px 14px;
        margin-bottom: 12px;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }
    .pm-bank-search-bar:focus-within {
        border-color: var(--md-sys-color-primary);
        background-color: var(--md-sys-color-surface-container-highest);
    }
    .pm-bank-search-icon {
        font-size: 1.25rem;
        color: var(--md-sys-color-outline);
        margin-right: 8px;
        flex-shrink: 0;
    }
    .pm-bank-search-field {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-size: 0.92rem;
        color: var(--md-sys-color-on-surface);
        padding: 8px 0;
    }
    .pm-bank-search-field::placeholder {
        color: var(--md-sys-color-outline);
    }
    .pm-bank-search-clear {
        background: transparent;
        border: none;
        padding: 4px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--md-sys-color-outline);
        border-radius: 50%;
        transition: color 0.15s ease;
    }
    .pm-bank-search-clear:hover {
        color: var(--md-sys-color-on-surface);
    }
    .pm-bank-search-clear span {
        font-size: 1.15rem;
    }
    .pm-bank-search-list {
        max-height: 290px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-right: 4px;
    }
    .pm-bank-search-list::-webkit-scrollbar {
        width: 6px;
    }
    .pm-bank-search-list::-webkit-scrollbar-thumb {
        background: var(--md-sys-color-outline-variant);
        border-radius: 4px;
    }
    .pm-bank-search-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-radius: 14px;
        cursor: pointer;
        background: transparent;
        border: none;
        width: 100%;
        text-align: left;
        font-family: inherit;
        transition: background-color 0.15s ease, transform 0.1s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .pm-bank-search-item:hover:not(.pm-bank-item-disabled) {
        background-color: var(--md-sys-color-surface-container-highest);
    }
    .pm-bank-search-item:active:not(.pm-bank-item-disabled) {
        transform: scale(0.985);
    }
    .pm-bank-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1;
    }
    .pm-bank-item-logo {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        flex-shrink: 0;
    }
    .pm-bank-item-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .pm-bank-item-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .pm-bank-item-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--md-sys-color-on-surface);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pm-bank-item-code {
        font-size: 0.76rem;
        color: var(--md-sys-color-outline);
        font-weight: 500;
    }
    .pm-bank-item-action {
        display: flex;
        align-items: center;
        color: var(--md-sys-color-primary);
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .pm-bank-item-disabled {
        opacity: 0.45;
        cursor: default;
    }
    .pm-bank-item-added-badge {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--md-sys-color-outline);
        background: var(--md-sys-color-surface-container-highest);
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .pm-bank-search-empty {
        padding: 24px 12px;
        text-align: center;
        color: var(--md-sys-color-outline);
        font-size: 0.88rem;
    }

    /* Badges y estados del Visor QR */
    .pm-qr-badge-official {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #e6f4ea;
        color: #137333;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 8px;
    }
    .dark .pm-qr-badge-official {
        background: rgba(52, 168, 83, 0.16);
        color: #81c995;
    }
    .pm-qr-empty-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--md-sys-color-surface-container-high);
        color: var(--md-sys-color-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .pm-qr-empty-icon span {
        font-size: 30px;
    }

        .pm-qrcode-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }
    .pm-qrcode-box canvas, .pm-qrcode-box img {
        display: block;
        max-width: 100%;
        height: auto;
    }

    /* Visor QR (debe estar por encima del modal de detalle del banco) */
    #pm-modal-qr {
        z-index: 270 !important;
    }

    .pm-qr-canvas-wrapper {
        display: flex;
        justify-content: center;
        margin: 12px 0;
    }

    #pm-qrcode-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    #pm-qrcode-container canvas, #pm-qrcode-container img {
        display: block;
        max-width: 100%;
        height: auto;
    }

    .pm-qr-info-box {
        background-color: var(--md-sys-color-surface-container);
        border-radius: 16px;
        padding: 10px 14px;
        margin-top: 12px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--md-sys-color-on-surface-variant);
        line-height: 1.4;
    }

    /* Toast */
    .pm-toast {
        position: fixed;
        bottom: calc(90px + env(safe-area-inset-bottom, 0px));
        left: 50%;
        transform: translateX(-50%) translateY(30px);
        background-color: #1e293b;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        z-index: 350;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s cubic-bezier(0.2, 0, 0, 1), transform 0.3s cubic-bezier(0.2, 0, 0, 1);
    }

    .pm-toast.active {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .pm-toast .material-symbols-rounded {
        color: var(--cambi-magic-mint);
        font-size: 20px;
    }
</style>
