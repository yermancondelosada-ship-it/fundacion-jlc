<?php
/**
 * Funciones de Utilidad - Plataforma JLC V2.0
 */

/**
 * Sanitizar entradas de texto
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Formatear moneda (Pesos Colombianos)
 */
function formatCurrency($amount) {
    return '$' . number_format($amount, 0, ',', '.');
}

/**
 * Subir comprobante de pago
 */
function uploadComprobante($file, $userId, $cursoId) {
    $targetDir = COMPROBANTES_PATH;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = "pago_" . $userId . "_" . $cursoId . "_" . time() . "." . $extension;
    $targetFile = $targetDir . $fileName;

    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array(strtolower($extension), $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de archivo no permitido.'];
    }

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'fileName' => $fileName];
    } else {
        return ['success' => false, 'message' => 'Error al subir el archivo.'];
    }
}

/**
 * Verificar si el usuario está logueado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verificar si es administrador
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirección segura
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}
