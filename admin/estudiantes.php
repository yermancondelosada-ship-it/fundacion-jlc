<?php 
$page_title = "Gestión de Estudiantes";
include_once 'includes/admin_header.php'; 

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND rol = 'estudiante'");
        $stmt->execute([$delete_id]);
        echo "<script>alert('Estudiante eliminado correctamente'); window.location.href='estudiantes.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error al eliminar: " . $e->getMessage() . "');</script>";
    }
}

// Fetch Students
$students = $db->query("SELECT * FROM users WHERE rol = 'estudiante' ORDER BY fecha_registro DESC")->fetchAll();
?>

<div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
    <div class="p-8 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-gray-900">Listado de Estudiantes</h3>
            <p class="text-sm text-gray-500">Administra los usuarios registrados en la plataforma</p>
        </div>
        <span class="bg-brand-50 text-brand-700 px-4 py-2 rounded-xl font-bold text-sm">
            Total: <?php echo count($students); ?>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest font-black">
                <tr>
                    <th class="px-8 py-4">Estudiante</th>
                    <th class="px-8 py-4">Contacto</th>
                    <th class="px-8 py-4">Registro</th>
                    <th class="px-8 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($students as $student): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center font-bold">
                                <?php echo substr($student->nombre_completo, 0, 1); ?>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900"><?php echo $student->nombre_completo; ?></p>
                                <p class="text-xs text-gray-400">ID: #<?php echo $student->id; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-sm text-gray-600"><i class="far fa-envelope mr-2 opacity-50"></i><?php echo $student->email; ?></p>
                        <p class="text-sm text-gray-600"><i class="fas fa-phone mr-2 opacity-50"></i><?php echo $student->telefono ?? 'N/A'; ?></p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($student->fecha_registro)); ?></p>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center justify-center space-x-3">
                            <a href="#" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Ver Perfil">
                                <i class="fas fa-user"></i>
                            </a>
                            <a href="?delete_id=<?php echo $student->id; ?>" 
                               onclick="return confirm('¿Estás seguro de eliminar este estudiante? Esta acción es irreversible.')"
                               class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($students)): ?>
                <tr>
                    <td colspan="4" class="px-8 py-12 text-center text-gray-400">No hay estudiantes registrados aún.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once 'includes/admin_footer.php'; ?>
