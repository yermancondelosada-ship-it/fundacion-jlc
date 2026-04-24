<?php 
ob_start();
require_once '../config/config.php';
require_once '../config/db.php'; 

$page_title = "Configuración Suprema JLC";
$db = Database::getInstance();

// 1. DATABASE LOGIC & PROCESSING (MUST BE BEFORE ANY HTML)
// 1. DATABASE LOGIC & PROCESSING (MUST BE BEFORE ANY HTML)
try {
    $db->query("CREATE TABLE IF NOT EXISTS carrusel (id INT AUTO_INCREMENT PRIMARY KEY, imagen VARCHAR(255) NOT NULL, titulo VARCHAR(255), descripcion TEXT, orden INT DEFAULT 0)");
    $db->query("CREATE TABLE IF NOT EXISTS ajustes_carrusel (id INT PRIMARY KEY, velocidad INT DEFAULT 5000)");
    $db->query("INSERT IGNORE INTO ajustes_carrusel (id, velocidad) VALUES (1, 5000)");
    $db->query("CREATE TABLE IF NOT EXISTS temas_programacion (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(100), fecha_inicio DATE, fecha_fin DATE, h INT, s INT, l INT, activo TINYINT DEFAULT 1)");
    
    // Inicializar colores HSL si no existen
    $db->query("INSERT IGNORE INTO site_config (llave, valor, categoria) VALUES ('color_h', '142', 'apariencia'), ('color_s', '70', 'apariencia'), ('color_l', '29', 'apariencia')");
    
    // Reparar tabla propuestas_valor (Asegurar columna icono)
    try {
        $db->query("ALTER TABLE propuestas_valor ADD COLUMN icono VARCHAR(100) DEFAULT 'fas fa-star' AFTER descripcion");
    } catch (Exception $e) { /* Columna ya existe */ }
} catch (Exception $e) {}

// Handle POST Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. General Info & Social
    if (isset($_POST['update_general'])) {
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['update_general', 'update_carrusel_speed'])) continue;
            $stmt = $db->prepare("INSERT INTO site_config (llave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?");
            $stmt->execute([$key, $value, $value]);
        }
        $globals = ['logo', 'favicon', 'hero_banner', 'historia_img'];
        foreach ($globals as $file_key) {
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
                $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
                $filename = $file_key . "_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], "../uploads/img/" . $filename)) {
                    $stmt = $db->prepare("INSERT INTO site_config (llave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = ?");
                    $stmt->execute([$file_key, $filename, $filename]);
                }
            }
        }
    }
    // 2. Carousel Management (Add Item)
    if (isset($_POST['action_carrusel']) && $_POST['action_carrusel'] == 'add') {
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $filename = "slide_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/img/" . $filename)) {
                $stmt = $db->prepare("INSERT INTO carrusel (imagen, titulo, descripcion) VALUES (?, ?, ?)");
                $stmt->execute([$filename, $_POST['titulo'], $_POST['descripcion']]);
            }
        }
    }
    // 3. Carousel Speed
    if (isset($_POST['update_carrusel_speed'])) {
        $speed = (int)$_POST['velocidad'] * 1000;
        $db->prepare("UPDATE ajustes_carrusel SET velocidad = ? WHERE id = 1")->execute([$speed]);
    }

    // 4. Propuesta de Valor Management
    if (isset($_POST['action_propuesta'])) {
        $id = !empty($_POST['id']) ? $_POST['id'] : null;
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $icono = $_POST['icono'];

        if ($id) {
            $stmt = $db->prepare("UPDATE propuestas_valor SET titulo = ?, descripcion = ?, icono = ? WHERE id = ?");
            $stmt->execute([$titulo, $descripcion, $icono, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO propuestas_valor (titulo, descripcion, icono) VALUES (?, ?, ?)");
            $stmt->execute([$titulo, $descripcion, $icono]);
        }
    }

    // 5. Institucion: Valores y Filosofia
    if (isset($_POST['update_institucion_esencia'])) {
        $fields = ['valores_titulo', 'valores_desc', 'filosofia_titulo', 'filosofia_desc'];
        foreach ($fields as $key) {
            if (isset($_POST[$key])) {
                $stmt = $db->prepare("INSERT INTO site_config (llave, valor, categoria) VALUES (?, ?, 'institucion') ON DUPLICATE KEY UPDATE valor = ?");
                $stmt->execute([$key, $_POST[$key], $_POST[$key]]);
            }
        }
        
        $images = ['valores_img', 'filosofia_img'];
        foreach ($images as $img_key) {
            if (isset($_FILES[$img_key]) && $_FILES[$img_key]['error'] == 0) {
                $ext = pathinfo($_FILES[$img_key]['name'], PATHINFO_EXTENSION);
                $filename = $img_key . "_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES[$img_key]['tmp_name'], "../uploads/institucion/" . $filename)) {
                    $stmt = $db->prepare("INSERT INTO site_config (llave, valor, categoria) VALUES (?, ?, 'institucion') ON DUPLICATE KEY UPDATE valor = ?");
                    $stmt->execute([$img_key, $filename, $filename]);
                }
            }
        }
    }

    // 6. Equipo Management
    if (isset($_POST['action_equipo'])) {
        $id = !empty($_POST['equipo_id']) ? $_POST['equipo_id'] : null;
        $nombre = $_POST['nombre'];
        $cargo = $_POST['cargo'];
        $frase = $_POST['frase'];
        $foto = "";

        if ($id) {
            $stmt = $db->prepare("SELECT foto FROM equipo WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            $foto = $current->foto;
        }

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $filename = "equipo_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/institucion/" . $filename)) {
                $foto = $filename;
            }
        }

        if ($id) {
            $stmt = $db->prepare("UPDATE equipo SET nombre = ?, cargo = ?, frase = ?, foto = ? WHERE id = ?");
            $stmt->execute([$nombre, $cargo, $frase, $foto, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO equipo (nombre, cargo, frase, foto) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $cargo, $frase, $foto]);
        }
    }

    header("Location: configuracion.php?success=1");
    exit;
}

