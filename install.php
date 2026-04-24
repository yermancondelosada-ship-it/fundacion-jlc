<?php
/**
 * Instalador Inicial - Plataforma JLC V2.0
 */
require_once 'config/db.php';
require_once 'config/config.php';

$message = "";
$status = "info"; // info, success, error

try {
    $db = Database::getInstance();

    // Datos del administrador
    $nombre = "Administrador Principal";
    $email = "yermanconde@hotmail.com";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $rol = "admin";

    // Verificar si el usuario ya existe
    $check = $db->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $message = "El usuario administrador ya existe en el sistema.";
        $status = "info";
    } else {
        // Insertar usuario
        $stmt = $db->prepare("INSERT INTO users (nombre_completo, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $password, $rol]);

        $message = "¡Instalación exitosa! El administrador ha sido creado correctamente.";
        $status = "success";
    }

} catch (PDOException $e) {
    $message = "Error de base de datos: " . $e->getMessage();
    $status = "error";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador | Fundación JLC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-brand-700 p-8 text-center text-white relative">
            <div class="bg-white/20 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-server text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold">Sistema de Instalación</h1>
            <p class="text-green-200 text-sm">Plataforma JLC V2.0</p>
        </div>

        <!-- Body -->
        <div class="p-10 text-center">
            <?php if ($status == 'success'): ?>
                <div
                    class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                    <i class="fas fa-check"></i>
                </div>
            <?php elseif ($status == 'error'): ?>
                <div
                    class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            <?php else: ?>
                <div
                    class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                    <i class="fas fa-info-circle"></i>
                </div>
            <?php endif; ?>

            <h2 class="text-xl font-bold text-gray-900 mb-4">Resultado del Proceso</h2>
            <p class="text-gray-600 mb-8 leading-relaxed"><?php echo $message; ?></p>

            <div class="space-y-4">
                <?php if ($status != 'error'): ?>
                    <a href="admin/login.php"
                        class="block w-full bg-brand-700 text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                        Ir al Panel Administrativo
                    </a>
                <?php endif; ?>
                <a href="index.php" class="block w-full text-brand-700 font-bold py-2 text-sm hover:underline">
                    Regresar al Inicio
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-6 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400 font-medium tracking-widest uppercase">Fundación JLC &copy;
                <?php echo date('Y'); ?></p>
        </div>
    </div>

    <script>
        // Custom colors for Tailwind if needed
        window.tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            700: '#15803d',
                            800: '#166534',
                        }
                    }
                }
            }
        }
    </script>
</body>

</html>