<?php
ob_start();
require_once 'config/config.php';
require_once 'config/db.php';
include_once 'includes/header.php';

$db = Database::getInstance();
$message = "";
$error = "";

// 1. AUTH LOGIC (Student Login/Register)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['auth_action'])) {
    if ($_POST['auth_action'] == 'login') {
        $email = $_POST['email'];
        $pass = $_POST['password'];
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($pass, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_nombre'] = $user->nombre_completo;
            $_SESSION['user_role'] = $user->rol;
            header("Location: aula-virtual.php"); exit;
        } else { $error = "Credenciales incorrectas."; }
    } 
    
    if ($_POST['auth_action'] == 'register') {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO users (nombre_completo, email, password, rol) VALUES (?, ?, ?, 'estudiante')");
            $stmt->execute([$nombre, $email, $pass]);
            $message = "Registro exitoso. Ahora puedes iniciar sesión.";
        } catch (Exception $e) { $error = "El correo ya está registrado."; }
    }
}

// 2. INSCRIPTION LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['comprobante'])) {
    if (!isset($_SESSION['user_id'])) { $error = "Debes iniciar sesión."; } else {
        $curso_id = $_POST['curso_id'];
        $user_id = $_SESSION['user_id'];
        $target_dir = "uploads/comprobantes/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES["comprobante"]["name"], PATHINFO_EXTENSION);
        $filename = "pago_" . $user_id . "_" . $curso_id . "_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $target_dir . $filename)) {
            $stmt = $db->prepare("INSERT INTO inscripciones (user_id, curso_id, comprobante_img, estado) VALUES (?, ?, ?, 'pendiente')");
            $stmt->execute([$user_id, $curso_id, $filename]);
            $message = "Comprobante enviado. Espera la validación administrativa.";
        }
    }
}

// 3. FETCH DATA
$cursos = $db->query("SELECT * FROM cursos WHERE estado = 'activo'")->fetchAll();

// Get user inscriptions if logged in
$mis_inscripciones = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT curso_id, estado FROM inscripciones WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $mis_inscripciones = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [curso_id => estado]
}
?>

