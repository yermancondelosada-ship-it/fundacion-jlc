<?php 
ob_start();
require_once '../config/config.php';
require_once '../config/db.php';

$page_title = "Validación de Pagos QR";
$db = Database::getInstance();
$message = "";

// 1. DATABASE LOGIC & PROCESSING (MUST BE BEFORE ANY HTML)
if (isset($_POST['action'])) {
    $inscripcion_id = $_POST['id'];
    $nuevo_estado = ($_POST['action'] == 'aprobar') ? 'completado' : 'rechazado';
    $observaciones = $_POST['observaciones'] ?? '';

    try {
        $stmt = $db->prepare("UPDATE inscripciones SET estado = ?, observaciones_admin = ? WHERE id = ?");
        $stmt->execute([$nuevo_estado, $observaciones, $inscripcion_id]);
        header("Location: pagos.php?success=1");
        exit;
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch Pending Payments
$pagos = $db->query("
    SELECT i.*, u.nombre_completo as estudiante, c.titulo as curso 
    FROM inscripciones i
    JOIN users u ON i.user_id = u.id
    JOIN cursos c ON i.curso_id = c.id
    WHERE i.estado = 'pendiente'
    ORDER BY i.fecha_inscripcion ASC
")->fetchAll();

// 2. INCLUDE HEADER (AFTER LOGIC)
include_once 'includes/admin_header.php'; 
?>

<?php if(isset($_GET['success'])): ?>
    <div class="bg-green-500 text-white p-6 rounded-2xl mb-8 shadow-xl font-bold flex items-center animate-bounce">
        <i class="fas fa-check-circle mr-3 text-2xl"></i> ¡Acción realizada correctamente!
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 gap-8">
    <?php foreach ($pagos as $pago): ?>
    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100 flex flex-col md:flex-row">
        <!-- Receipt Image -->
        <div class="md:w-1/3 bg-gray-100 relative group">
            <img src="../uploads/comprobantes/<?php echo $pago->comprobante_img; ?>" 
                 class="w-full h-full object-contain max-h-[400px]" alt="Comprobante">
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <a href="../uploads/comprobantes/<?php echo $pago->comprobante_img; ?>" target="_blank" class="bg-white text-brand-700 px-4 py-2 rounded-lg font-bold">Ver Ampliado</a>
            </div>
        </div>
        
        <!-- Info & Actions -->
        <div class="md:w-2/3 p-10 flex flex-col">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-orange-500 bg-orange-50 px-3 py-1 rounded-full mb-3 inline-block">Pendiente de Validación</span>
                    <h3 class="text-2xl font-bold text-gray-900"><?php echo $pago->curso; ?></h3>
                    <p class="text-gray-500 font-medium">Estudiante: <span class="text-brand-700"><?php echo $pago->estudiante; ?></span></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Fecha de envío</p>
                    <p class="font-bold text-gray-700"><?php echo date('d/m/Y H:i', strtotime($pago->fecha_inscripcion)); ?></p>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-2xl mb-8 border border-gray-100">
                <p class="text-sm text-gray-600 mb-2 font-bold uppercase tracking-wider">Detalles del Pago:</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Método</p>
                        <p class="font-bold text-gray-800"><?php echo $pago->metodo_pago; ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Inscripción ID</p>
                        <p class="font-bold text-gray-800">#<?php echo $pago->id; ?></p>
                    </div>
                </div>
            </div>

            <form action="pagos.php" method="POST" class="mt-auto space-y-4">
                <input type="hidden" name="id" value="<?php echo $pago->id; ?>">
                <textarea name="observaciones" placeholder="Notas opcionales para el estudiante..." class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-brand-500"></textarea>
                <div class="flex space-x-4">
                    <button type="submit" name="action" value="aprobar" class="flex-grow bg-brand-700 text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-brand-800 transition-all flex items-center justify-center">
                        <i class="fas fa-check mr-2"></i> Aprobar Pago
                    </button>
                    <button type="submit" name="action" value="rechazar" class="bg-red-50 text-red-600 font-bold px-8 py-4 rounded-2xl hover:bg-red-100 transition-all">
                        Rechazar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if(empty($pagos)): ?>
    <div class="bg-white rounded-[3rem] p-20 text-center shadow-xl border border-gray-100">
        <div class="w-20 h-20 bg-brand-50 text-brand-700 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fas fa-check-double"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">¡Todo al día!</h3>
        <p class="text-gray-500">No hay pagos pendientes de validación en este momento.</p>
    </div>
    <?php endif; ?>
</div>

<?php 
$out = ob_get_clean();
echo $out;
include_once 'includes/admin_footer.php'; 
?>
