<?php
/**
 * Configuración Global - EJEMPLO
 * Renombra este archivo a config.php y completa con tus datos.
 */

// Definición de la URL base
define('BASE_URL', 'http://localhost/tu-proyecto/'); 

// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'nombre_de_tu_bd'); 
define('DB_USER', 'usuario_de_tu_bd'); 
define('DB_PASS', 'tu_password_seguro');

// Información de la Institución
define('SITE_NAME', 'Fundación JLC');
define('CONTACT_PHONE', '+57 000 000 0000');
define('CONTACT_EMAIL', 'admin@ejemplo.org');

// Configuración de Firebase
define('FIREBASE_API_KEY', 'TU_API_KEY');
define('FIREBASE_AUTH_DOMAIN', 'TU_PROYECTO.firebaseapp.com');
define('FIREBASE_PROJECT_ID', 'TU_PROYECTO_ID');
define('FIREBASE_STORAGE_BUCKET', 'TU_PROYECTO.appspot.com');
define('FIREBASE_MESSAGING_SENDER_ID', 'TU_SENDER_ID');
define('FIREBASE_APP_ID', 'TU_APP_ID');

// Seguridad de Sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();
