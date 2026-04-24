<?php
require_once 'config/config.php';
require_once 'config/db.php';

// Obtener conexión
$db = Database::getInstance();

// Consulta de cursos (Asegúrate de que la tabla 'cursos' exista)
try {
    $stmt = $db->prepare("SELECT * FROM cursos WHERE estado = 'activo' ORDER BY id DESC");
    $stmt->execute();
    $cursos = $stmt->fetchAll();
} catch (Exception $e) {
    $cursos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capacítate | Fundación JLC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <section class="page-header">
        <h1>Centro de Capacitación</h1>
        <p>Aprende con los mejores programas de formación estratégica.</p>
    </section>

    <main class="container">
        <!-- Estado: No Logueado -->
        <div id="logged-out-view" class="auth-card">
            <i class="fas fa-lock"></i>
            <h2>Acceso Restringido</h2>
            <p>Para ver nuestros cursos disponibles y comenzar tu formación, por favor inicia sesión con tu cuenta de Google.</p>
            <button id="btn-login-main" class="btn-google-large">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/action/google.svg" alt="Google">
                Iniciar Sesión con Google
            </button>
        </div>

        <!-- Estado: Logueado -->
        <div id="logged-in-view" style="display: none;">
            <div class="courses-grid">
                <?php if(empty($cursos)): ?>
                    <p>Próximamente nuevos cursos para ti.</p>
                <?php else: ?>
                    <?php foreach($cursos as $curso): ?>
                        <div class="course-card">
                            <div class="course-img">
                                <img src="https://via.placeholder.com/400x200?text=Curso+JLC" alt="<?= $curso->nombre ?>">
                            </div>
                            <div class="course-info">
                                <span class="category"><?= $curso->categoria ?? 'General' ?></span>
                                <h3><?= $curso->nombre ?></h3>
                                <p><?= substr($curso->descripcion, 0, 100) ?>...</p>
                                <a href="ver-curso.php?id=<?= $curso->id ?>" class="btn-view">Ver Curso</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
    body { background: #f4f7f6; color: #333; }
    .container { max-width: 1200px; margin: 3rem auto; padding: 0 2rem; }
    
    .page-header { background: #004a99; color: white; padding: 4rem 2rem; text-align: center; }
    .page-header h1 { font-size: 2.5rem; margin-bottom: 1rem; }

    /* Auth Card */
    .auth-card { background: white; padding: 4rem; border-radius: 20px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
    .auth-card i { font-size: 4rem; color: #004a99; margin-bottom: 2rem; }
    .auth-card h2 { margin-bottom: 1rem; color: #004a99; }
    .auth-card p { margin-bottom: 2rem; color: #666; }

    .btn-google-large { display: flex; align-items: center; justify-content: center; gap: 15px; width: 100%; padding: 1rem; border: 1px solid #ddd; background: white; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: 0.3s; }
    .btn-google-large:hover { background: #f9f9f9; transform: translateY(-2px); }
    .btn-google-large img { width: 24px; }

    /* Courses Grid */
    .courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; }
    .course-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; }
    .course-card:hover { transform: translateY(-5px); }
    .course-img img { width: 100%; height: 200px; object-fit: cover; }
    .course-info { padding: 1.5rem; }
    .category { color: #28a745; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
    .course-info h3 { margin: 0.5rem 0; color: #004a99; }
    .course-info p { font-size: 0.9rem; color: #666; margin-bottom: 1.5rem; }
    .btn-view { display: block; text-align: center; background: #004a99; color: white; text-decoration: none; padding: 0.8rem; border-radius: 6px; font-weight: 600; }
    </style>

    <script type="module">
        import { auth, provider, signInWithPopup, onAuthStateChanged } from './js/firebase-config.js';

        const loggedOutView = document.getElementById('logged-out-view');
        const loggedInView = document.getElementById('logged-in-view');
        const btnLoginMain = document.getElementById('btn-login-main');

        onAuthStateChanged(auth, (user) => {
            if (user) {
                loggedOutView.style.display = 'none';
                loggedInView.style.display = 'block';
            } else {
                loggedOutView.style.display = 'block';
                loggedInView.style.display = 'none';
            }
        });

        if(btnLoginMain) {
            btnLoginMain.onclick = async () => {
                await signInWithPopup(auth, provider);
            };
        }
    </script>
</body>
</html>
