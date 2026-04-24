<?php
/**
 * Login Administrativo - Plataforma JLC V2.0
 */
require_once '../config/config.php';
require_once '../config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND rol = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_nombre'] = $user->nombre_completo;
            $_SESSION['user_role'] = $user->rol;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Credenciales incorrectas o acceso denegado.";
        }
    } catch (PDOException $e) {
        $error = "Error de conexión: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Fundación JLC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
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
    </style>
</head>
<body class="bg-brand-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
        <div class="p-10">
            <div class="text-center mb-10">
                <div class="bg-brand-700 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-white text-2xl shadow-lg">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Panel Admin</h1>
                <p class="text-gray-500">Ingresa para gestionar la plataforma</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm flex items-center border border-red-100">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 ml-1">Correo Electrónico</label>
                    <input type="email" name="email" required placeholder="tu@correo.com" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 ml-1">Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <button type="submit" class="w-full bg-brand-700 text-white font-bold py-5 rounded-2xl shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                    ACCEDER AHORA
                </button>
            </form>
        </div>
        <div class="bg-gray-50 p-6 text-center border-t border-gray-100">
            <a href="../index.php" class="text-xs text-brand-700 font-bold hover:underline uppercase tracking-widest">Volver al sitio público</a>
        </div>
    </div>

</body>
</html>
