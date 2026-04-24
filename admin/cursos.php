<?php 
$page_title = "Gestión de Cursos";
include_once 'includes/admin_header.php'; 

$message = "";

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $titulo = $_POST['titulo'];
    $resumen = $_POST['resumen'];
    $precio = $_POST['precio'];
    $estado = $_POST['estado'];
    
    $imagen = $_POST['imagen_actual'] ?? '';
    
    // File Upload
    if (isset($_FILES['imagen_portada']) && $_FILES['imagen_portada']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen_portada']['name'], PATHINFO_EXTENSION);
        $filename = time() . "_" . uniqid() . "." . $ext;
        $target = "../uploads/cursos/" . $filename;
        
        if (move_uploaded_file($_FILES['imagen_portada']['tmp_name'], $target)) {
            $imagen = $filename;
        }
    }

    if ($_POST['action'] == 'add') {
        $stmt = $db->prepare("INSERT INTO cursos (titulo, resumen, precio, imagen_portada, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $resumen, $precio, $imagen, $estado]);
        $message = "Curso agregado correctamente.";
    } elseif ($_POST['action'] == 'edit') {
        $id = $_POST['id'];
        $stmt = $db->prepare("UPDATE cursos SET titulo=?, resumen=?, precio=?, imagen_portada=?, estado=? WHERE id=?");
        $stmt->execute([$titulo, $resumen, $precio, $imagen, $estado, $id]);
        $message = "Curso actualizado correctamente.";
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $db->prepare("DELETE FROM cursos WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    $message = "Curso eliminado.";
}

// Fetch Courses
$cursos = $db->query("SELECT * FROM cursos ORDER BY id DESC")->fetchAll();
?>

<?php if($message): ?>
    <div class="bg-brand-50 border-l-4 border-brand-500 text-brand-700 p-4 rounded-xl mb-8 shadow-sm">
        <i class="fas fa-check-circle mr-2"></i> <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Form Section (Add New) -->
<div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100 mb-12">
    <div class="p-8 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-900">Agregar / Editar Curso</h3>
    </div>
    <form action="cursos.php" method="POST" enctype="multipart/form-data" class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" name="action" value="add" id="formAction">
        <input type="hidden" name="id" id="courseId">
        <input type="hidden" name="imagen_actual" id="courseImageActual">

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Título del Curso</label>
                <input type="text" name="titulo" id="inputTitulo" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 transition-all">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Precio (COP)</label>
                <input type="number" name="precio" id="inputPrecio" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 transition-all">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Estado</label>
                <select name="estado" id="inputEstado" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Resumen / Descripción corta</label>
                <textarea name="resumen" id="inputResumen" rows="4" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500 transition-all"></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Imagen de Portada</label>
                <input type="file" name="imagen_portada" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
            </div>
        </div>
        <div class="md:col-span-2 pt-4">
            <button type="submit" class="bg-brand-700 text-white px-12 py-4 rounded-2xl font-bold shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                Guardar Curso
            </button>
            <button type="button" onclick="resetForm()" class="ml-4 text-gray-500 font-bold">Cancelar</button>
        </div>
    </form>
</div>

<!-- List Section -->
<div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
    <div class="p-8 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-900">Cursos Disponibles</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest font-black">
                <tr>
                    <th class="px-8 py-4">Portada</th>
                    <th class="px-8 py-4">Curso</th>
                    <th class="px-8 py-4">Precio</th>
                    <th class="px-8 py-4">Estado</th>
                    <th class="px-8 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($cursos as $curso): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-4">
                        <img src="<?php echo $curso->imagen_portada ? '../uploads/cursos/'.$curso->imagen_portada : 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?auto=format&fit=crop&w=100&q=80'; ?>" 
                             class="w-16 h-12 object-cover rounded-lg shadow-sm" alt="Portada">
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-gray-900"><?php echo $curso->titulo; ?></p>
                        <p class="text-xs text-gray-400 line-clamp-1"><?php echo $curso->resumen; ?></p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-brand-700">$<?php echo number_format($curso->precio, 0, ',', '.'); ?></p>
                    </td>
                    <td class="px-8 py-6">
                        <span class="<?php echo $curso->estado == 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?> px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                            <?php echo $curso->estado; ?>
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center justify-center space-x-3">
                             <a href="gestionar_clases.php?curso_id=<?php echo $curso->id; ?>" class="p-2 bg-brand-700 text-white rounded-lg hover:bg-brand-800 transition-colors" title="Gestionar Contenido">
                                <i class="fas fa-layer-group"></i>
                             </a>
                             <button onclick="editCourse(<?php echo htmlspecialchars(json_encode($curso)); ?>)" class="p-2 bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete_id=<?php echo $curso->id; ?>" onclick="return confirm('¿Eliminar curso?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function editCourse(curso) {
        document.getElementById('formAction').value = 'edit';
        document.getElementById('courseId').value = curso.id;
        document.getElementById('courseImageActual').value = curso.imagen_portada;
        document.getElementById('inputTitulo').value = curso.titulo;
        document.getElementById('inputPrecio').value = curso.precio;
        document.getElementById('inputEstado').value = curso.estado;
        document.getElementById('inputResumen').value = curso.resumen;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('formAction').value = 'add';
        document.getElementById('courseId').value = '';
        document.getElementById('courseImageActual').value = '';
        document.getElementById('inputTitulo').value = '';
        document.getElementById('inputPrecio').value = '';
        document.getElementById('inputEstado').value = 'activo';
        document.getElementById('inputResumen').value = '';
    }
</script>

<?php include_once 'includes/admin_footer.php'; ?>
