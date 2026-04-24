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
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <header class="hero-section">
        <div class="hero-content">
            <h1>Transformando el Mañana a través de la Educación</h1>
            <p>Impulsamos el desarrollo integral mediante tecnología, sostenibilidad y gestión social.</p>
            <div class="hero-btns">
                <a href="capacitate.php" class="btn-primary">Explorar Cursos</a>
                <a href="#pilares" class="btn-secondary">Nuestros Pilares</a>
            </div>
        </div>
    </header>

    <section id="pilares" class="pillars-grid">
        <div class="section-title">
            <h2>Nuestros Pilares Estratégicos</h2>
            <div class="underline"></div>
        </div>

        <div class="container">
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

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: #fcfcfc;
            color: #333;
            line-height: 1.6;
        }

        .hero-section {
            height: 80vh;
            background: linear-gradient(rgba(0, 74, 153, 0.7), rgba(0, 74, 153, 0.7)),
                url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1351&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-btns {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-primary,
        .btn-secondary {
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary {
            background: #28a745;
            color: white;
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .pillars-grid {
            padding: 5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: #004a99;
        }

        .underline {
            width: 80px;
            height: 4px;
            background: #28a745;
            margin: 10px auto;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .pillar-card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            border-bottom: 5px solid transparent;
        }

        .pillar-card:hover {
            transform: translateY(-10px);
            border-bottom: 5px solid #28a745;
        }

        .pillar-card i {
            font-size: 3rem;
            color: #004a99;
            margin-bottom: 1.5rem;
        }

        .pillar-card h3 {
            margin-bottom: 1rem;
            color: #333;
        }
    </style>

    <!-- Firebase SDK (Version 9+) -->
    <script type="module" src="js/auth-handler.js"></script>

</body>

</html>