// Handle Deletes
if (isset($_GET['delete_slide'])) {
    $db->prepare("DELETE FROM carrusel WHERE id = ?")->execute([$_GET['delete_slide']]);
    header("Location: configuracion.php#carrusel"); exit;
}
if (isset($_GET['delete_service'])) {
    $db->prepare("DELETE FROM servicios WHERE id = ?")->execute([$_GET['delete_service']]);
    header("Location: configuracion.php#servicios"); exit;
}
if (isset($_GET['delete_propuesta'])) {
    $db->prepare("DELETE FROM propuestas_valor WHERE id = ?")->execute([$_GET['delete_propuesta']]);
    header("Location: configuracion.php#propuestas"); exit;
}
if (isset($_GET['delete_equipo'])) {
    $db->prepare("DELETE FROM equipo WHERE id = ?")->execute([$_GET['delete_equipo']]);
    header("Location: configuracion.php#institucion"); exit;
}

// Fetch Everything for Display
$configs = $db->query("SELECT llave, valor FROM site_config")->fetchAll(PDO::FETCH_KEY_PAIR);
$slides = $db->query("SELECT * FROM carrusel ORDER BY orden ASC")->fetchAll();
$ajustes = $db->query("SELECT velocidad FROM ajustes_carrusel WHERE id = 1")->fetch();
$carrusel_speed = ($ajustes ? $ajustes->velocidad : 5000) / 1000;
$servicios = $db->query("SELECT * FROM servicios ORDER BY orden ASC")->fetchAll();
$propuestas = $db->query("SELECT * FROM propuestas_valor ORDER BY orden ASC")->fetchAll();
$equipo = $db->query("SELECT * FROM equipo ORDER BY orden ASC")->fetchAll();

function getConfig($key, $default = '') {
    global $configs;
    return $configs[$key] ?? $default;
}

// 2. NOW WE CAN INCLUDE THE HEADER (WHICH OUTPUTS HTML)
include_once 'includes/admin_header.php'; 
?>

