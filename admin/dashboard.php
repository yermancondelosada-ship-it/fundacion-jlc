<?php
/**
 * Dashboard Administrativo - Plataforma JLC V2.0
 */
$page_title = "Resumen del Sistema";
include_once 'includes/admin_header.php';

// Consultas para las tarjetas (Métricas reales)
try {
    $total_estudiantes = $db->query("SELECT COUNT(*) as total FROM users WHERE rol = 'estudiante'")->fetch()->total;
    $total_cursos = $db->query("SELECT COUNT(*) as total FROM cursos WHERE estado = 'activo'")->fetch()->total;
    $pendientes_pago = $db->query("SELECT COUNT(*) as total FROM inscripciones WHERE estado = 'pendiente'")->fetch()->total;
    $pagos_aprobados = $db->query("SELECT COUNT(*) as total FROM inscripciones WHERE estado = 'aprobado'")->fetch()->total;
} catch (Exception $e) {
    $total_estudiantes = 0; $total_cursos = 0; $pendientes_pago = 0; $pagos_aprobados = 0;
}
?>

<!-- Welcome Banner -->
<div class="bg-brand-700 rounded-[2rem] p-8 text-white mb-8 shadow-xl relative overflow-hidden">
    <div class="relative z-10">
        <h2 class="text-3xl font-bold mb-2">¡Hola, <?php echo explode(' ', $_SESSION['user_nombre'])[0]; ?>!</h2>
        <p class="opacity-80">Aquí tienes el resumen de lo que está pasando en la Fundación hoy.</p>
    </div>
    <i class="fas fa-rocket absolute right-0 bottom-0 text-9xl text-white/10 -mb-8 -mr-8"></i>
</div>

<!-- Dashboard Grid (12 Cards) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    
    <!-- 1. Total Estudiantes -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-user-graduate"></i>
            </div>
            <span class="text-xs font-bold text-green-500">+12%</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Total Estudiantes</p>
        <h3 class="text-3xl font-bold text-gray-900"><?php echo $total_estudiantes; ?></h3>
    </div>

    <!-- 2. Cursos Activos -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-book"></i>
            </div>
            <span class="text-xs font-bold text-gray-400">Estable</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Cursos Activos</p>
        <h3 class="text-3xl font-bold text-gray-900"><?php echo $total_cursos; ?></h3>
    </div>

    <!-- 3. Pagos Pendientes -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-orange-200 bg-orange-50/30 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-clock"></i>
            </div>
            <span class="text-xs font-bold text-orange-600 animate-pulse">URGENTE</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Pendientes Aprobar</p>
        <h3 class="text-3xl font-bold text-gray-900"><?php echo $pendientes_pago; ?></h3>
    </div>

    <!-- 4. Inscripciones Totales -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-file-signature"></i>
            </div>
            <span class="text-xs font-bold text-purple-500">Nuevas</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Inscripciones Mes</p>
        <h3 class="text-3xl font-bold text-gray-900"><?php echo ($pagos_aprobados + $pendientes_pago); ?></h3>
    </div>

    <!-- 5. Mensajes de Contacto -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-brand-50 text-brand-700 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-comment-dots"></i>
            </div>
            <span class="text-xs font-bold text-brand-600">3 Nuevos</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Consultas Web</p>
        <h3 class="text-3xl font-bold text-gray-900">14</h3>
    </div>

    <!-- 6. Impacto Ambiental -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-tree"></i>
            </div>
            <span class="text-xs font-bold text-emerald-500">Meta 80%</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Árboles Plantados</p>
        <h3 class="text-3xl font-bold text-gray-900">450</h3>
    </div>

    <!-- 7. Visitas al Sitio -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-gray-50 text-gray-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-mouse-pointer"></i>
            </div>
            <span class="text-xs font-bold text-gray-400">24h</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Visitas Hoy</p>
        <h3 class="text-3xl font-bold text-gray-900">1.2k</h3>
    </div>

    <!-- 8. Alianzas Activas -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-handshake"></i>
            </div>
            <span class="text-xs font-bold text-indigo-500">+1</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Alianzas Corporativas</p>
        <h3 class="text-3xl font-bold text-gray-900">12</h3>
    </div>

    <!-- 9. Blog Académico -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm font-medium">Artículos Publicados</p>
        <h3 class="text-3xl font-bold text-gray-900">28</h3>
    </div>

    <!-- 10. Satisfacción -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-star"></i>
            </div>
            <span class="text-xs font-bold text-yellow-600">4.8/5</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Satisfacción Estudiantes</p>
        <h3 class="text-3xl font-bold text-gray-900">96%</h3>
    </div>

    <!-- 11. Capacidad Aula -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-cyan-50 text-cyan-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-microchip"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm font-medium">Recursos Digitales</p>
        <h3 class="text-3xl font-bold text-gray-900">150GB</h3>
    </div>

    <!-- 12. Soporte Técnico -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-headset"></i>
            </div>
        </div>
        <p class="text-gray-500 text-sm font-medium">Tickets Soporte</p>
        <h3 class="text-3xl font-bold text-gray-900">0</h3>
    </div>

</div>

<?php include_once 'includes/admin_footer.php'; ?>
