<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php?pagina=home-index');
    exit;
}

require_once __DIR__ . '/pages/auth/login.inc.php';
