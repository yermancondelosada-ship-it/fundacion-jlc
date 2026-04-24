<?php 
ob_start();
require_once '../config/config.php';
require_once '../config/db.php'; 

$page_title = "Gestor de Blog Académico";
$db = Database::getInstance();

$message = "";

// Ensure table exists
try {
    $db->query("CREATE TABLE IF NOT EXISTS blog_posts (id INT AUTO_INCREMENT PRIMARY KEY, titulo VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL UNIQUE, contenido TEXT, resumen TEXT, imagen_destacada VARCHAR(255), autor VARCHAR(100), categoria VARCHAR(50), fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, estado ENUM('publicado', 'borrador') DEFAULT 'publicado')");
} catch (Exception $e) {}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $titulo = $_POST['titulo'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
    $contenido = $_POST['contenido'];
    $resumen = $_POST['resumen'];
    $autor = $_POST['autor'];
    $categoria = $_POST['categoria'];
    $estado = $_POST['estado'];
    
    $imagen = $_POST['imagen_actual'] ?? '';
    if (isset($_FILES['imagen_destacada']) && $_FILES['imagen_destacada']['error'] == 0) {
        $ext = pathinfo($_FILES['imagen_destacada']['name'], PATHINFO_EXTENSION);
        $filename = "blog_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], "../uploads/img/" . $filename)) {
            $imagen = $filename;
        }
    }

    if ($_POST['action'] == 'add') {
        $stmt = $db->prepare("INSERT INTO blog_posts (titulo, slug, contenido, resumen, imagen_destacada, autor, categoria, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $slug, $contenido, $resumen, $imagen, $autor, $categoria, $estado]);
        header("Location: blog_posts.php?success=add"); exit;
    } elseif ($_POST['action'] == 'edit') {
        $id = $_POST['id'];
        $stmt = $db->prepare("UPDATE blog_posts SET titulo=?, slug=?, contenido=?, resumen=?, imagen_destacada=?, autor=?, categoria=?, estado=? WHERE id=?");
        $stmt->execute([$titulo, $slug, $contenido, $resumen, $imagen, $autor, $categoria, $estado, $id]);
        header("Location: blog_posts.php?success=edit"); exit;
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: blog_posts.php?success=delete"); exit;
}

// Success Messages
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'add') $message = "Artículo publicado con éxito.";
    if ($_GET['success'] == 'edit') $message = "Artículo actualizado correctamente.";
    if ($_GET['success'] == 'delete') $message = "Artículo eliminado.";
}

// Fetch Posts
$posts = $db->query("SELECT * FROM blog_posts ORDER BY fecha_publicacion DESC")->fetchAll();

include_once 'includes/admin_header.php'; 
?>

<?php if($message): ?>
    <div class="bg-brand-50 border-l-4 border-brand-500 text-brand-700 p-4 rounded-xl mb-8 shadow-sm">
        <i class="fas fa-check-circle mr-2"></i> <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Form Section -->
<div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100 mb-12">
    <div class="p-8 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-900">Redactar Nuevo Artículo</h3>
    </div>
    <form action="blog_posts.php" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
        <input type="hidden" name="action" value="add" id="formAction">
        <input type="hidden" name="id" id="postId">
        <input type="hidden" name="imagen_actual" id="postImageActual">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Título del Artículo</label>
                <input type="text" name="titulo" id="inputTitulo" required class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Autor</label>
                <input type="text" name="autor" id="inputAutor" value="<?php echo $_SESSION['user_nombre']; ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                <select name="categoria" id="inputCategoria" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                    <option value="Tecnología">Tecnología</option>
                    <option value="Educación">Educación</option>
                    <option value="Ambiental">Ambiental</option>
                    <option value="Turismo">Turismo</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Resumen (Introducción)</label>
                <textarea name="resumen" id="inputResumen" rows="2" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Contenido Completo (HTML permitido)</label>
                <textarea name="contenido" id="inputContenido" rows="8" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Imagen Destacada</label>
                <input type="file" name="imagen_destacada" class="text-sm text-gray-500 file:bg-brand-50 file:text-brand-700 file:rounded-full file:px-4 file:py-2 file:border-none font-bold">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Estado</label>
                <select name="estado" id="inputEstado" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-brand-500">
                    <option value="publicado">Publicado</option>
                    <option value="borrador">Borrador</option>
                </select>
            </div>
        </div>
        <div class="pt-6">
            <button type="submit" class="bg-brand-700 text-white px-12 py-4 rounded-2xl font-bold shadow-xl hover:bg-brand-800 transition-all transform hover:-translate-y-1">
                Publicar Ahora
            </button>
            <button type="button" onclick="resetForm()" class="ml-4 text-gray-500 font-bold">Cancelar</button>
        </div>
    </form>
</div>

<!-- List Section -->
<div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
    <div class="p-8 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-900">Artículos Publicados</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest font-black">
                <tr>
                    <th class="px-8 py-4">Imagen</th>
                    <th class="px-8 py-4">Título</th>
                    <th class="px-8 py-4">Autor/Fecha</th>
                    <th class="px-8 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($posts as $post): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-4">
                        <img src="<?php echo $post->imagen_destacada ? '../uploads/img/'.$post->imagen_destacada : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=100&q=80'; ?>" 
                             class="w-16 h-12 object-cover rounded-lg shadow-sm">
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-gray-900"><?php echo $post->titulo; ?></p>
                        <span class="text-[9px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-md font-black uppercase"><?php echo $post->categoria; ?></span>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-sm text-gray-700 font-medium"><?php echo $post->autor; ?></p>
                        <p class="text-[10px] text-gray-400"><?php echo date('d M, Y', strtotime($post->fecha_publicacion)); ?></p>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center justify-center space-x-3">
                            <button onclick="editPost(<?php echo htmlspecialchars(json_encode($post)); ?>)" class="p-2 bg-brand-50 text-brand-700 rounded-lg hover:bg-brand-100 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete_id=<?php echo $post->id; ?>" onclick="return confirm('¿Eliminar artículo?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
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
    function editPost(post) {
        document.getElementById('formAction').value = 'edit';
        document.getElementById('postId').value = post.id;
        document.getElementById('postImageActual').value = post.imagen_destacada;
        document.getElementById('inputTitulo').value = post.titulo;
        document.getElementById('inputAutor').value = post.autor;
        document.getElementById('inputCategoria').value = post.categoria;
        document.getElementById('inputResumen').value = post.resumen;
        document.getElementById('inputContenido').value = post.contenido;
        document.getElementById('inputEstado').value = post.estado;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('formAction').value = 'add';
        document.getElementById('postId').value = '';
        document.getElementById('postImageActual').value = '';
        document.getElementById('inputTitulo').value = '';
        document.getElementById('inputAutor').value = '<?php echo $_SESSION['user_nombre']; ?>';
        document.getElementById('inputCategoria').value = 'Tecnología';
        document.getElementById('inputResumen').value = '';
        document.getElementById('inputContenido').value = '';
        document.getElementById('inputEstado').value = 'publicado';
    }
</script>

<?php 
$out = ob_get_clean();
echo $out;
include_once 'includes/admin_footer.php'; 
?>
