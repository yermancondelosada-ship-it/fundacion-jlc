<?php
/**
 * Header Administrativo Compartido - Plataforma JLC
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';

// Verificación de sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance();

// Contador de pagos pendientes para el badge
try {
    $pendientes_pago = $db->query("SELECT COUNT(*) as total FROM inscripciones WHERE estado = 'pendiente'")->fetch()->total;
} catch (Exception $e) {
    $pendientes_pago = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin'; ?> | Fundación JLC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .active-link { background-color: #15803d; font-weight: bold; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-brand-900 text-white flex-shrink-0 hidden lg:flex flex-col sticky top-0 h-screen">
        <div class="p-6 border-b border-brand-800">
            <a href="../index.php" class="flex items-center space-x-3">
                <div class="bg-white p-2 rounded-lg text-brand-700">
                    <i class="fas fa-leaf"></i>
                </div>
                <span class="font-bold tracking-tight">ADMIN <span class="text-green-400">JLC</span></span>
            </a>
        </div>
        <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center space-x-3 p-3 rounded-xl transition-all hover:bg-brand-800 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active-link' : ''; ?>">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span>Resumen</span>
            </a>
            <a href="estudiantes.php" class="flex items-center space-x-3 p-3 rounded-xl transition-all hover:bg-brand-800 <?php echo basename($_SERVER['PHP_SELF']) == 'estudiantes.php' ? 'active-link' : ''; ?>">
                <i class="fas fa-users w-5 text-center"></i>
                <span>Estudiantes</span>
            </a>
            <a href="cursos.php" class="flex items-center space-x-3 p-3 rounded-xl transition-all hover:bg-brand-800 <?php echo basename($_SERVER['PHP_SELF']) == 'cursos.php' ? 'active-link' : ''; ?>">
                <i class="fas fa-book w-5 text-center"></i>
                <span>Cursos</span>
            </a>
            <a href="pagos.php" class="flex items-center space-x-3 p-3 rounded-xl transition-all hover:bg-brand-800 <?php echo basename($_SERVER['PHP_SELF']) == 'pagos.php' ? 'active-link' : ''; ?>">
                <i class="fas fa-receipt w-5 text-center"></i>
                <span>Pagos QR</span>
                <?php if($pendientes_pago > 0): ?>
                    <span class="ml-auto bg-yellow-400 text-brand-900 text-[10px] font-black px-2 py-0.5 rounded-full"><?php echo $pendientes_pago; ?></span>
                <?php endif; ?>
            </a>
            <a href="mensajes.php" class="flex items-center space-x-3 p-3 rounded-xl transition-all hover:bg-brand-800 <?php echo basename($_SERVER['PHP_SELF']) == 'mensajes.php' ? 'active-link' : ''; ?>">
                <i class="fas fa-envelope w-5 text-center"></i>
                <span>Mensajes</span>
            </a>
            <div class="pt-4 mt-4 border-t border-brand-800/50">
                <a href="configuracion.php" class="flex items-center space-x-3 p-3 rounded-xl transition-all hover:bg-brand-800 <?php echo basename($_SERVER['PHP_SELF']) == 'configuracion.php' ? 'active-link' : ''; ?>">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span>Configuración Web</span>
                </a>
            </div>
        </nav>
        <div class="p-4 border-t border-brand-800">
            <a href="../logout.php" class="flex items-center space-x-3 p-3 hover:text-red-400 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-grow flex flex-col min-w-0">
        <header class="bg-white h-16 border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-40">
            <div class="flex items-center">
                <button class="lg:hidden mr-4 text-gray-500"><i class="fas fa-bars text-xl"></i></button>
                <h2 class="text-lg font-bold text-gray-800"><?php echo $page_title ?? 'Panel Administrativo'; ?></h2>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900"><?php echo $_SESSION['user_nombre']; ?></p>
                    <p class="text-[10px] text-brand-600 uppercase font-black tracking-widest">Administrador</p>
                </div>
                <img src="https://i.pravatar.cc/150?u=<?php echo $_SESSION['user_id']; ?>" class="w-10 h-10 rounded-full border-2 border-brand-500 p-0.5" alt="Avatar">
            </div>
        </header>

        <main class="p-8">
