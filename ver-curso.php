<?php
require_once 'config/config.php';
require_once 'config/db.php';
include_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: aula-virtual.php"); exit;
}

$curso_id = $_GET['id'] ?? 0;
$leccion_id = $_GET['leccion_id'] ?? 0;
$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

// 1. SECURITY CHECK
$stmt = $db->prepare("SELECT estado FROM inscripciones WHERE user_id = ? AND curso_id = ? AND estado = 'completado'");
$stmt->execute([$user_id, $curso_id]);
if (!$stmt->fetch()) {
    header("Location: aula-virtual.php?error=acceso_denegado"); exit;
}

// 2. FETCH COURSE & STRUCTURE
$stmt = $db->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->execute([$curso_id]);
$curso = $stmt->fetch();

$modulos_stmt = $db->prepare("SELECT * FROM modulos WHERE curso_id = ? ORDER BY orden ASC");
$modulos_stmt->execute([$curso_id]);
$modulos = $modulos_stmt->fetchAll();

foreach ($modulos as &$mod) {
    $stmt = $db->prepare("SELECT * FROM lecciones WHERE modulo_id = ? ORDER BY orden ASC");
    $stmt->execute([$mod->id]);
    $mod->lecciones = $stmt->fetchAll();
}

// 3. CURRENT LESSON
if ($leccion_id > 0) {
    $stmt = $db->prepare("SELECT * FROM lecciones WHERE id = ?");
    $stmt->execute([$leccion_id]);
    $current_lesson = $stmt->fetch();
} else {
    // Default to first lesson of first module
    $current_lesson = $modulos[0]->lecciones[0] ?? null;
}
?>

<div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
    
    <!-- SIDEBAR: Curriculum -->
    <aside class="w-full lg:w-96 bg-white border-r border-gray-200 overflow-y-auto lg:h-[calc(100vh-80px)] sticky top-20 z-30">
        <div class="p-8 border-b border-gray-100 bg-brand-900 text-white">
            <h4 class="font-black text-xl leading-tight mb-2"><?php echo $curso->titulo; ?></h4>
            <div class="flex items-center text-xs text-green-400 font-bold uppercase tracking-widest">
                <i class="fas fa-tasks mr-2"></i> Progreso: 0%
            </div>
        </div>

        <div class="p-4 space-y-4">
            <?php foreach($modulos as $mod): ?>
            <div class="space-y-2">
                <h5 class="px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400"><?php echo $mod->nombre; ?></h5>
                <?php foreach($mod->lecciones as $lec): ?>
                <a href="?id=<?php echo $curso_id; ?>&leccion_id=<?php echo $lec->id; ?>" 
                   class="flex items-center p-4 rounded-2xl transition-all group <?php echo $current_lesson && $current_lesson->id == $lec->id ? 'bg-brand-50 border-l-4 border-brand-700' : 'hover:bg-gray-50'; ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-4 <?php echo $current_lesson && $current_lesson->id == $lec->id ? 'bg-brand-700 text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-brand-100 group-hover:text-brand-700'; ?>">
                        <i class="fas fa-play text-[10px]"></i>
                    </div>
                    <span class="text-sm font-bold <?php echo $current_lesson && $current_lesson->id == $lec->id ? 'text-brand-900' : 'text-gray-600 group-hover:text-gray-900'; ?>">
                        <?php echo $lec->titulo; ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- MAIN AREA: Video & Content -->
    <main class="flex-grow p-4 md:p-12">
        <?php if($current_lesson): ?>
            <div class="max-w-5xl mx-auto space-y-12">
                
                <!-- Video Player -->
                <?php if($current_lesson->video_url): ?>
                    <?php 
                        // Simple YouTube URL to Embed conversion
                        $v_url = $current_lesson->video_url;
                        if(strpos($v_url, 'youtube.com') !== false) {
                            parse_str(parse_url($v_url, PHP_URL_QUERY), $vars);
                            $v_id = $vars['v'] ?? '';
                        } elseif(strpos($v_url, 'youtu.be') !== false) {
                            $v_id = ltrim(parse_url($v_url, PHP_URL_PATH), '/');
                        } else { $v_id = $v_url; }
                    ?>
                    <div class="bg-black rounded-[3rem] overflow-hidden shadow-2xl aspect-video border-[12px] border-white ring-1 ring-gray-100">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/<?php echo $v_id; ?>?rel=0&showinfo=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <!-- Header Info -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h2 class="text-4xl font-black text-gray-900 mb-2"><?php echo $current_lesson->titulo; ?></h2>
                        <span class="inline-block bg-brand-100 text-brand-700 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-md">Materia Técnica JLC</span>
                    </div>
                    <?php if($current_lesson->material_pdf): ?>
                    <a href="uploads/cursos/<?php echo $current_lesson->material_pdf; ?>" target="_blank" class="flex items-center bg-red-600 text-white px-8 py-4 rounded-2xl font-black shadow-xl hover:bg-red-700 transition-all hover:-translate-y-1">
                        <i class="fas fa-file-pdf mr-3 text-xl"></i> DESCARGAR MATERIAL PDF
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Text Content -->
                <div class="bg-white p-12 rounded-[3rem] shadow-xl border border-gray-100">
                    <h4 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-widest border-b pb-4">Instrucciones y Notas</h4>
                    <div class="prose max-w-none text-gray-600 leading-relaxed text-lg">
                        <?php echo nl2br($current_lesson->contenido); ?>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="flex justify-between items-center pt-12 border-t">
                    <button class="flex items-center text-gray-400 font-bold hover:text-brand-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Anterior
                    </button>
                    <button class="flex items-center bg-brand-700 text-white px-10 py-4 rounded-2xl font-black shadow-xl hover:bg-brand-800 transition-all">
                        SIGUIENTE LECCIÓN <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>

            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center h-full text-center p-20">
                <i class="fas fa-video-slash text-8xl text-gray-200 mb-8"></i>
                <h2 class="text-3xl font-black text-gray-400">Este curso aún no tiene lecciones publicadas.</h2>
                <p class="text-gray-400 mt-4">Vuelve pronto para ver el contenido académico.</p>
            </div>
        <?php endif; ?>
    </main>

</div>

<?php include_once 'includes/footer.php'; ?>
