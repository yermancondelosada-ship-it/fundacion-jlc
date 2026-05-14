<?php
require_once 'config/config.php';
require_once 'config/db.php';

$db = Database::getInstance();
$site_configs = $db->query("SELECT llave, valor FROM site_config")->fetchAll(PDO::FETCH_KEY_PAIR);

if (!function_exists('getSiteConfig')) {
    function getSiteConfig($key, $default = '')
    {
        global $site_configs;
        return $site_configs[$key] ?? $default;
    }
}

$carrusel = $db->query("SELECT * FROM carrusel ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fundación José Lisper Conde | Innovación y Sostenibilidad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/style.css">
    <style>
        #heroSwiper {
            width: 100% !important;
            max-width: 100vw !important;
        }
        .hero-wrapper {
            overflow: hidden;
        }
    </style>
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="hero-wrapper">
        <!-- Slider main container -->
        <div class="swiper-container swiper" id="heroSwiper">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <?php if (count($carrusel) > 0): ?>
                    <?php foreach ($carrusel as $slide): ?>
                        <div class="swiper-slide">
                            <img src="uploads/img/<?php echo htmlspecialchars($slide->imagen); ?>"
                                alt="<?php echo htmlspecialchars($slide->titulo); ?>">
                            <div class="carousel-caption">
                                <h1><?php echo htmlspecialchars($slide->titulo); ?></h1>
                                <p><?php echo htmlspecialchars($slide->descripcion); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Slide por Defecto -->
                    <div class="swiper-slide">
                        <img src="uploads/img/banner1.jpg" alt="Educación"
                            onerror="this.src='https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&h=500&q=80'">
                        <div class="carousel-caption">
                            <h1>Transformando el Mañana a través de la Educación</h1>
                            <p>Impulsamos el desarrollo integral mediante tecnología, sostenibilidad y gestión social.</p>
                            <div class="hero-btns">
                                <a href="capacitate.php" class="btn-primary">Explorar Cursos</a>
                                <a href="#pilares" class="btn-secondary">Nuestros Pilares</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev" style="color: white;"></div>
            <div class="swiper-button-next" style="color: white;"></div>
        </div>
    </div>

    <section id="pilares" class="pillars-grid">
        <div class="section-title">
            <h2>Nuestros Pilares Estratégicos</h2>
            <div class="underline"></div>
        </div>

        <div class="pillars-container">
            <?php
            $default_icons = ['fas fa-microchip', 'fas fa-book-reader', 'fas fa-globe-americas', 'fas fa-seedling'];
            $default_titles = ['Tecnología e Innovación', 'Educación Multinivel', 'Turismo Sostenible', 'Gestión Socio-Ambiental'];
            $default_descs = [
                'Digitalización y soluciones vanguardistas para el desarrollo comunitario.',
                'Programas académicos desde lo básico hasta formación técnica avanzada.',
                'Promoción de destinos respetando el equilibrio ecológico y cultural.',
                'Proyectos de impacto real en la conservación y bienestar social.'
            ];

            for ($i = 1; $i <= 4; $i++):
                $icono = getSiteConfig("pilar_{$i}_icono", $default_icons[$i - 1]);
                $titulo = getSiteConfig("pilar_{$i}_titulo", $default_titles[$i - 1]);
                $desc = getSiteConfig("pilar_{$i}_desc", $default_descs[$i - 1]);
                ?>
                <div class="pillar-card">
                    <i class="<?php echo htmlspecialchars($icono); ?>"></i>
                    <h3><?php echo htmlspecialchars($titulo); ?></h3>
                    <p><?php echo htmlspecialchars($desc); ?></p>
                </div>
            <?php endfor; ?>
        </div>
    </section>

    <!-- Redes Sociales Section -->
    <section class="py-16 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Nuestras Redes Sociales</h2>
                <div class="w-20 h-1 bg-brand-700 mx-auto rounded"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- YouTube Widget -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fab fa-youtube text-red-600 mr-3 text-2xl"></i> Canal Oficial
                    </h3>
                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-gray-100">
                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <p class="text-center text-sm text-gray-500 mt-4">Video Destacado</p>
                </div>

                <!-- Facebook Widget -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fab fa-facebook text-blue-600 mr-3 text-2xl"></i> Facebook
                    </h3>
                    <div class="w-full rounded-xl overflow-hidden bg-gray-100 flex justify-center" style="height: 250px;">
                        <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Ffacebook&tabs=timeline&width=340&height=250&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=false&appId" width="100%" height="100%" style="border:none;overflow:hidden;" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                    </div>
                    <p class="text-center text-sm text-gray-500 mt-4">Últimas Noticias</p>
                </div>

                <!-- Instagram Widget -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fab fa-instagram text-pink-600 mr-3 text-2xl"></i> Instagram
                    </h3>
                    <div class="w-full rounded-xl overflow-hidden bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 flex items-center justify-center flex-col text-white text-center" style="height: 250px;">
                        <div class="bg-white/10 backdrop-blur-sm w-full h-full p-6 flex flex-col items-center justify-center">
                            <i class="fab fa-instagram text-5xl mb-3"></i>
                            <h4 class="text-xl font-bold mb-1">@fundacionjlc_co</h4>
                            <p class="mb-4 text-sm opacity-90">Síguenos para ver nuestro día a día.</p>
                            <a href="https://www.instagram.com/fundacionjlc_co/" target="_blank" class="bg-white text-purple-600 font-bold py-2 px-6 rounded-full hover:scale-105 transition-transform shadow-lg text-sm">
                                Ver Perfil
                            </a>
                        </div>
                    </div>
                    <p class="text-center text-sm text-gray-500 mt-4">Nuestra Galería</p>
                </div>
            </div>
        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Carousel Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('#heroSwiper', {
                direction: 'horizontal',
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>

    <!-- Firebase SDK (Version 9+) -->
    <script type="module" src="js/auth-handler.js"></script>

</body>

</html>