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
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/style.css">
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