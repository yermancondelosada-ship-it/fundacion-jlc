<?php
/**
 * Navbar Profesional - Fundación JLC
 */
?>
<nav class="main-navbar">
    <div class="nav-container">
        <!-- Logo con carga dinámica -->
        <a href="index.php" class="nav-logo">
            <?php
            // Lógica de detección de logo tal como solicitaste
            $logo_path = 'https://via.placeholder.com/150x50?text=Fundación+JLC'; // Fallback
            if (function_exists('getSiteConfig') && getSiteConfig('logo')) {
                $logo_path = 'uploads/img/' . getSiteConfig('logo');
            } else if (file_exists('uploads/img/logo.png')) {
                $logo_path = 'uploads/img/logo.png';
            }
            ?>
            <img src="<?php echo $logo_path; ?>" alt="Fundación JLC">
        </a>

        <!-- Enlaces de navegación -->
        <ul class="nav-links">
            <li><a href="index.php">INICIO</a></li>
            <li><a href="nuestra-fundacion.php">NUESTRA FUNDACIÓN</a></li>
            <li><a href="servicios-corporativos.php">SERVICIOS CORPORATIVOS</a></li>
            <li><a href="pilares.php">PILARES</a></li>
            <li><a href="propuesta-de-valor.php">PROPUESTA DE VALOR</a></li>
            <li><a href="blog.php">BLOG</a></li>
            <li><a href="contacto.php">CONTACTO</a></li>
        </ul>
    </div>
</nav>