<?php
$componentsDir = __DIR__ . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once $componentsDir . 'head.php'; ?>
</head>
<body>
    <!-- Pantalla de Carga (Splash Screen) -->
    <div id="splash-screen" class="splash-screen">
        <div class="splash-content">
            <img src="public/logo.webp" alt="Cambi Logo" class="splash-logo">
            <div class="progress-container">
                <div id="progress-bar" class="progress-bar"></div>
            </div>
        </div>
    </div>

    <?php 
    include_once $componentsDir . 'header.php';
    ?>
    <main id="main-content" class="main-content">
        <?php include_once $componentsDir . 'rates_tab.php'; ?>
        <?php include_once $componentsDir . 'calculator_tab.php'; ?>
        <?php include_once $componentsDir . 'history_tab.php'; ?>
    </main>
    <?php
    include_once $componentsDir . 'bottom_nav.php';
    include_once $componentsDir . 'scripts.php';
    ?>
</body>
</html>