<?php
/**
 * Configuración Global - Fundación José Lisper Conde
 * Optimizado para Hostinger
 */

// Detección automática de entorno
if ($_SERVER['HTTP_HOST'] == 'fjlc.site' || $_SERVER['HTTP_HOST'] == 'www.fjlc.site') {
    // ENTORNO: PRODUCCIÓN (Hostinger)
    if (file_exists(__DIR__ . '/db_deploy.php')) {
        require_once __DIR__ . '/db_deploy.php';
    }
} else {
    // ENTORNO: LOCAL (Desarrollo)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u123456789_nombre_bd'); 
    define('DB_USER', 'u123456789_usuario_bd'); 
    define('DB_PASS', 'tu_password_seguro');
    define('BASE_URL', 'http://localhost/Tutorial PHP/'); 
}

// Información de la Institución
define('SITE_NAME', 'Fundación José Lisper Conde');
define('CONTACT_PHONE', '+57 123 456 7890');
define('CONTACT_EMAIL', 'contacto@fundacionjlc.org');

// Configuración de Firebase (Para uso en el Frontend)
define('FIREBASE_API_KEY', 'TU_API_KEY');
define('FIREBASE_AUTH_DOMAIN', 'TU_PROYECTO.firebaseapp.com');
define('FIREBASE_PROJECT_ID', 'TU_PROYECTO_ID');
define('FIREBASE_STORAGE_BUCKET', 'TU_PROYECTO.appspot.com');
define('FIREBASE_MESSAGING_SENDER_ID', 'TU_SENDER_ID');
define('FIREBASE_APP_ID', 'TU_APP_ID');

// Seguridad de Sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // Activado para HTTPS en Hostinger
session_start();
