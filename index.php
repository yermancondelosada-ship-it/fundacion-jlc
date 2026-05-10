<?php
require_once 'config/config.php';
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
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <img src="uploads/img/banner1.jpg" alt="Educación" onerror="this.src='https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&h=500&q=80'">
                    <div class="carousel-caption">
                        <h1>Transformando el Mañana a través de la Educación</h1>
                        <p>Impulsamos el desarrollo integral mediante tecnología, sostenibilidad y gestión social.</p>
                        <div class="hero-btns">
                            <a href="capacitate.php" class="btn-primary">Explorar Cursos</a>
                            <a href="#pilares" class="btn-secondary">Nuestros Pilares</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <img src="uploads/img/banner2.jpg" alt="Sostenibilidad" onerror="this.src='https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&h=500&q=80'">
                    <div class="carousel-caption">
                        <h1>Turismo Sostenible y Gestión Ambiental</h1>
                        <p>Promovemos la conservación de nuestro entorno para las futuras generaciones.</p>
                        <div class="hero-btns">
                            <a href="nuestra-fundacion.php" class="btn-primary">Conócenos</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <img src="uploads/img/banner3.jpg" alt="Tecnología" onerror="this.src='https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&h=500&q=80'">
                    <div class="carousel-caption">
                        <h1>Innovación para el Desarrollo</h1>
                        <p>Llevamos herramientas tecnológicas a quienes más lo necesitan.</p>
                        <div class="hero-btns">
                            <a href="servicios-corporativos.php" class="btn-primary">Nuestros Servicios</a>
                        </div>
                    </div>
                </div>
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
            <div class="pillar-card">
                <i class="fas fa-microchip"></i>
                <h3>Tecnología e Innovación</h3>
                <p>Digitalización y soluciones vanguardistas para el desarrollo comunitario.</p>
            </div>
            <div class="pillar-card">
                <i class="fas fa-book-reader"></i>
                <h3>Educación Multinivel</h3>
                <p>Programas académicos desde lo básico hasta formación técnica avanzada.</p>
            </div>
            <div class="pillar-card">
                <i class="fas fa-globe-americas"></i>
                <h3>Turismo Sostenible</h3>
                <p>Promoción de destinos respetando el equilibrio ecológico y cultural.</p>
            </div>
            <div class="pillar-card">
                <i class="fas fa-seedling"></i>
                <h3>Gestión Socio-Ambiental</h3>
                <p>Proyectos de impacto real en la conservación y bienestar social.</p>
            </div>
        </div>
    </section>

    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Carousel Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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