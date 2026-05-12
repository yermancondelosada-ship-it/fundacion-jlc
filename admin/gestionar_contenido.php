<?php 
ob_start();
require_once '../config/config.php';
require_once '../config/db.php';
$page_title = "Editor Rápido de Contenido";
include_once __DIR__ . '/includes/admin_header.php';

$db = Database::getInstance();
$message = "";

// 1. QUICK UPLOAD LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $curso_id = $_POST['curso_id'];
    $modulo_nombre = $_POST['modulo_nombre'] ?: "Módulo General";
    $leccion_titulo = $_POST['leccion_titulo'];
    $video_url = $_POST['video_url'];
    $contenido = $_POST['contenido'];
    
    // Ensure modulo exists or create it
    $stmt = $db->prepare("SELECT id FROM modulos WHERE curso_id = ? AND nombre = ? LIMIT 1");
    $stmt->execute([$curso_id, $modulo_nombre]);
    $mod = $stmt->fetch();
    
    if (!$mod) {
        $stmt = $db->prepare("INSERT INTO modulos (curso_id, nombre) VALUES (?, ?)");
        $stmt->execute([$curso_id, $modulo_nombre]);
        $modulo_id = $db->lastInsertId();
    } else {
        $modulo_id = $mod->id;
    }

    // Handle PDF
    $pdf_file = "";
    if (isset($_FILES['material_pdf']) && $_FILES['material_pdf']['error'] == 0) {
        $ext = pathinfo($_FILES['material_pdf']['name'], PATHINFO_EXTENSION);
        $filename = "material_" . time() . "_" . uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['material_pdf']['tmp_name'], "../uploads/cursos/" . $filename)) {
            $pdf_file = $filename;
        }
    }

    // Insert Lesson
    $stmt = $db->prepare("INSERT INTO lecciones (modulo_id, titulo, contenido, video_url, material_pdf) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$modulo_id, $leccion_titulo, $contenido, $video_url, $pdf_file]);
    
    header("Location: gestionar_contenido.php?success=1");
    exit;
}

// 2. QUICK UPLOAD CAROUSEL LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'quick_carrusel') {
    $titulo = $_POST['carrusel_titulo'];
    $descripcion = $_POST['carrusel_descripcion'];
    
    if (isset($_FILES['carrusel_imagen']) && $_FILES['carrusel_imagen']['error'] == 0) {
        $ext = pathinfo($_FILES['carrusel_imagen']['name'], PATHINFO_EXTENSION);
        $filename = "slide_" . time() . "_" . uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['carrusel_imagen']['tmp_name'], "../uploads/img/" . $filename)) {
            $stmt = $db->prepare("INSERT INTO carrusel (imagen, titulo, descripcion) VALUES (?, ?, ?)");
            $stmt->execute([$filename, $titulo, $descripcion]);
        }
    }
    
    header("Location: gestionar_contenido.php?success=2");
    exit;
}

$cursos = $db->query("SELECT id, titulo FROM cursos WHERE estado = 'activo'")->fetchAll();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12">
        <h3 class="text-3xl font-black text-gray-900 mb-2">Carga Rápida de Lecciones</h3>
        <p class="text-gray-500 font-medium">Sube contenido a tus cursos en segundos.</p>
    </div>

    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="bg-brand-700 text-white p-6 rounded-[2rem] mb-12 shadow-2xl flex items-center animate__animated animate__fadeIn">
            <i class="fas fa-rocket mr-4 text-2xl"></i> <span class="font-bold">¡Lección publicada con éxito!</span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden">
        <form action="" method="POST" enctype="multipart/form-data" class="p-12 space-y-8">
            <input type="hidden" name="action" value="quick_add">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">1. Seleccionar Curso</label>
                    <select name="curso_id" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 font-bold">
                        <?php foreach($cursos as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo $c->titulo; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">2. Nombre del Módulo</label>
                    <input type="text" name="modulo_nombre" placeholder="Ej: Fundamentos / Módulo 1" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="space-y-6 pt-4 border-t">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">3. Título de la Lección</label>
                    <input type="text" name="leccion_titulo" required placeholder="Ej: Introducción a la materia" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 text-lg font-bold">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">4. Link de Video (YouTube)</label>
                        <input type="text" name="video_url" placeholder="https://youtube.com/watch?v=..." class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">5. Material de Apoyo (PDF)</label>
                        <input type="file" name="material_pdf" class="text-sm font-bold text-gray-400 file:bg-brand-50 file:text-brand-700 file:rounded-full file:px-4 file:py-2 file:border-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">6. Descripción / Apuntes</label>
                    <textarea name="contenido" rows="5" placeholder="Escribe aquí los puntos clave o instrucciones de la clase..." class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-brand-900 text-white font-black py-6 rounded-3xl shadow-2xl hover:bg-brand-800 transition-all transform hover:-translate-y-1 flex items-center justify-center text-xl">
                <i class="fas fa-cloud-upload-alt mr-3"></i> PUBLICAR CONTENIDO AHORA
            </button>
        </form>
    </div>

    <!-- Carrusel Section -->
    <div class="mt-12 mb-12">
        <h3 class="text-3xl font-black text-gray-900 mb-2">Carga de Imágenes del Carrusel</h3>
        <p class="text-gray-500 font-medium">Sube nuevas imágenes al banner principal de la página de inicio.</p>
    </div>

    <?php if(isset($_GET['success']) && $_GET['success'] == 2): ?>
        <div class="bg-brand-700 text-white p-6 rounded-[2rem] mb-12 shadow-2xl flex items-center animate__animated animate__fadeIn">
            <i class="fas fa-image mr-4 text-2xl"></i> <span class="font-bold">¡Imagen agregada al carrusel con éxito!</span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden mb-12">
        <form action="" method="POST" enctype="multipart/form-data" class="p-12 space-y-8">
            <input type="hidden" name="action" value="quick_carrusel">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">1. Imagen del Banner (Recomendado: 1920x800)</label>
                    <input type="file" name="carrusel_imagen" required class="text-sm font-bold text-gray-400 file:bg-brand-50 file:text-brand-700 file:rounded-full file:px-4 file:py-2 file:border-none w-full">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">2. Título Principal</label>
                    <input type="text" name="carrusel_titulo" required placeholder="Ej: Transformando el Mañana" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-brand-700 mb-3">3. Subtítulo / Descripción</label>
                    <input type="text" name="carrusel_descripcion" placeholder="Ej: Impulsamos el desarrollo integral..." class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-black py-6 rounded-3xl shadow-2xl hover:bg-blue-700 transition-all transform hover:-translate-y-1 flex items-center justify-center text-xl">
                <i class="fas fa-images mr-3"></i> SUBIR IMAGEN AL CARRUSEL
            </button>
        </form>
    </div>

</div>

<?php 
$out = ob_get_clean();
echo $out;
include_once __DIR__ . '/includes/admin_footer.php'; 
?>
