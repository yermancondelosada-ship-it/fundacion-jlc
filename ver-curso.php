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
$stmt = $db->prepare("SELECT estado FROM inscripciones WHERE user_id = ? AND curso_id = ? AND estado = 'aprobado'");
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

// 3. HANDLE PROGRESS (Mark as completed)
if (isset($_POST['mark_completed'])) {
    $lec_to_complete = $_POST['leccion_id'];
    $stmt = $db->prepare("INSERT IGNORE INTO progreso_estudiantes (user_id, curso_id, leccion_id) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $curso_id, $lec_to_complete]);
    
    // Redirect to next lesson if available
    if (isset($_POST['next_leccion_id']) && $_POST['next_leccion_id'] > 0) {
        header("Location: ?id=$curso_id&leccion_id=" . $_POST['next_leccion_id']);
    } else {
        header("Location: ?id=$curso_id&leccion_id=$lec_to_complete&msg=completado");
    }
    exit;
}

// 4. FETCH PROGRESS
$stmt = $db->prepare("SELECT leccion_id FROM progreso_estudiantes WHERE user_id = ? AND curso_id = ?");
$stmt->execute([$user_id, $curso_id]);
$completadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 5. CURRENT LESSON & NAVIGATION
$todas_lecciones = [];
foreach ($modulos as $mod) {
    foreach ($mod->lecciones as $lec) {
        $todas_lecciones[] = $lec;
    }
}

$current_index = 0;
if ($leccion_id > 0) {
    foreach ($todas_lecciones as $index => $lec) {
        if ($lec->id == $leccion_id) {
            $current_lesson = $lec;
            $current_index = $index;
            break;
        }
    }
} else {
    $current_lesson = $todas_lecciones[0] ?? null;
}

$next_lesson = $todas_lecciones[$current_index + 1] ?? null;
$prev_lesson = $todas_lecciones[$current_index - 1] ?? null;

// Progress Percentage
$total_lecciones = count($todas_lecciones);
$total_completadas = count($completadas);
$porcentaje = $total_lecciones > 0 ? round(($total_completadas / $total_lecciones) * 100) : 0;
?>

<div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
    
    <!-- SIDEBAR: Curriculum -->
    <aside class="w-full lg:w-96 bg-white border-r border-gray-200 overflow-y-auto lg:h-[calc(100vh-80px)] sticky top-20 z-30">
        <div class="p-8 border-b border-gray-100 bg-brand-900 text-white">
            <h4 class="font-black text-xl leading-tight mb-2"><?php echo $curso->titulo; ?></h4>
            <div class="flex items-center text-xs text-green-400 font-bold uppercase tracking-widest">
                <i class="fas fa-tasks mr-2"></i> Progreso: <?php echo $porcentaje; ?>%
            </div>
            <div class="w-full bg-brand-800 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="bg-green-400 h-full transition-all duration-500" style="width: <?php echo $porcentaje; ?>%"></div>
            </div>
            <a href="mapa-curso.php?id=<?php echo $curso_id; ?>" class="mt-6 block w-full text-center py-3 bg-brand-800 hover:bg-brand-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                <i class="fas fa-map mr-2"></i> Ver Mapa de Curso
            </a>
        </div>

        <div class="p-4 space-y-4">
            <?php foreach($modulos as $mod): ?>
            <div class="space-y-2">
                <h5 class="px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400"><?php echo $mod->nombre; ?></h5>
                <?php 
                $prev_was_completed = true; // First lesson is always unlocked
                foreach($mod->lecciones as $index => $lec): 
                    $is_completed = in_array($lec->id, $completadas);
                    $is_current = $current_lesson && $current_lesson->id == $lec->id;
                    $is_locked = !$is_completed && !$is_current && !$prev_was_completed;
                ?>
                <a href="<?php echo $is_locked ? '#' : '?id='.$curso_id.'&leccion_id='.$lec->id; ?>" 
                   class="flex items-center p-4 rounded-2xl transition-all group <?php echo $is_current ? 'bg-brand-50 border-l-4 border-brand-700' : ($is_locked ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'); ?>">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-4 <?php echo $is_current ? 'bg-brand-700 text-white' : ($is_completed ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'); ?>">
                        <?php if($is_completed): ?>
                            <i class="fas fa-check text-[10px]"></i>
                        <?php elseif($is_locked): ?>
                            <i class="fas fa-lock text-[10px]"></i>
                        <?php else: ?>
                            <?php if($lec->tipo == 'video'): ?>
                                <i class="fas fa-play text-[10px]"></i>
                            <?php elseif($lec->tipo == 'actividad'): ?>
                                <i class="fas fa-puzzle-piece text-[10px]"></i>
                            <?php else: ?>
                                <i class="fas fa-file-alt text-[10px]"></i>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold <?php echo $is_current ? 'text-brand-900' : 'text-gray-600'; ?>">
                            <?php echo $lec->titulo; ?>
                        </span>
                        <span class="text-[9px] uppercase tracking-widest font-black text-gray-400">
                            <?php echo $lec->tipo; ?>
                        </span>
                    </div>
                </a>
                <?php 
                    $prev_was_completed = $is_completed;
                endforeach; ?>
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
                    <?php if($prev_lesson): ?>
                    <a href="?id=<?php echo $curso_id; ?>&leccion_id=<?php echo $prev_lesson->id; ?>" class="flex items-center text-gray-400 font-bold hover:text-brand-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Anterior
                    </a>
                    <?php else: ?>
                    <div></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <input type="hidden" name="mark_completed" value="1">
                        <input type="hidden" name="leccion_id" value="<?php echo $current_lesson->id; ?>">
                        <input type="hidden" name="next_leccion_id" value="<?php echo $next_lesson ? $next_lesson->id : 0; ?>">
                        
                        <button type="submit" class="flex items-center bg-brand-700 text-white px-10 py-4 rounded-2xl font-black shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                            <?php echo $next_lesson ? 'SIGUIENTE LECCIÓN' : 'FINALIZAR CURSO'; ?> 
                            <i class="fas <?php echo $next_lesson ? 'fa-arrow-right' : 'fa-graduation-cap'; ?> ml-2"></i>
                        </button>
                    </form>
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
