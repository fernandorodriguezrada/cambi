<?php
$componentsDir = __DIR__ . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once $componentsDir . 'head.php'; ?>
    <script src="public/qrcode.min.js"></script>
    <script src="public/jsqr.min.js"></script>
</head>
<body>
    <script>
        if (window.matchMedia && (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true)) {
            document.documentElement.classList.add('pwa-standalone');
        }
    </script>
    <!-- Pantalla de Carga (Splash Screen) -->
    <div id="splash-screen" class="splash-screen">
        <div class="splash-content">
            <img src="public/logo.webp" alt="Cambi Logo" class="splash-logo">
        </div>
    </div>

    <?php 
    include_once $componentsDir . 'header.php';
    ?>
    <main id="main-content" class="main-content">
        <?php include_once $componentsDir . 'rates_tab.php'; ?>
        <?php include_once $componentsDir . 'pagomovil_tab.php'; ?>
        <?php include_once $componentsDir . 'calculator_tab.php'; ?>
        <?php include_once $componentsDir . 'history_tab.php'; ?>
    </main>
    <?php
    include_once $componentsDir . 'pagomovil_modals.php';
    include_once $componentsDir . 'calculator_modal.php';
    include_once $componentsDir . 'bottom_nav.php';
    include_once $componentsDir . 'scripts.php';
    ?>
</body>
</html>
