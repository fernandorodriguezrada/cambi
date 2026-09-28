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
        width: 90%;
        max-width: 400px;
        margin: 0 auto;
        padding-top: 4px;
        padding-bottom: 24px;
    }

    .pm-view {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: opacity 0.3s cubic-bezier(0.2, 0, 0, 1);
    }

    /* Onboarding / Tour Card */
    .pm-tour-card {
        background-color: var(--md-sys-color-surface-container);
        border-radius: 28px;
        padding: 28px 20px 24px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .pm-carousel-wrapper {
        width: 100%;
        overflow: hidden;
        position: relative;
    }

    .pm-carousel {
        display: flex;
        transition: transform 0.4s cubic-bezier(0.2, 0, 0, 1);
        width: 100%;
    }

    .pm-slide {
        min-width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 4px 8px;
        box-sizing: border-box;
    }

    .pm-slide-icon-wrap {
        width: 76px;
        height: 76px;
        border-radius: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }

    .pm-slide-icon-wrap .material-symbols-rounded {
        font-size: 40px;
    }

    .pm-slide-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: var(--md-sys-color-on-surface);
    }

    .pm-slide-desc {
        font-size: 0.92rem;
        line-height: 1.45;
        color: var(--md-sys-color-on-surface-variant);
        margin: 0;
        max-width: 290px;
    }

    .pm-indicators {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
        margin: 20px 0 22px;
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
        width: 26px;
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
        padding: 14px 20px;
        border-radius: 20px;
        border: none;
        background-color: var(--cambi-magic-mint);
        color: #004d34;
        font-family: var(--font-google-sans);
        font-size: 1rem;
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
        margin-bottom: 14px;
        width: 100%;
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

    /* Sección de chips de bancos */
    .pm-banks-section {
        width: 100%;
        margin-bottom: 14px;
    }

    .pm-section-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--md-sys-color-outline);
        margin-bottom: 8px;
        display: block;
        padding-left: 4px;
    }

    .pm-chips-scroll {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .pm-chips-scroll::-webkit-scrollbar {
        display: none;
    }

    .pm-chips-list {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pm-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 16px;
        background-color: var(--md-sys-color-surface-container);
        border: 1.5px solid transparent;
        color: var(--md-sys-color-on-surface-variant);
        font-family: var(--font-google-sans);
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
    }

    .pm-chip.active {
        background-color: var(--cambi-magic-mint);
        color: #004d34;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(163, 241, 203, 0.35);
    }

    .pm-chip-add {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        border-radius: 16px;
        background-color: transparent;
        border: 1.5px dashed var(--md-sys-color-outline);
        color: var(--md-sys-color-outline);
        font-family: var(--font-google-sans);
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
        cursor: pointer;
        transition: border-color 0.2s, color 0.2s;
    }

    .pm-chip-add:hover, .pm-chip-add:active {
        border-color: var(--cambi-magic-mint-on-surface);
        color: var(--cambi-magic-mint-on-surface);
    }

    /* Tarjeta Principal del Pago Móvil */
    .pm-card {
        background-color: var(--md-sys-color-surface-container);
        border-radius: 28px;
        padding: 22px 20px 18px;
        width: 100%;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .pm-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    body.dark-mode .pm-card-top {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .pm-card-bank-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--md-sys-color-on-surface);
    }

    .pm-card-bank-badge .material-symbols-rounded {
        color: var(--cambi-baby-blue-on-surface);
    }

    body.dark-mode .pm-card-bank-badge .material-symbols-rounded {
        color: var(--cambi-baby-blue);
    }

    .pm-bank-code-pill {
        padding: 4px 10px;
        border-radius: 12px;
        background-color: rgba(177, 211, 254, 0.35);
        color: #1a6ec0;
        font-family: monospace;
        font-size: 0.9rem;
        font-weight: 700;
    }

    body.dark-mode .pm-bank-code-pill {
        background-color: rgba(177, 211, 254, 0.15);
        color: var(--cambi-baby-blue);
    }

    .pm-card-body {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .pm-field-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        font-size: 0.95rem;
    }

    .pm-field-label {
        color: var(--md-sys-color-outline);
        font-weight: 500;
    }

    .pm-field-value {
        color: var(--md-sys-color-on-surface);
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .pm-card-actions {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }

    .pm-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 13px 12px;
        border-radius: 18px;
        border: none;
        font-family: var(--font-google-sans);
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.15s, filter 0.15s;
    }

    .pm-action-btn:active {
        transform: scale(0.97);
        filter: brightness(0.96);
    }

    .pm-action-copy {
        background-color: var(--cambi-magic-mint);
        color: #004d34;
    }

    .pm-action-qr {
        background-color: var(--cambi-baby-blue);
        color: #0c4384;
    }

    body.dark-mode .pm-action-qr {
        color: #052449;
    }

    .pm-card-footer {
        display: flex;
        justify-content: center;
        margin-top: 12px;
    }

    .pm-delete-bank-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: none;
        border: none;
        color: #e53935;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        opacity: 0.75;
        padding: 4px 8px;
        border-radius: 8px;
        transition: opacity 0.2s;
    }

    .pm-delete-bank-btn:hover, .pm-delete-bank-btn:active {
        opacity: 1;
    }

    /* Modales Bottom Sheet M3 */
    .pm-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
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
        border-radius: 28px 28px 0 0;
        padding: 24px 22px calc(24px + env(safe-area-inset-bottom, 0px));
        box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.18);
        transform: translateY(100%);
        transition: transform 0.35s cubic-bezier(0.2, 0, 0, 1);
        max-height: 88vh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        box-sizing: border-box;
    }

    .pm-modal-overlay.active .pm-modal-sheet {
        transform: translateY(0);
    }

    .pm-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .pm-modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--md-sys-color-on-surface);
    }

    .pm-modal-close {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: rgba(0, 0, 0, 0.05);
        color: var(--md-sys-color-on-surface);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    body.dark-mode .pm-modal-close {
        background: rgba(255, 255, 255, 0.1);
    }

    .pm-modal-subtext {
        font-size: 0.88rem;
        color: var(--md-sys-color-outline);
        margin: -8px 0 16px;
        line-height: 1.4;
    }

    .pm-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .pm-form-row {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
    }

    .pm-form-group label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--md-sys-color-outline);
    }

    .pm-form-group input, .pm-form-group select {
        padding: 12px 14px;
        border-radius: 14px;
        border: 1.5px solid var(--md-sys-color-surface-container);
        background-color: var(--md-sys-color-surface-container);
        color: var(--md-sys-color-on-surface);
        font-family: var(--font-google-sans);
        font-size: 0.95rem;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s;
    }

    .pm-form-group input:focus, .pm-form-group select:focus {
        border-color: var(--cambi-magic-mint-on-surface);
    }

    .pm-modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .pm-modal-actions button {
        flex: 1;
    }

    /* Visor QR */
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