<main class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">

        <!-- Welcome Banner -->
        <div class="bg-brand-700 rounded-[3rem] p-10 md:p-20 mb-12 text-white shadow-2xl relative overflow-hidden">
            <div class="relative z-10 max-w-2xl">
                <h1 class="text-5xl md:text-7xl font-black mb-6">Aula <span class="text-green-300">Virtual</span></h1>
                <p class="text-xl opacity-90 leading-relaxed">Tu plataforma de crecimiento profesional. Aprende a tu ritmo con los mejores expertos del Caquetá.</p>
            </div>
            <i class="fas fa-graduation-cap absolute bottom-0 right-0 text-[20rem] text-white/10 -mb-20 -mr-20"></i>
        </div>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="bg-green-500 text-white p-6 rounded-3xl mb-12 shadow-xl flex items-center animate-bounce">
                <i class="fas fa-check-circle mr-4 text-2xl"></i> <span class="font-bold"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-500 text-white p-6 rounded-3xl mb-12 shadow-xl flex items-center">
                <i class="fas fa-exclamation-triangle mr-4 text-2xl"></i> <span class="font-bold"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- LOGIN / REGISTER GATE -->
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Registration -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl border border-gray-100">
                    <h2 class="text-3xl font-black mb-8 text-gray-900">Crear Cuenta</h2>
                    <form action="aula-virtual.php" method="POST" class="space-y-6">
                        <input type="hidden" name="auth_action" value="register">
                        <input type="text" name="nombre" placeholder="Nombre Completo" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                        <input type="email" name="email" placeholder="Correo Electrónico" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                        <input type="password" name="password" placeholder="Contraseña" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                        <button type="submit" class="w-full bg-brand-700 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-brand-800 transition-all">REGISTRARME AHORA</button>
                    </form>
                    <div class="mt-8 flex items-center space-x-4">
                        <div class="flex-grow h-px bg-gray-100"></div>
                        <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">O entrar con</span>
                        <div class="flex-grow h-px bg-gray-100"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <button class="flex items-center justify-center py-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all font-bold text-sm">
                            <i class="fab fa-google mr-2 text-red-500"></i> Google
                        </button>
                        <button class="flex items-center justify-center py-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all font-bold text-sm">
                            <i class="fab fa-facebook mr-2 text-blue-600"></i> Facebook
                        </button>
                    </div>
                </div>

                <!-- Login -->
                <div class="bg-brand-900 p-10 rounded-[2.5rem] shadow-2xl text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-3xl font-black mb-8">Ya soy Estudiante</h2>
                        <form action="aula-virtual.php" method="POST" class="space-y-6">
                            <input type="hidden" name="auth_action" value="login">
                            <input type="email" name="email" placeholder="Tu Email" required class="w-full px-6 py-4 rounded-2xl bg-white/10 border-none text-white placeholder-white/50 focus:ring-2 focus:ring-brand-500">
                            <input type="password" name="password" placeholder="Tu Contraseña" required class="w-full px-6 py-4 rounded-2xl bg-white/10 border-none text-white placeholder-white/50 focus:ring-2 focus:ring-brand-500">
                            <button type="submit" class="w-full bg-white text-brand-900 font-black py-5 rounded-2xl shadow-xl hover:bg-brand-50 transition-all">ACCEDER AL PANEL</button>
                        </form>
                    </div>
                    <i class="fas fa-lock absolute -bottom-10 -right-10 text-9xl text-white/5"></i>
                </div>

            </div>
        <?php else: ?>
            <!-- DASHBOARD / CATALOG -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Mis Cursos y Oferta</h2>
                    <p class="text-gray-500 font-bold">Bienvenido, <?php echo $_SESSION['user_nombre']; ?></p>
                </div>
                <a href="logout.php" class="bg-red-50 text-red-600 px-6 py-3 rounded-xl font-black text-sm hover:bg-red-100 transition-all">CERRAR SESIÓN</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php foreach ($cursos as $curso): ?>
                <?php 
                    $estado = $mis_inscripciones[$curso->id] ?? 'no_inscrito'; 
                ?>
                <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-xl border border-gray-100 group flex flex-col">
                    <div class="h-56 relative overflow-hidden">
                        <img src="<?php echo $curso->imagen_portada ? 'uploads/cursos/'.$curso->imagen_portada : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80'; ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-6 right-6 bg-brand-700 text-white px-5 py-2 rounded-2xl font-black text-sm shadow-xl">
                            $<?php echo number_format($curso->precio, 0, ',', '.'); ?>
                        </div>
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <h3 class="text-2xl font-black text-gray-900 mb-4"><?php echo $curso->titulo; ?></h3>
                        <p class="text-gray-500 mb-8 line-clamp-3 text-sm flex-grow"><?php echo $curso->resumen; ?></p>
                        
                        <?php if ($estado == 'completado'): ?>
                            <a href="ver-curso.php?id=<?php echo $curso->id; ?>" class="w-full bg-brand-700 text-white text-center py-5 rounded-2xl font-black shadow-xl hover:bg-brand-800 transition-all">ENTRAR AL CURSO</a>
                        <?php elseif ($estado == 'pendiente'): ?>
                            <button disabled class="w-full bg-yellow-400 text-brand-900 py-5 rounded-2xl font-black shadow-xl cursor-not-allowed flex items-center justify-center">
                                <i class="fas fa-clock mr-2"></i> PAGO EN VERIFICACIÓN
                            </button>
                        <?php else: ?>
                            <button onclick="openPaymentModal(<?php echo $curso->id; ?>, '<?php echo $curso->titulo; ?>')" class="w-full bg-gray-900 text-white py-5 rounded-2xl font-black shadow-xl hover:bg-brand-700 transition-all">PAGAR E INSCRIBIRSE</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
    <div class="bg-white rounded-[3rem] shadow-2xl max-w-md w-full overflow-hidden animate__animated animate__zoomIn">
        <div class="bg-brand-700 p-8 text-white flex justify-between items-center">
            <div>
                <h3 class="font-black text-2xl" id="modalCourseTitle">Inscripción</h3>
                <p class="text-xs text-green-200 uppercase tracking-[0.3em] font-bold">Pago Seguro QR</p>
            </div>
            <button onclick="closePaymentModal()" class="text-white/80 hover:text-white text-3xl"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-10 text-center">
            <p class="text-gray-500 mb-8 font-medium">Escanea el código para pagar el curso.</p>
            <div class="bg-gray-50 p-6 rounded-[2rem] mb-10 border-4 border-dashed border-gray-100 inline-block">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=JLC-PAYMENT-MOCK" class="w-56 h-56" alt="QR">
            </div>
            <form action="aula-virtual.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="curso_id" id="modalCursoId">
                <div class="relative">
                    <input type="file" name="comprobante" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="bg-brand-50 border-2 border-brand-200 border-dotted rounded-2xl p-6 text-center">
                        <i class="fas fa-cloud-upload-alt text-brand-700 text-3xl mb-3"></i>
                        <p class="text-sm font-bold text-brand-800">Cargar Comprobante</p>
                    </div>
                </div>
                <button type="submit" class="w-full bg-brand-700 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-brand-800 transition-all">ENVIAR PARA VALIDACIÓN</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openPaymentModal(id, title) {
        document.getElementById('modalCursoId').value = id;
        document.getElementById('modalCourseTitle').innerText = title;
        document.getElementById('paymentModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<?php 
$out = ob_get_clean();
echo $out;
include_once 'includes/footer.php'; 
?>