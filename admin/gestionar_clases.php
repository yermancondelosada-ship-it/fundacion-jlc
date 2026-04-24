<?php 
ob_start();
require_once '../config/config.php';
require_once '../config/db.php';

$curso_id = $_GET['curso_id'] ?? 0;
$db = Database::getInstance();

// Fetch Course Info
$stmt = $db->prepare("SELECT titulo FROM cursos WHERE id = ?");
$stmt->execute([$curso_id]);
$curso = $stmt->fetch();

if (!$curso) { header("Location: cursos.php"); exit; }

$page_title = "Estructura Académica: " . $curso->titulo;
include_once 'includes/admin_header.php'; 

$message = "";

// 1. MODULE LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_modulo'])) {
    $nombre = $_POST['nombre'];
    if ($_POST['action_modulo'] == 'add') {
        $stmt = $db->prepare("INSERT INTO modulos (curso_id, nombre) VALUES (?, ?)");
        $stmt->execute([$curso_id, $nombre]);
        header("Location: gestionar_clases.php?curso_id=$curso_id&msg=modulo_creado");
        exit;
    }
}

// 2. LESSON LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_leccion'])) {
    $modulo_id = $_POST['modulo_id'];
    $titulo = $_POST['titulo'];
    $video_url = $_POST['video_url'];
    $contenido = $_POST['contenido'];
    
    $stmt = $db->prepare("INSERT INTO lecciones (modulo_id, titulo, contenido, video_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$modulo_id, $titulo, $contenido, $video_url]);
    header("Location: gestionar_clases.php?curso_id=$curso_id&msg=leccion_agregada");
    exit;
}

// Handle Delete
if (isset($_GET['delete_modulo'])) {
    $db->prepare("DELETE FROM modulos WHERE id = ?")->execute([$_GET['delete_modulo']]);
    header("Location: gestionar_clases.php?curso_id=$curso_id"); exit;
}
if (isset($_GET['delete_leccion'])) {
    $db->prepare("DELETE FROM lecciones WHERE id = ?")->execute([$_GET['delete_leccion']]);
    header("Location: gestionar_clases.php?curso_id=$curso_id"); exit;
}

// 3. FETCH DATA
$modulos = $db->prepare("SELECT * FROM modulos WHERE curso_id = ? ORDER BY orden ASC");
$modulos->execute([$curso_id]);
$modulos = $modulos->fetchAll();

