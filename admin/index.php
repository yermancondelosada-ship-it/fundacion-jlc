<?php
/**
 * Punto de entrada del Panel Administrativo
 */
require_once __DIR__ . '/../config/config.php';

// Verificación de sesión y rol
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    include __DIR__ . '/dashboard.php';
} else {
    header("Location: login.php");
    exit();
}
?>
