<?php
/**
 * Configuración Global - Fundación José Lisper Conde
 * Optimizado para Hostinger
 */

// Detección automática de entorno
$isProduction = (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'fundacionjlc.org' || $_SERVER['HTTP_HOST'] == 'www.fundacionjlc.org'));

if ($isProduction) {
    // ENTORNO: PRODUCCIÓN (Hostinger)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u800252909_fjlc_db');
    define('DB_USER', 'u800252909_admin_fjlc');
    define('DB_PASS', 'German_2025');
    define('BASE_URL', 'https://fundacionjlc.org/');
} else {
    // ENTORNO: LOCAL (Desarrollo)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u800252909_fjlc_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
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
if ($isProduction) {
    ini_set('session.cookie_secure', 1); // Activado solo para HTTPS en Hostinger
}
session_start();