foreach ($modulos as &$mod) {
    $stmt = $db->prepare("SELECT * FROM lecciones WHERE modulo_id = ? ORDER BY orden ASC");
    $stmt->execute([$mod->id]);
    $mod->lecciones = $stmt->fetchAll();
}
?>

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 leading-tight"><?php echo $curso->titulo; ?></h1>
            <p class="text-brand-700 font-bold flex items-center mt-1">
                <i class="fas fa-graduation-cap mr-2"></i> Gestión de Contenido Académico
            </p>
        </div>
        <a href="cursos.php" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Cursos
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="bg-brand-700 text-white p-6 rounded-3xl mb-10 shadow-xl flex items-center animate-bounce">
            <i class="fas fa-check-circle mr-3 text-xl"></i> 
            <span class="font-bold"><?php echo $_GET['msg'] == 'modulo_creado' ? '¡Módulo creado con éxito!' : '¡Lección agregada correctamente!'; ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Sidebar: Add Module Card -->
        <div class="lg:col-span-4">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 sticky top-28">
                <h3 class="text-xl font-black text-gray-900 mb-6">Nuevo Módulo</h3>
                <form action="" method="POST" class="space-y-4">
                    <input type="hidden" name="action_modulo" value="add">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nombre del Módulo</label>
                        <input type="text" name="nombre" placeholder="Ej: Fundamentos Técnicos" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>
                    <button type="submit" class="w-full bg-brand-700 text-white font-black py-4 rounded-2xl shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                        CREAR MÓDULO <i class="fas fa-plus-circle ml-2"></i>
                    </button>
                </form>
                <div class="mt-8 pt-8 border-t border-gray-100">
                    <p class="text-sm text-gray-500 leading-relaxed italic">
                        "La educación es el arma más poderosa que puedes usar para cambiar el mundo."
                    </p>
                </div>
            </div>
        </div>

        <!-- Main: Modules List -->
        <div class="lg:col-span-8 space-y-8">
            <?php if(empty($modulos)): ?>
                <div class="bg-white p-20 rounded-[3.5rem] text-center shadow-xl border border-gray-100">
                    <div class="w-20 h-20 bg-brand-50 text-brand-700 rounded-3xl flex items-center justify-center mx-auto mb-6 text-3xl">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 mb-2">Sin Contenido</h4>
                    <p class="text-gray-500">Crea el primer módulo para empezar a subir lecciones.</p>
                </div>
            <?php endif; ?>

            <?php foreach($modulos as $mod): ?>
            <div class="bg-white rounded-[3rem] shadow-xl border border-gray-100 overflow-hidden group hover:shadow-2xl transition-all">
                <div class="p-8 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-brand-700 text-white rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-book text-sm"></i>
                        </div>
                        <h4 class="text-xl font-black text-gray-900"><?php echo $mod->nombre; ?></h4>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="openLeccionModal(<?php echo $mod->id; ?>)" class="bg-brand-700 text-white text-[10px] px-5 py-2.5 rounded-full font-black uppercase tracking-[0.1em] shadow-lg hover:bg-brand-800 transition-all">
                            + AGREGAR LECCIÓN
                        </button>
                        <a href="?curso_id=<?php echo $curso_id; ?>&delete_modulo=<?php echo $mod->id; ?>" onclick="return confirm('¿Borrar módulo y sus lecciones?')" class="text-gray-300 hover:text-red-600 transition-colors">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                </div>
                
                <div class="p-8 space-y-4">
                    <?php if(empty($mod->lecciones)): ?>
                        <div class="py-6 text-center">
                            <p class="text-sm text-gray-400 font-bold italic">Este módulo aún no tiene lecciones.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php foreach($mod->lecciones as $lec): ?>
                    <div class="flex items-center justify-between p-5 bg-white rounded-2xl border border-gray-100 group/item hover:border-brand-300 hover:bg-brand-50/20 transition-all shadow-sm">
                        <div class="flex items-center space-x-5">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 group-hover/item:text-brand-700 group-hover/item:bg-white transition-all">
                                <i class="fas fa-play text-xs"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 group-hover/item:text-brand-700 transition-colors"><?php echo $lec->titulo; ?></p>
                                <div class="flex items-center space-x-4 mt-1">
                                    <?php if($lec->video_url): ?>
                                        <span class="text-[9px] text-brand-600 font-black uppercase tracking-widest flex items-center">
                                            <i class="fab fa-youtube mr-1 text-sm"></i> Video de Clase
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <a href="?curso_id=<?php echo $curso_id; ?>&delete_leccion=<?php echo $lec->id; ?>" class="text-gray-200 hover:text-red-500 transition-colors">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal: Nueva Lección -->
<div id="leccionModal" class="fixed inset-0 z-[100] hidden bg-brand-900/40 flex items-center justify-center p-4 backdrop-blur-md">
    <div class="bg-white rounded-[3.5rem] shadow-2xl max-w-2xl w-full overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="bg-brand-700 p-8 text-white flex justify-between items-center">
            <div>
                <h3 class="text-2xl font-black">Nueva Lección</h3>
                <p class="text-xs text-green-200 uppercase tracking-widest font-bold">Añadiendo contenido académico</p>
            </div>
            <button onclick="closeLeccionModal()" class="text-white/80 hover:text-white text-3xl transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="" method="POST" class="p-10 space-y-6">
            <input type="hidden" name="action_leccion" value="add">
            <input type="hidden" name="modulo_id" id="modalModuloId">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Título de la Lección</label>
                    <input type="text" name="titulo" required placeholder="Ej: Introducción a la variable" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 font-bold text-lg">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Link de YouTube</label>
                    <input type="text" name="video_url" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Descripción / Apuntes de Clase</label>
                    <textarea name="contenido" rows="5" placeholder="Puntos clave, instrucciones o resumen de la lección..." class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-brand-700 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                PUBLICAR LECCIÓN <i class="fas fa-paper-plane ml-2"></i>
            </button>
        </form>
    </div>
</div>

<script>
    function openLeccionModal(id) {
        document.getElementById('modalModuloId').value = id;
        document.getElementById('leccionModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeLeccionModal() {
        document.getElementById('leccionModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<?php 
$out = ob_get_clean();
echo $out;
include_once 'includes/admin_footer.php'; 
?>
