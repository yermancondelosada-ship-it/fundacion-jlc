<?php 
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../config/db.php';

// Cargar configuración del sitio
$db = Database::getInstance();
$site_configs = $db->query("SELECT llave, valor FROM site_config")->fetchAll(PDO::FETCH_KEY_PAIR);

function getSiteConfig($key, $default = '') {
    global $site_configs;
    return $site_configs[$key] ?? $default;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getSiteConfig('site_name', SITE_NAME); ?> | Educación y Tecnología</title>
    
    <!-- Favicon dinámico -->
    <?php if(getSiteConfig('favicon')): ?>
        <link rel="icon" type="image/png" href="uploads/img/<?php echo getSiteConfig('favicon'); ?>">
    <?php endif; ?>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        <?php 
            $h = getSiteConfig('color_h', '142');
            $s = getSiteConfig('color_s', '70');
            $l = getSiteConfig('color_l', '29');
        ?>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: 'hsl(<?php echo $h; ?>, <?php echo $s; ?>%, 97%)',
                            100: 'hsl(<?php echo $h; ?>, <?php echo $s; ?>%, 93%)',
                            500: 'hsl(<?php echo $h; ?>, <?php echo $s; ?>%, 50%)',
                            600: 'hsl(<?php echo $h; ?>, <?php echo $s; ?>%, 40%)',
                            700: 'hsl(<?php echo $h; ?>, <?php echo $s; ?>%, <?php echo $l; ?>%)',
                            800: 'hsl(<?php echo $h; ?>, <?php echo $s; ?>%, 20%)',
                            900: 'hsl(<?php echo $h; ?>, <?php echo $s; ?>%, 15%)',
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

    <!-- Top Info Bar -->
    <div class="bg-white border-b border-gray-100 py-2 hidden md:block">
        <div class="container mx-auto px-4 flex justify-between items-center text-sm text-gray-600">
            <div class="flex items-center space-x-6">
                <span class="flex items-center"><i class="fas fa-phone text-brand-700 mr-2"></i> <?php echo getSiteConfig('contact_phone', CONTACT_PHONE); ?></span>
                <span class="flex items-center"><i class="fas fa-envelope text-brand-700 mr-2"></i> <?php echo getSiteConfig('contact_email', CONTACT_EMAIL); ?></span>
            </div>
            <div class="flex items-center space-x-4">
                <?php if(getSiteConfig('social_fb')): ?>
                    <a href="<?php echo getSiteConfig('social_fb'); ?>" target="_blank" class="hover:text-brand-700 transition-colors"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if(getSiteConfig('social_ig')): ?>
                    <a href="<?php echo getSiteConfig('social_ig'); ?>" target="_blank" class="hover:text-brand-700 transition-colors"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <a href="<?php echo getSiteConfig('social_wa', 'https://wa.me/'.WHATSAPP_NUMBER); ?>" target="_blank" class="hover:text-brand-700 transition-colors"><i class="fab fa-whatsapp"></i></a>
                
                <div class="h-4 w-px bg-gray-200 mx-2"></div>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="aula-virtual.php" class="bg-brand-700 text-white px-4 py-1 rounded-md text-xs font-bold hover:bg-brand-800 transition-all shadow-sm flex items-center">
                        <i class="fas fa-graduation-cap mr-2"></i> <?php echo explode(' ', $_SESSION['user_nombre'])[0]; ?>
                    </a>
                    <a href="logout.php" class="ml-2 text-red-600 hover:text-red-800 text-xs font-bold"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="aula-virtual.php" class="bg-brand-700 text-white px-4 py-1 rounded-md text-xs font-bold hover:bg-brand-800 transition-all shadow-sm flex items-center">
                        <i class="fas fa-user-circle mr-2"></i> LOGIN
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="sticky top-0 z-50 bg-brand-700 text-white shadow-xl">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="index.php" class="flex items-center space-x-3 group">
                    <div class="bg-white p-2 rounded-xl shadow-inner group-hover:scale-110 transition-transform flex items-center justify-center">
                        <?php if(getSiteConfig('logo')): ?>
                            <img src="uploads/img/<?php echo getSiteConfig('logo'); ?>" class="h-10 w-auto" alt="Logo">
                        <?php else: ?>
                            <i class="fas fa-leaf text-brand-700 text-2xl"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold tracking-tight leading-none uppercase"><?php echo getSiteConfig('site_name_short', 'FUNDACIÓN JLC'); ?></span>
                        <span class="text-[10px] uppercase tracking-[0.2em] font-light opacity-80">Educación y Tecnología</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-5">
                    <a href="index.php" class="text-xs font-bold uppercase tracking-wider hover:text-green-300 transition-colors">Inicio</a>
                    <a href="nuestra-fundacion.php" class="text-xs font-bold uppercase tracking-wider hover:text-green-300 transition-colors">Nuestra Fundación</a>
                    <a href="servicios-corporativos.php" class="text-xs font-bold uppercase tracking-wider hover:text-green-300 transition-colors">Servicios</a>
                    <a href="pilares.php" class="text-xs font-bold uppercase tracking-wider hover:text-green-300 transition-colors">Pilares</a>
                    <a href="propuesta-de-valor.php" class="text-xs font-bold uppercase tracking-wider hover:text-green-300 transition-colors">Propuesta</a>
                    <a href="blog.php" class="text-xs font-bold uppercase tracking-wider hover:text-green-300 transition-colors">Blog</a>
                    <a href="contacto.php" class="text-xs font-bold uppercase tracking-wider hover:text-green-300 transition-colors">Contacto</a>
                </div>

                <!-- Mobile Toggle -->
                <button class="lg:hidden text-2xl p-2 hover:bg-brand-800 rounded-lg transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>
