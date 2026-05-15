<?php
require_once 'config/config.php';
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: aula-virtual.php");
    exit;
}

$curso_id = (int) ($_GET['id'] ?? 0);
if ($curso_id <= 0) {
    header("Location: aula-virtual.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

// 1. SECURITY CHECK
$stmt = $db->prepare("SELECT estado FROM inscripciones WHERE user_id = ? AND curso_id = ? AND estado = 'aprobado'");
$stmt->execute([$user_id, $curso_id]);
if (!$stmt->fetch()) {
    header("Location: aula-virtual.php?error=acceso_denegado");
    exit;
}

include_once 'includes/header.php';

// VERIFICACIÓN DE BASE DE DATOS (MIGRACIÓN)
try {
    $db->query("SELECT 1 FROM progreso_estudiantes LIMIT 1");
} catch (Exception $e) {
    echo "<div class='p-20 text-center'>
            <div class='bg-red-50 text-red-600 p-10 rounded-3xl border border-red-100 shadow-xl inline-block max-w-2xl'>
                <i class='fas fa-database text-5xl mb-4'></i>
                <h2 class='text-2xl font-black mb-2'>ERROR DE BASE DE DATOS</h2>
                <p class='font-bold mb-4'>Parece que falta la tabla de progreso. Por favor, ejecuta el archivo de migración en tu servidor:</p>
                <code class='bg-red-100 px-4 py-2 rounded-lg block mb-4 text-sm'>tudominio.com/admin/migrate.php</code>
                <p class='text-xs opacity-70'>Error: " . $e->getMessage() . "</p>
            </div>
          </div>";
    include_once 'includes/footer.php';
    exit;
}

// 2. FETCH DATA
$stmt = $db->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->execute([$curso_id]);
$curso = $stmt->fetch();
if (!$curso) {
    echo "<div class='min-h-screen flex items-center justify-center bg-gray-50 p-20 text-center font-black text-gray-400'>
            <div><i class='fas fa-exclamation-circle text-6xl mb-4'></i><br>ERROR: Curso no encontrado.</div>
          </div>";
    include_once 'includes/footer.php';
    exit;
}

$modulos_stmt = $db->prepare("SELECT * FROM modulos WHERE curso_id = ? ORDER BY orden ASC");
$modulos_stmt->execute([$curso_id]);
$modulos = $modulos_stmt->fetchAll() ?: [];

foreach ($modulos as &$mod) {
    $stmt = $db->prepare("SELECT * FROM lecciones WHERE modulo_id = ? ORDER BY orden ASC");
    $stmt->execute([$mod->id]);
    $mod->lecciones = $stmt->fetchAll() ?: [];
}
unset($mod);

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
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Outfit:wght@300;400;600;900&display=swap');

    :root {
        --magic-cyan: #06b6d4;
        --magic-gold: #fbbf24;
        --magic-locked: #94a3b8;
    }

    body {
        overflow-x: hidden;
        background: #0f172a;
    }

    .map-world {
        min-height: 100vh;
        background-image: url('uploads/gamificacion/map_bg.png');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        position: relative;
        padding-top: 100px;
        padding-bottom: 200px;
    }

    /* Hero Widget */
    .hero-widget {
        position: fixed;
        top: 120px;
        right: 40px;
        z-index: 100;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(12px);
        border: 2px solid rgba(255, 255, 255, 0.1);
        padding: 1.2rem;
        border-radius: 2.5rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: slideInRight 1s cubic-bezier(0.16, 1, 0.3, 1);
        border-left: 4px solid var(--magic-cyan);
    }

    @keyframes slideInRight {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .avatar-ring {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        padding: 4px;
        background: linear-gradient(135deg, var(--magic-cyan), var(--magic-gold));
        position: relative;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.4);
    }

    .avatar-ring img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #0f172a;
    }

    .level-badge {
        position: absolute;
        bottom: -5px;
        right: -5px;
        background: var(--magic-gold);
        color: #000;
        font-weight: 900;
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 12px;
        border: 2px solid #0f172a;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .exp-container {
        width: 160px;
    }

    .exp-bar-bg {
        height: 10px;
        background: rgba(255,255,255,0.1);
        border-radius: 20px;
        overflow: hidden;
        margin-top: 6px;
        border: 1px solid rgba(255,255,255,0.05);
    }

    .exp-bar-fill {
        height: 100%;
        background: linear-gradient(to right, var(--magic-cyan), #22d3ee);
        box-shadow: 0 0 15px var(--magic-cyan);
        transition: width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Island Nodes */
    .campaign-path {
        max-width: 1100px;
        margin: 0 auto;
        position: relative;
    }

    .island-node {
        position: relative;
        width: 320px;
        height: 320px;
        margin-bottom: -60px;
        z-index: 10;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .island-node:hover {
        transform: translateY(-20px) scale(1.08);
    }

    .island-img {
        width: 100%;
        height: 100%;
        background-image: url('uploads/gamificacion/island_portal.png');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
        animation: float 5s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(1deg); }
    }

    /* State: Locked */
    .island-node.locked .island-img {
        filter: grayscale(1) brightness(0.6) contrast(1.2);
    }
    .island-node.locked .portal-glow {
        display: none;
    }
    .lock-chain {
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 3.5rem;
        color: rgba(255,255,255,0.6);
        z-index: 20;
        text-shadow: 0 0 25px rgba(0,0,0,0.9);
        filter: drop-shadow(0 0 10px rgba(0,0,0,0.5));
    }

    /* State: Completed */
    .island-node.completed .portal-glow {
        background: radial-gradient(circle, var(--magic-gold) 0%, transparent 70%);
        opacity: 0.5;
    }
    .completion-badge {
        position: absolute;
        top: 25%;
        right: 25%;
        background: linear-gradient(135deg, var(--magic-gold), #d97706);
        color: #fff;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        box-shadow: 0 0 25px var(--magic-gold);
        z-index: 30;
        border: 3px solid white;
        animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes popIn {
        from { transform: scale(0) rotate(-45deg); opacity: 0; }
        to { transform: scale(1) rotate(0); opacity: 1; }
    }

    /* State: Current (Active) */
    .island-node.current .portal-glow {
        background: radial-gradient(circle, var(--magic-cyan) 0%, transparent 75%);
        opacity: 0.9;
        animation: pulseGlow 2.5s infinite;
    }

    @keyframes pulseGlow {
        0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.9; box-shadow: 0 0 30px var(--magic-cyan); }
        50% { transform: translate(-50%, -50%) scale(1.4); opacity: 0.3; box-shadow: 0 0 60px var(--magic-cyan); }
    }

    .portal-glow {
        position: absolute;
        top: 48%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 140px;
        height: 140px;
        border-radius: 50%;
        z-index: 5;
        pointer-events: none;
    }

    .island-label {
        position: absolute;
        bottom: 5%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15, 23, 42, 0.9);
        color: white;
        padding: 0.7rem 1.8rem;
        border-radius: 1.2rem;
        white-space: nowrap;
        font-family: 'Cinzel', serif;
        font-size: 0.95rem;
        border: 2px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.6);
        transition: all 0.3s ease;
    }

    .island-node:hover .island-label {
        border-color: var(--magic-cyan);
        box-shadow: 0 0 20px rgba(6, 182, 212, 0.3);
    }

    /* Staggered Path Logic */
    .node-container:nth-child(4n+1) .island-node { margin-left: 5%; }
    .node-container:nth-child(4n+2) .island-node { margin-left: 35%; }
    .node-container:nth-child(4n+3) .island-node { margin-left: 60%; }
    .node-container:nth-child(4n+4) .island-node { margin-left: 30%; }

    /* SVG Path Overlay */
    #path-svg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .energy-path {
        fill: none;
        stroke: var(--magic-cyan);
        stroke-width: 5;
        stroke-linecap: round;
        stroke-dasharray: 12, 18;
        animation: flow 25s linear infinite;
        opacity: 0.35;
        filter: drop-shadow(0 0 8px var(--magic-cyan));
    }

    @keyframes flow {
        from { stroke-dashoffset: 300; }
        to { stroke-dashoffset: 0; }
    }

    .course-title-banner {
        text-align: center;
        margin-bottom: 80px;
        position: relative;
    }

    .course-title-banner h1 {
        font-family: 'Cinzel', serif;
        font-size: 4.5rem;
        color: white;
        text-shadow: 0 0 40px rgba(6, 182, 212, 0.6), 0 8px 20px rgba(0,0,0,0.9);
        letter-spacing: 6px;
        text-transform: uppercase;
    }

    /* Responsiveness */
    @media (max-width: 1024px) {
        .campaign-path { max-width: 100%; padding: 0 20px; }
        .island-node { width: 260px; height: 260px; }
        .node-container:nth-child(n) .island-node { margin-left: auto !important; margin-right: auto !important; }
    }

    @media (max-width: 768px) {
        .hero-widget {
            top: auto;
            bottom: 30px;
            right: 20px;
            left: 20px;
            width: auto;
            animation: slideInUp 1s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideInUp {
            from { transform: translateY(120%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .course-title-banner h1 { font-size: 2.2rem; }
        .island-node { width: 220px; height: 220px; margin-bottom: 20px; }
        .island-label { padding: 0.5rem 1.2rem; font-size: 0.8rem; }
    }
</style>

<div class="map-world">
    <!-- Hero Widget -->
    <div class="hero-widget">
        <div class="avatar-ring">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_nombre']); ?>&background=random" alt="Avatar">
            <?php 
                $num_completadas = count($completadas);
                $nivel = floor($num_completadas / 2) + 1;
                $progreso_total = count($todas_lecciones) > 0 ? ($num_completadas / count($todas_lecciones)) * 100 : 0;
            ?>
            <div class="level-badge">LV <?php echo $nivel; ?></div>
        </div>
        <div class="text-white">
            <div class="text-[10px] font-black uppercase tracking-[0.2em] opacity-50 mb-1">Misión de Aprendizaje</div>
            <div class="font-black text-xl tracking-tight"><?php echo explode(' ', $_SESSION['user_nombre'])[0]; ?></div>
            <div class="exp-container mt-2">
                <div class="flex justify-between text-[10px] font-black mb-1 opacity-80">
                    <span>PROGRESS</span>
                    <span class="text-magic-cyan"><?php echo round($progreso_total); ?>%</span>
                </div>
                <div class="exp-bar-bg">
                    <div class="exp-bar-fill" style="width: <?php echo $progreso_total; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="campaign-path">
        <!-- SVG Container for lines -->
        <svg id="path-svg"></svg>

        <div class="course-title-banner">
            <div class="text-magic-cyan font-black tracking-[0.4em] text-xs mb-3 uppercase opacity-80">Mapa de Campaña Estudiantil</div>
            <h1><?php echo $curso->titulo; ?></h1>
        </div>

        <div class="nodes-wrapper relative">
            <?php 
            $prev_was_completed = true;
            $node_counter = 0;
            foreach ($modulos as $mod): 
                foreach ($mod->lecciones as $lec):
                    $is_completed = in_array($lec->id, $completadas);
                    $is_current = false;
                    if (!$is_completed && $prev_was_completed) {
                        $is_current = true;
                    }
                    $is_locked = !$is_completed && !$is_current && !$prev_was_completed;
                    
                    $state_class = $is_completed ? 'completed' : ($is_current ? 'current' : 'locked');
                    $node_counter++;
            ?>
                <div class="node-container">
                    <div class="island-node <?php echo $state_class; ?>" id="node-<?php echo $node_counter; ?>" 
                         onclick="<?php echo $is_locked ? 'alert(\'¡Este portal está sellado! Completa el anterior para avanzar.\')' : "window.location='ver-curso.php?id=$curso_id&leccion_id=$lec->id'"; ?>">
                        
                        <div class="island-img">
                            <div class="portal-glow"></div>
                            
                            <?php if($is_locked): ?>
                                <div class="lock-chain">
                                    <i class="fas fa-lock text-slate-400"></i>
                                </div>
                            <?php endif; ?>

                            <?php if($is_completed): ?>
                                <div class="completion-badge">
                                    <i class="fas fa-crown"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="island-label">
                            <span class="text-magic-cyan mr-2 font-black"><?php echo str_pad($node_counter, 2, '0', STR_PAD_LEFT); ?></span>
                            <?php echo $lec->titulo; ?>
                        </div>
                    </div>
                </div>
            <?php 
                $prev_was_completed = $is_completed;
                endforeach; 
            endforeach; 
            ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const svg = document.getElementById('path-svg');
        const nodes = document.querySelectorAll('.island-node');
        const container = document.querySelector('.campaign-path');
        
        function drawPath() {
            if (nodes.length < 2) return;
            
            let pathD = '';
            const containerRect = container.getBoundingClientRect();
            
            nodes.forEach((node, index) => {
                const r1 = node.getBoundingClientRect();
                const x1 = r1.left + r1.width/2 - containerRect.left;
                const y1 = r1.top + r1.height/2 - containerRect.top;

                if (index === 0) {
                    pathD += `M ${x1} ${y1} `;
                } else {
                    const prevNode = nodes[index - 1];
                    const pr = prevNode.getBoundingClientRect();
                    const px = pr.left + pr.width/2 - containerRect.left;
                    const py = pr.top + pr.height/2 - containerRect.top;
                    
                    const midY = (py + y1) / 2;
                    pathD += `C ${px} ${midY}, ${x1} ${midY}, ${x1} ${y1} `;
                }
            });
            
            svg.innerHTML = `
                <path d="${pathD}" class="energy-path" />
                <path d="${pathD}" style="stroke: white; stroke-width: 1; opacity: 0.15; fill: none;" />
            `;
            
            // Adjust SVG height to content
            const lastNode = nodes[nodes.length - 1].getBoundingClientRect();
            svg.style.height = (lastNode.bottom - containerRect.top + 100) + 'px';
        }

        // Draw initial and on resize
        setTimeout(drawPath, 800);
        window.addEventListener('resize', drawPath);
        
        // Add subtle parallax to background
        window.addEventListener('scroll', () => {
            const world = document.querySelector('.map-world');
            world.style.backgroundPositionY = -(window.scrollY * 0.2) + 'px';
        });
    });
</script>

<?php include_once 'includes/footer.php'; ?>