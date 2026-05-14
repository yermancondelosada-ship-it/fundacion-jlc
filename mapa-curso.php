<?php
require_once 'config/config.php';
require_once 'config/db.php';
include_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: aula-virtual.php"); exit;
}

$curso_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

// 1. SECURITY CHECK
$stmt = $db->prepare("SELECT estado FROM inscripciones WHERE user_id = ? AND curso_id = ? AND estado = 'aprobado'");
$stmt->execute([$user_id, $curso_id]);
if (!$stmt->fetch()) {
    header("Location: aula-virtual.php?error=acceso_denegado"); exit;
}

// 2. FETCH DATA
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

$stmt = $db->prepare("SELECT leccion_id FROM progreso_estudiantes WHERE user_id = ? AND curso_id = ?");
$stmt->execute([$user_id, $curso_id]);
$completadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Flatten lessons for sequence
$todas_lecciones = [];
foreach ($modulos as $mod) {
    foreach ($mod->lecciones as $lec) {
        $todas_lecciones[] = $lec;
    }
}
?>

<style>
    .map-container {
        background: radial-gradient(circle at top right, #f8fafc, #f1f5f9);
        min-height: 100vh;
        padding: 4rem 1rem;
        position: relative;
        overflow: hidden;
    }

    /* The decorative path line */
    .path-line {
        position: absolute;
        top: 0;
        left: 50%;
        width: 4px;
        height: 100%;
        background: repeating-linear-gradient(
            to bottom,
            #cbd5e1 0px,
            #cbd5e1 10px,
            transparent 10px,
            transparent 20px
        );
        transform: translateX(-50%);
        z-index: 0;
    }

    .node {
        position: relative;
        z-index: 10;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .node-icon {
        width: 80px;
        height: 80px;
        border-radius: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1);
        cursor: pointer;
        background: white;
        border: 4px solid white;
    }

    .node.completed .node-icon {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        box-shadow: 0 15px 30px -10px rgba(34, 197, 94, 0.4);
    }

    .node.current .node-icon {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
        box-shadow: 0 0 0 8px rgba(14, 165, 233, 0.2);
        animation: pulse 2s infinite;
    }

    .node.locked .node-icon {
        background: #f1f5f9;
        color: #94a3b8;
        filter: grayscale(1);
        cursor: not-allowed;
    }

    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(14, 165, 233, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0); }
    }

    .module-banner {
        background: #0f172a;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 1rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.75rem;
        display: inline-block;
        margin-bottom: 2rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    .zig-zag:nth-child(even) { flex-direction: row-reverse; }

</style>

<div class="map-container">
    <div class="path-line"></div>
    
    <div class="max-w-4xl mx-auto relative">
        
        <!-- Header -->
        <div class="text-center mb-20">
            <h1 class="text-5xl font-black text-slate-900 mb-4"><?php echo $curso->titulo; ?></h1>
            <p class="text-slate-500 font-bold tracking-widest uppercase text-sm">Tu Mapa de Aprendizaje</p>
            
            <div class="mt-8 flex justify-center gap-8">
                <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
                    <span class="block text-2xl font-black text-slate-900"><?php echo count($completadas); ?>/<?php echo count($todas_lecciones); ?></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Lecciones</span>
                </div>
                <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
                    <span class="block text-2xl font-black text-brand-700"><?php echo count($modulos); ?></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Módulos</span>
                </div>
            </div>
        </div>

        <!-- Course Map Nodes -->
        <div class="space-y-32">
            <?php 
            $prev_was_completed = true;
            foreach($modulos as $mod_index => $mod): 
            ?>
            <div class="flex flex-col items-center">
                <div class="module-banner"><?php echo $mod->nombre; ?></div>
                
                <div class="grid grid-cols-1 gap-16 w-full max-w-lg">
                    <?php foreach($mod->lecciones as $lec_index => $lec): 
                        $is_completed = in_array($lec->id, $completadas);
                        // A lesson is current if it's the first non-completed one
                        $is_current = false;
                        if (!$is_completed && $prev_was_completed) {
                            $is_current = true;
                        }
                        $is_locked = !$is_completed && !$is_current && !$prev_was_completed;
                        
                        // Alternate alignment
                        $align = ($lec_index % 2 == 0) ? 'mr-auto' : 'ml-auto';
                    ?>
                    <div class="node flex items-center <?php echo $align; ?> <?php echo $is_completed ? 'completed' : ($is_current ? 'current' : 'locked'); ?>">
                        <a href="<?php echo $is_locked ? '#' : 'ver-curso.php?id='.$curso_id.'&leccion_id='.$lec->id; ?>" 
                           class="flex flex-col items-center group">
                            <div class="node-icon mb-4 hover:scale-110 transition-transform">
                                <?php if($is_completed): ?>
                                    <i class="fas fa-check"></i>
                                <?php elseif($is_locked): ?>
                                    <i class="fas fa-lock"></i>
                                <?php else: ?>
                                    <?php if($lec->tipo == 'video'): ?>
                                        <i class="fas fa-play"></i>
                                    <?php elseif($lec->tipo == 'actividad'): ?>
                                        <i class="fas fa-puzzle-piece"></i>
                                    <?php else: ?>
                                        <i class="fas fa-file-alt"></i>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs font-black text-slate-900 group-hover:text-brand-700 transition-colors bg-white/80 backdrop-blur px-3 py-1 rounded-full shadow-sm">
                                <?php echo $lec->titulo; ?>
                            </span>
                        </a>
                    </div>
                    <?php 
                        $prev_was_completed = $is_completed;
                    endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer Goal -->
        <div class="mt-40 text-center">
            <div class="w-24 h-24 bg-brand-900 text-white rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 shadow-2xl animate-bounce">
                <i class="fas fa-trophy text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900">¡Meta Final!</h3>
            <p class="text-slate-500">Completa todas las lecciones para obtener tu certificación.</p>
        </div>

    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
