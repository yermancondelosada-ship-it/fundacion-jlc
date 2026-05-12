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
            <?php
            $default_links = [
                ['url' => 'index.php', 'text' => 'INICIO'],
                ['url' => 'nuestra-fundacion.php', 'text' => 'NUESTRA FUNDACIÓN'],
                ['url' => 'servicios-corporativos.php', 'text' => 'SERVICIOS CORPORATIVOS'],
                ['url' => 'pilares.php', 'text' => 'PILARES'],
                ['url' => 'propuesta-de-valor.php', 'text' => 'PROPUESTA DE VALOR'],
                ['url' => 'blog.php', 'text' => 'BLOG'],
                ['url' => 'contacto.php', 'text' => 'CONTACTO']
            ];
            $nav_links = $default_links;
            if (function_exists('getSiteConfig')) {
                $saved_links = getSiteConfig('navbar_links');
                if (!empty($saved_links)) {
                    $decoded = json_decode($saved_links, true);
                    if (is_array($decoded) && count($decoded) > 0) {
                        $nav_links = $decoded;
                    }
                }
            }
            
            foreach ($nav_links as $link) {
                echo '<li><a href="' . htmlspecialchars($link['url']) . '">' . htmlspecialchars(strtoupper($link['text'])) . '</a></li>';
            }
            ?>
        </ul>
    </div>
</nav>