<!-- Quill.js for Rich Text -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<style>
    .module-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
    .module-card:hover { transform: translateY(-10px); border-color: #15803d; }
    .icon-box { background-color: #f0fdf4; color: #15803d; }
    .view-module { display: none; }
    .view-module.active { display: block; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Custom Scrollbar for editors */
    .ql-container { height: 300px; font-family: 'Outfit', sans-serif; font-size: 16px; border-radius: 0 0 1.5rem 1.5rem !important; overflow-x: hidden; }
    .ql-editor { max-width: 100%; word-break: break-word; white-space: pre-wrap !important; }
    .ql-toolbar { border-radius: 1.5rem 1.5rem 0 0 !important; background: #f9fafb; max-width: 100%; }
    
    .floating-save { position: fixed; bottom: 2rem; right: 2rem; z-index: 50; }
</style>

<div class="mb-12">
    <h3 class="text-3xl font-bold text-gray-900 mb-2">Editor Global JLC V3</h3>
    <p class="text-gray-500">Control total del Hero dinámico, servicios corporativos y presencia digital.</p>
</div>

<!-- Main Content Container -->
<div id="config-container">
    
    <!-- DASHBOARD VIEW -->
    <div id="view-dashboard" class="view-module active">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- 1. Gestión Carrusel -->
            <div onclick="showModule('carrusel')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-images"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Gestión de Carrusel</h4>
                <p class="text-gray-500 text-sm">Controla las imágenes y velocidad del banner principal.</p>
            </div>

            <!-- 2. Identidad y Colores -->
            <div onclick="showModule('identidad')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-palette"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Identidad y Colores</h4>
                <p class="text-gray-500 text-sm">Logo, favicon y paleta de colores HSL.</p>
            </div>

            <!-- 3. Nuestra Institución -->
            <div onclick="showModule('institucion')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-university"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Nuestra Institución</h4>
                <p class="text-gray-500 text-sm">Edita Misión, Visión, Historia y Perfiles.</p>
            </div>

            <!-- 4. Propuesta de Valor -->
            <div onclick="showModule('propuesta')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-gem"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Propuesta de Valor</h4>
                <p class="text-gray-500 text-sm">Gestiona los puntos clave del servicio.</p>
            </div>

            <!-- 5. Servicios Corporativos -->
            <div onclick="showModule('servicios')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Servicios</h4>
                <p class="text-gray-500 text-sm">Administra la oferta de servicios profesionales.</p>
            </div>

            <!-- 6. Pilares Estratégicos -->
            <div onclick="showModule('pilares')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-archway"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Pilares</h4>
                <p class="text-gray-500 text-sm">Configura los 4 ejes fundamentales.</p>
            </div>

            <!-- 7. Redes y Contacto -->
            <div onclick="showModule('contacto')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-hashtag"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Redes y Contacto</h4>
                <p class="text-gray-500 text-sm">Links sociales, WhatsApp y ubicación.</p>
            </div>

            <!-- 8. Programador de Temas -->
            <div onclick="showModule('programador')" class="module-card bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col items-center text-center">
                <div class="w-16 h-16 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Programador</h4>
                <p class="text-gray-500 text-sm">Automatiza el cambio de estilo visual.</p>
            </div>
        </div>
    </div>

    <!-- MODULE: CARRUSEL -->
    <div id="module-carrusel" class="view-module">
        <button onclick="showDashboard()" class="mb-8 text-brand-700 font-bold flex items-center hover:translate-x-[-5px] transition-transform">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Panel
        </button>
        <section class="bg-white rounded-[2.5rem] shadow-xl p-10">
            <h4 class="text-2xl font-bold mb-8">Gestión de Carrusel</h4>
            <div class="bg-gray-50 rounded-3xl p-6 mb-8 border border-gray-100">
                <form action="configuracion.php" method="POST" class="flex items-center space-x-4">
                    <input type="hidden" name="update_carrusel_speed" value="1">
                    <label class="text-sm font-bold text-gray-600">Velocidad de transición (segundos):</label>
                    <input type="number" name="velocidad" value="<?php echo $carrusel_speed; ?>" class="w-20 px-4 py-2 rounded-xl border-none shadow-inner">
                    <button type="submit" class="bg-brand-700 text-white px-6 py-2 rounded-xl font-bold">ACTUALIZAR</button>
                </form>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <button onclick="openModal('slideModal')" class="border-2 border-dashed border-gray-200 rounded-3xl p-10 flex flex-col items-center justify-center text-gray-400 hover:border-brand-500 hover:text-brand-500 transition-all">
                    <i class="fas fa-plus text-3xl mb-4"></i>
                    <span class="font-bold">Agregar Slide</span>
                </button>
                <?php foreach($slides as $slide): ?>
                <div class="relative group rounded-3xl overflow-hidden shadow-md">
                    <img src="../uploads/img/<?php echo $slide->imagen; ?>" class="w-full h-48 object-cover">
                    <div class="absolute inset-0 bg-black/40 p-4 flex flex-col justify-end text-white">
                        <h5 class="font-bold text-sm"><?php echo $slide->titulo; ?></h5>
                        <a href="?delete_slide=<?php echo $slide->id; ?>" onclick="return confirm('¿Borrar?')" class="absolute top-2 right-2 bg-red-600 w-8 h-8 rounded-full flex items-center justify-center"><i class="fas fa-trash text-xs"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- MODULE: IDENTIDAD -->
    <div id="module-identidad" class="view-module">
        <form action="configuracion.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_general" value="1">
            <button type="button" onclick="showDashboard()" class="mb-8 text-brand-700 font-bold flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
            <section class="bg-white rounded-[2.5rem] shadow-xl p-10">
                <h4 class="text-2xl font-bold mb-8">Identidad y Colores</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-sm font-bold mb-4 text-gray-600 uppercase">Logotipo Principal</label>
                        <div class="bg-gray-50 p-10 rounded-[2rem] border-2 border-dashed border-gray-200 text-center mb-4">
                            <img id="preview-logo" src="../uploads/img/<?php echo getConfig('logo'); ?>" class="max-h-32 mx-auto mb-4">
                            <input type="file" name="logo" onchange="previewImage(this, 'preview-logo')" class="hidden" id="logo-input">
                            <label for="logo-input" class="cursor-pointer bg-white px-6 py-2 rounded-xl shadow-sm border border-gray-200 font-bold text-xs text-brand-700 hover:bg-brand-50 transition-all">CAMBIAR LOGO</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-4 text-gray-600 uppercase">Favicon (32x32)</label>
                        <div class="bg-gray-50 p-10 rounded-[2rem] border-2 border-dashed border-gray-200 text-center mb-4">
                            <img id="preview-favicon" src="../uploads/img/<?php echo getConfig('favicon'); ?>" class="w-16 h-16 mx-auto mb-4">
                            <input type="file" name="favicon" onchange="previewImage(this, 'preview-favicon')" class="hidden" id="fav-input">
                            <label for="fav-input" class="cursor-pointer bg-white px-6 py-2 rounded-xl shadow-sm border border-gray-200 font-bold text-xs text-brand-700 hover:bg-brand-50 transition-all">CAMBIAR FAVICON</label>
                        </div>
                    </div>
                </div>
                <div class="mt-10 pt-10 border-t">
                    <h5 class="font-bold text-gray-900 mb-6">Paleta de Color HSL (Primario)</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500">HUE (Matiz: 0-360)</label>
                            <input type="number" name="color_h" value="<?php echo getConfig('color_h', '142'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500">SATURATION (%)</label>
                            <input type="number" name="color_s" value="<?php echo getConfig('color_s', '70'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500">LIGHTNESS (%)</label>
                            <input type="number" name="color_l" value="<?php echo getConfig('color_l', '29'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold">
                        </div>
                    </div>
                </div>
            </section>
            <button type="submit" class="floating-save bg-brand-700 text-white px-10 py-5 rounded-3xl font-black text-lg shadow-2xl hover:bg-brand-800 transition-all">
                <i class="fas fa-save mr-2"></i> GUARDAR CAMBIOS
            </button>
        </form>
    </div>

    <!-- MODULE: INSTITUCION (Rich Text & New Sections) -->
    <div id="module-institucion" class="view-module">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <button type="button" onclick="showDashboard()" class="text-brand-700 font-bold flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
            <div class="flex space-x-2">
                <a href="#section-esencia" class="px-4 py-2 bg-gray-100 rounded-xl text-xs font-bold text-gray-600 hover:bg-brand-50 hover:text-brand-700 transition-all">ESENCIA</a>
                <a href="#section-equipo" class="px-4 py-2 bg-gray-100 rounded-xl text-xs font-bold text-gray-600 hover:bg-brand-50 hover:text-brand-700 transition-all">EQUIPO</a>
            </div>
        </div>

        <section class="space-y-12">
            <!-- 1. Textos Principales -->
            <form action="configuracion.php" id="form-institucion" method="POST" class="bg-white rounded-[2.5rem] shadow-xl p-10 space-y-10">
                <input type="hidden" name="update_general" value="1">
                <h4 class="text-2xl font-bold border-b pb-4">Contenido Principal</h4>
                <div class="grid grid-cols-1 gap-8">
                    <div>
                        <label class="block text-sm font-bold mb-4 text-gray-600 uppercase">Misión Institucional</label>
                        <div id="editor-mision" class="bg-white"></div>
                        <input type="hidden" name="mision" id="input-mision" value="<?php echo htmlspecialchars(getConfig('mision')); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-4 text-gray-600 uppercase">Visión Institucional</label>
                        <div id="editor-vision" class="bg-white"></div>
                        <input type="hidden" name="vision" id="input-vision" value="<?php echo htmlspecialchars(getConfig('vision')); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-4 text-gray-600 uppercase">Nuestra Historia</label>
                        <div id="editor-historia" class="bg-white"></div>
                        <input type="hidden" name="historia" id="input-historia" value="<?php echo htmlspecialchars(getConfig('historia')); ?>">
                    </div>
                </div>
                <button type="submit" class="bg-brand-700 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:bg-brand-800 transition-all w-full">
                    <i class="fas fa-save mr-2"></i> GUARDAR CONTENIDO PRINCIPAL
                </button>
            </form>

            <!-- 2. Nuestra Esencia (Valores y Filosofía) -->
            <form action="configuracion.php" method="POST" enctype="multipart/form-data" id="section-esencia" class="bg-white rounded-[2.5rem] shadow-xl p-10 space-y-10">
                <input type="hidden" name="update_institucion_esencia" value="1">
                <h4 class="text-2xl font-bold border-b pb-4">Nuestra Esencia (Valores y Filosofía)</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Valores -->
                    <div class="space-y-6">
                        <h5 class="font-bold text-brand-700 flex items-center">
                            <span class="w-8 h-8 bg-brand-100 text-brand-700 rounded-lg flex items-center justify-center mr-2 text-xs">1</span>
                            Nuestros Valores
                        </h5>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Título</label>
                            <input type="text" name="valores_titulo" value="<?php echo htmlspecialchars(getConfig('valores_titulo', 'Nuestros Valores')); ?>" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripción</label>
                            <textarea name="valores_desc" rows="4" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none text-sm"><?php echo htmlspecialchars(getConfig('valores_desc')); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-4">Imagen o Icono acompañante</label>
                            <div class="flex items-center space-x-6 bg-gray-50 p-6 rounded-3xl">
                                <img src="../uploads/institucion/<?php echo getConfig('valores_img'); ?>" class="w-20 h-20 object-cover rounded-2xl bg-white shadow-sm" onerror="this.src='https://via.placeholder.com/150?text=Icono'">
                                <input type="file" name="valores_img" class="text-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Filosofía -->
                    <div class="space-y-6">
                        <h5 class="font-bold text-yellow-600 flex items-center">
                            <span class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center mr-2 text-xs">2</span>
                            Nuestra Filosofía
                        </h5>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Título</label>
                            <input type="text" name="filosofia_titulo" value="<?php echo htmlspecialchars(getConfig('filosofia_titulo', 'Nuestra Filosofía')); ?>" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripción</label>
                            <textarea name="filosofia_desc" rows="4" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none text-sm"><?php echo htmlspecialchars(getConfig('filosofia_desc')); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-4">Imagen o Icono acompañante</label>
                            <div class="flex items-center space-x-6 bg-gray-50 p-6 rounded-3xl">
                                <img src="../uploads/institucion/<?php echo getConfig('filosofia_img'); ?>" class="w-20 h-20 object-cover rounded-2xl bg-white shadow-sm" onerror="this.src='https://via.placeholder.com/150?text=Icono'">
                                <input type="file" name="filosofia_img" class="text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-gray-900 text-white px-10 py-4 rounded-2xl font-black shadow-lg hover:bg-black transition-all w-full">
                    <i class="fas fa-save mr-2"></i> GUARDAR ESENCIA
                </button>
            </form>

            <!-- 3. Perfiles del Equipo -->
            <div id="section-equipo" class="bg-white rounded-[2.5rem] shadow-xl p-10">
                <div class="flex justify-between items-center mb-10 border-b pb-6">
                    <div>
                        <h4 class="text-2xl font-bold">Perfiles Profesionales (Equipo)</h4>
                        <p class="text-gray-500 text-sm mt-1">Administra el liderazgo académico y administrativo.</p>
                    </div>
                    <button onclick="openModal('equipoModal'); resetEquipoForm();" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center shadow-md transition-all text-sm">
                        <i class="fas fa-plus mr-2"></i> + AÑADIR PERFIL
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($equipo as $person): ?>
                    <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 flex items-center space-x-4 group relative">
                        <img src="../uploads/institucion/<?php echo $person->foto; ?>" class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($person->nombre); ?>&background=random'">
                        <div class="flex-grow min-w-0">
                            <h5 class="font-bold text-gray-900 truncate"><?php echo $person->nombre; ?></h5>
                            <p class="text-brand-600 text-xs font-bold uppercase tracking-wider"><?php echo $person->cargo; ?></p>
                        </div>
                        <div class="flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick='editEquipo(<?php echo json_encode($person); ?>)' class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                            <a href="?delete_equipo=<?php echo $person->id; ?>" onclick="return confirm('¿Eliminar integrante?')" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($equipo)): ?>
                        <div class="col-span-full py-10 text-center text-gray-400 italic">No hay integrantes registrados.</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- MODULE: REDES (REUSE EXISTING LOGIC) -->
    <div id="module-contacto" class="view-module">
        <form action="configuracion.php" method="POST">
            <input type="hidden" name="update_general" value="1">
            <button type="button" onclick="showDashboard()" class="mb-8 text-brand-700 font-bold flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
            <section class="bg-white rounded-[2.5rem] shadow-xl p-10">
                <h4 class="text-2xl font-bold mb-10">Redes y Contacto</h4>
                
                <div class="bg-brand-50 p-8 rounded-3xl mb-10 border border-brand-100">
                    <h5 class="font-bold text-brand-900 mb-2">Visibilidad de Ventana Social</h5>
                    <select name="show_social_section" class="w-full bg-white border-none rounded-2xl font-bold text-brand-700 px-6 py-4 shadow-sm">
                        <option value="1" <?php echo getConfig('show_social_section') == '1' ? 'selected' : ''; ?>>ACTIVADA EN INICIO</option>
                        <option value="0" <?php echo getConfig('show_social_section') == '0' ? 'selected' : ''; ?>>DESACTIVADA EN INICIO</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500">TELÉFONO DE CONTACTO</label>
                        <input type="text" name="contact_phone" value="<?php echo getConfig('contact_phone'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500">EMAIL OFICIAL</label>
                        <input type="email" name="contact_email" value="<?php echo getConfig('contact_email'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-bold text-gray-500">DIRECCIÓN FÍSICA</label>
                        <input type="text" name="contact_address" value="<?php echo getConfig('contact_address'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500">FACEBOOK URL</label>
                        <input type="text" name="social_fb" value="<?php echo getConfig('social_fb'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500">INSTAGRAM URL</label>
                        <input type="text" name="social_ig" value="<?php echo getConfig('social_ig'); ?>" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none">
                    </div>
                </div>
            </section>
            <button type="submit" class="floating-save bg-brand-700 text-white px-10 py-5 rounded-3xl font-black text-lg shadow-2xl hover:bg-brand-800 transition-all">
                <i class="fas fa-save mr-2"></i> GUARDAR CAMBIOS
            </button>
        </form>
    </div>

    <!-- MODULE: PROGRAMADOR (Dashboard Calendar placeholder for logic) -->
    <div id="module-programador" class="view-module">
        <button onclick="showDashboard()" class="mb-8 text-brand-700 font-bold flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
        <section class="bg-white rounded-[2.5rem] shadow-xl p-10">
            <h4 class="text-2xl font-bold mb-8">Programador de Temas Visuales</h4>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="bg-gray-50 p-8 rounded-3xl border">
                    <h5 class="font-bold mb-6">Programar Nuevo Tema</h5>
                    <form action="configuracion.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action_theme" value="add">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Nombre del Evento</label>
                            <input type="text" name="nombre_tema" placeholder="Ej: Navidad, Aniversario" class="w-full px-4 py-3 rounded-xl border-none shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase">Inicio</label>
                                <input type="date" name="fecha_inicio" class="w-full px-4 py-3 rounded-xl border-none shadow-sm">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase">Fin</label>
                                <input type="date" name="fecha_fin" class="w-full px-4 py-3 rounded-xl border-none shadow-sm">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-brand-700 text-white font-bold py-4 rounded-xl shadow-lg mt-4">AGREGAR AL CALENDARIO</button>
                    </form>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Eventos Próximos</h5>
                    <div class="space-y-4">
                        <p class="text-gray-400 italic text-sm">No hay temas programados actualmente.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- MODULE: PROPUESTA -->
    <div id="module-propuesta" class="view-module">
        <button onclick="showDashboard()" class="mb-8 text-brand-700 font-bold flex items-center hover:translate-x-[-5px] transition-transform">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Panel
        </button>
        <section class="bg-white rounded-[2.5rem] shadow-xl p-10">
            <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6">
                <div>
                    <h4 class="text-3xl font-bold text-gray-900">Propuestas de Valor</h4>
                    <p class="text-gray-500 text-sm mt-1">Gestiona los pilares competitivos de la institución.</p>
                </div>
                <button onclick="openModal('propuestaModal'); resetPropuestaForm();" class="bg-brand-700 hover:bg-brand-800 text-white px-8 py-4 rounded-2xl font-bold flex items-center shadow-lg transition-all">
                    <i class="fas fa-plus mr-2"></i> AGREGAR NUEVA PROPUESTA
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($propuestas as $prop): ?>
                <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-md hover:shadow-xl transition-all group relative">
                    <div class="w-14 h-14 icon-box rounded-2xl flex items-center justify-center text-2xl mb-6">
                        <i class="<?php echo $prop->icono; ?>"></i>
                    </div>
                    <h5 class="text-xl font-bold text-gray-900 mb-3"><?php echo $prop->titulo; ?></h5>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        <?php echo strlen($prop->descripcion) > 100 ? substr($prop->descripcion, 0, 100) . '...' : $prop->descripcion; ?>
                    </p>
                    
                    <div class="flex items-center space-x-3 pt-6 border-t border-gray-50">
                        <button onclick='editPropuesta(<?php echo json_encode($prop); ?>)' class="flex-grow bg-brand-50 text-brand-700 font-bold py-3 rounded-xl hover:bg-brand-700 hover:text-white transition-all text-xs flex items-center justify-center">
                            <i class="fas fa-pencil-alt mr-2"></i> EDITAR
                        </button>
                        <a href="?delete_propuesta=<?php echo $prop->id; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta propuesta?')" class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- MODULE: SERVICIOS -->
    <div id="module-servicios" class="view-module">
        <button onclick="showDashboard()" class="mb-8 text-brand-700 font-bold flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
        <section class="bg-white rounded-[2.5rem] shadow-xl p-10">
            <div class="flex justify-between items-center mb-10">
                <h4 class="text-2xl font-bold">Servicios Corporativos</h4>
                <button onclick="openModal('serviceModal')" class="bg-green-600 text-white px-6 py-2 rounded-full font-bold text-xs shadow-lg">AGREGAR NUEVO</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach($servicios as $srv): ?>
                <div class="p-6 bg-gray-50 rounded-3xl border flex items-center justify-between group hover:border-brand-500 transition-all">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-brand-700 shadow-sm">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="font-bold text-gray-800"><?php echo $srv->titulo; ?></span>
                    </div>
                    <a href="?delete_service=<?php echo $srv->id; ?>" class="text-red-400 hover:text-red-600" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- MODULE: PILARES -->
    <div id="module-pilares" class="view-module">
        <button onclick="showDashboard()" class="mb-8 text-brand-700 font-bold flex items-center"><i class="fas fa-arrow-left mr-2"></i> Volver</button>
        <section class="bg-white rounded-[2.5rem] shadow-xl p-10">
            <h4 class="text-2xl font-bold mb-8">Pilares Estratégicos</h4>
            <p class="text-gray-500 mb-10">Edita los 4 pilares fundamentales que aparecen en la página de inicio.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-8 rounded-3xl border space-y-4">
                    <h5 class="font-bold text-brand-700">Pilar 1: Capacitación</h5>
                    <input type="text" value="Capacitación" class="w-full px-4 py-3 rounded-xl border-none shadow-sm font-bold">
                    <textarea class="w-full px-4 py-3 rounded-xl border-none shadow-sm text-sm" rows="3">Formación técnica y profesional para las nuevas demandas laborales.</textarea>
                </div>
                <div class="bg-gray-50 p-8 rounded-3xl border space-y-4">
                    <h5 class="font-bold text-yellow-600">Pilar 2: Proyectos</h5>
                    <input type="text" value="Proyectos" class="w-full px-4 py-3 rounded-xl border-none shadow-sm font-bold">
                    <textarea class="w-full px-4 py-3 rounded-xl border-none shadow-sm text-sm" rows="3">Asesoría experta en iniciativas de alto impacto socio-ambiental.</textarea>
                </div>
            </div>
            <button class="mt-8 bg-brand-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg">GUARDAR PILARES</button>
        </section>
    </div>

</div> <!-- End config-container -->

<!-- MODALS -->
<!-- MODAL: PROPUESTA -->
<div id="propuestaModal" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-[3rem] shadow-2xl max-w-2xl w-full p-10 overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-bold text-gray-900" id="propuesta-modal-title">Nueva Propuesta de Valor</h3>
            <button onclick="closeModal('propuestaModal')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <form action="configuracion.php" method="POST" class="space-y-6">
            <input type="hidden" name="action_propuesta" value="1">
            <input type="hidden" name="id" id="prop-id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Título de la Propuesta</label>
                    <input type="text" name="titulo" id="prop-titulo" required placeholder="Ej: Educación Disruptiva" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none focus:ring-2 focus:ring-brand-500 font-bold">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Descripción Detallada</label>
                    <textarea name="descripcion" id="prop-descripcion" rows="4" required placeholder="Explica brevemente de qué trata esta propuesta..." class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none focus:ring-2 focus:ring-brand-500 text-sm"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Selecciona un Icono Visual</label>
                    <input type="hidden" name="icono" id="prop-icono" value="fas fa-star">
                    
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-4 bg-gray-50 p-6 rounded-[2rem]">
                        <?php 
                        $common_icons = [
                            'fas fa-brain' => 'Cerebro',
                            'fas fa-bullseye' => 'Diana',
                            'fas fa-star' => 'Estrella',
                            'fas fa-leaf' => 'Hoja',
                            'fas fa-users' => 'Usuarios',
                            'fas fa-microchip' => 'Tecno',
                            'fas fa-graduation-cap' => 'Edu',
                            'fas fa-hand-holding-heart' => 'Social'
                        ];
                        foreach($common_icons as $class => $name): ?>
                        <div onclick="selectIcon('<?php echo $class; ?>')" 
                             id="icon-opt-<?php echo str_replace(' ', '-', $class); ?>"
                             class="icon-selector-opt w-full aspect-square bg-white rounded-xl flex items-center justify-center text-xl text-gray-400 border-2 border-transparent hover:border-brand-500 cursor-pointer transition-all shadow-sm"
                             title="<?php echo $name; ?>">
                            <i class="<?php echo $class; ?>"></i>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="md:col-span-2 flex items-center justify-between bg-brand-50 p-6 rounded-2xl">
                    <span class="text-xs font-bold text-brand-700 uppercase">Icono Seleccionado:</span>
                    <div class="w-12 h-12 bg-white text-brand-700 rounded-xl flex items-center justify-center text-2xl shadow-sm">
                        <i id="icon-preview" class="fas fa-star"></i>
                    </div>
                </div>
            </div>

            <div class="flex space-x-4 pt-8">
                <button type="submit" class="flex-grow bg-brand-700 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-brand-800 transition-all uppercase tracking-widest">
                    GUARDAR PROPUESTA
                </button>
                <button type="button" onclick="closeModal('propuestaModal')" class="px-10 py-5 text-gray-400 font-bold hover:text-gray-600 transition-all">
                    CANCELAR
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: CARRUSEL -->
<div id="slideModal" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-[3rem] shadow-2xl max-w-lg w-full p-10">
        <h3 class="text-2xl font-bold mb-8">Nuevo Slide del Carrusel</h3>
        <form action="configuracion.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action_carrusel" value="add">
            <div>
                <label class="block text-sm font-bold mb-2">Imagen (Recomendado 1920x800)</label>
                <input type="file" name="imagen" required class="text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">Título del Slide</label>
                <input type="text" name="titulo" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none font-bold">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">Descripción corta</label>
                <textarea name="descripcion" rows="3" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none text-sm"></textarea>
            </div>
            <div class="flex space-x-4 pt-6">
                <button type="submit" class="flex-grow bg-brand-700 text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-brand-800 transition-all">SUBIR SLIDE</button>
                <button type="button" onclick="closeModal('slideModal')" class="px-8 py-4 text-gray-500 font-bold">CANCELAR</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EQUIPO -->
<div id="equipoModal" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-[3rem] shadow-2xl max-w-xl w-full p-10">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-bold text-gray-900" id="equipo-modal-title">Nuevo Perfil del Equipo</h3>
            <button onclick="closeModal('equipoModal')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <form action="configuracion.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action_equipo" value="1">
            <input type="hidden" name="equipo_id" id="equipo-id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2">Nombre Completo</label>
                    <input type="text" name="nombre" id="equipo-nombre" required class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2">Cargo / Título</label>
                    <input type="text" name="cargo" id="equipo-cargo" required class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2">Foto de Perfil</label>
                    <input type="file" name="foto" id="equipo-foto" class="text-xs">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase mb-2">Frase Inspiradora</label>
                    <textarea name="frase" id="equipo-frase" rows="3" class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none text-sm"></textarea>
                </div>
            </div>

            <div class="flex space-x-4 pt-6">
                <button type="submit" class="flex-grow bg-green-600 text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-green-700 transition-all uppercase">
                    GUARDAR PERFIL
                </button>
                <button type="button" onclick="closeModal('equipoModal')" class="px-8 py-4 text-gray-500 font-bold">CANCELAR</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Navigation Logic
    function showModule(moduleId) {
        document.querySelectorAll('.view-module').forEach(m => m.classList.remove('active'));
        document.getElementById('module-' + moduleId).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showDashboard() {
        document.querySelectorAll('.view-module').forEach(m => m.classList.remove('active'));
        document.getElementById('view-dashboard').classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Modal Logic
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // Propuesta Logic
    function resetPropuestaForm() {
        document.getElementById('propuesta-modal-title').innerText = 'Nueva Propuesta de Valor';
        document.getElementById('prop-id').value = '';
        document.getElementById('prop-titulo').value = '';
        document.getElementById('prop-descripcion').value = '';
        document.getElementById('prop-icono').value = 'fas fa-star';
        updateIconPreview('fas fa-star');
    }

    function editPropuesta(data) {
        document.getElementById('propuesta-modal-title').innerText = 'Editar Propuesta';
        document.getElementById('prop-id').value = data.id;
        document.getElementById('prop-titulo').value = data.titulo;
        document.getElementById('prop-descripcion').value = data.descripcion;
        document.getElementById('prop-icono').value = data.icono;
        updateIconPreview(data.icono);
        openModal('propuestaModal');
    }

    function updateIconPreview(className) {
        const preview = document.getElementById('icon-preview');
        preview.className = className || 'fas fa-question';
        
        // Highlight active icon in picker
        document.querySelectorAll('.icon-selector-opt').forEach(opt => {
            opt.classList.remove('border-brand-500', 'text-brand-700', 'bg-brand-50');
            opt.classList.add('text-gray-400', 'bg-white');
        });
        const activeOpt = document.getElementById('icon-opt-' + (className || '').replace(/ /g, '-'));
        if (activeOpt) {
            activeOpt.classList.remove('text-gray-400', 'bg-white');
            activeOpt.classList.add('border-brand-500', 'text-brand-700', 'bg-brand-50');
        }
    }

    function selectIcon(className) {
        document.getElementById('prop-icono').value = className;
        updateIconPreview(className);
    }

    // Image Preview
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Equipo Logic
    function resetEquipoForm() {
        document.getElementById('equipo-modal-title').innerText = 'Nuevo Perfil del Equipo';
        document.getElementById('equipo-id').value = '';
        document.getElementById('equipo-nombre').value = '';
        document.getElementById('equipo-cargo').value = '';
        document.getElementById('equipo-frase').value = '';
        document.getElementById('equipo-foto').required = true;
    }

    function editEquipo(data) {
        document.getElementById('equipo-modal-title').innerText = 'Editar Perfil';
        document.getElementById('equipo-id').value = data.id;
        document.getElementById('equipo-nombre').value = data.nombre;
        document.getElementById('equipo-cargo').value = data.cargo;
        document.getElementById('equipo-frase').value = data.frase;
        document.getElementById('equipo-foto').required = false;
        openModal('equipoModal');
    }

    // Quill Editors Initialization
    const quillConfigs = {
        theme: 'snow',
        placeholder: 'Escribe aquí...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'clean']
            ]
        }
    };

    const editors = {};
    ['mision', 'vision', 'historia'].forEach(id => {
        const container = document.getElementById('editor-' + id);
        if (container) {
            editors[id] = new Quill('#editor-' + id, quillConfigs);
            // Load initial content
            const input = document.getElementById('input-' + id);
            if (input && input.value) {
                editors[id].root.innerHTML = input.value;
            }
        }
    });

    // Update hidden inputs before submit
    const institutionForm = document.getElementById('form-institucion');
    if (institutionForm) {
        institutionForm.onsubmit = function() {
            ['mision', 'vision', 'historia'].forEach(id => {
                document.getElementById('input-' + id).value = editors[id].root.innerHTML;
            });
        };
    }
</script>

<?php 
$content = ob_get_clean();
echo $content;
include_once 'includes/admin_footer.php'; 
?>
