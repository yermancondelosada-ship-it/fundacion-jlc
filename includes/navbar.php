<?php
/**
 * Navbar Profesional - Fundación JLC
 */
?>
<nav class="main-navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <?php
            // Intentar cargar logo desde config si existe
            $logo_path = 'https://via.placeholder.com/150x50?text=Fundación+JLC'; // Fallback
            if (function_exists('getSiteConfig') && getSiteConfig('logo')) {
                $logo_path = 'uploads/img/' . getSiteConfig('logo');
            } else if (file_exists('uploads/img/logo.png')) {
                $logo_path = 'uploads/img/logo.png';
            }
            ?>
            <img src="<?php echo $logo_path; ?>" alt="Fundación JLC"
                style="max-height: 50px; background: white; padding: 5px; border-radius: 8px;">
        </a>

        <ul class="nav-links">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="nuestra-fundacion.php">Fundación</a></li>
            <li><a href="servicios-corporativos.php">Servicios</a></li>
            <li class="dropdown">
                <a href="pilares.php" class="dropbtn">Pilares <i class="fas fa-chevron-down"></i></a>
                <div class="dropdown-content">
                    <a href="pilares.php"><i class="fas fa-laptop-code"></i> Tecnología e Innovación</a>
                    <a href="pilares.php"><i class="fas fa-user-graduate"></i> Educación Multinivel</a>
                    <a href="pilares.php"><i class="fas fa-leaf"></i> Turismo Sostenible</a>
                    <a href="pilares.php"><i class="fas fa-hand-holding-heart"></i> Gestión Socio-Ambiental</a>
                </div>
            </li>
            <li><a href="propuesta-de-valor.php">Propuesta</a></li>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="contacto.php">Contacto</a></li>
            <li><a href="capacitate.php" class="btn-highlight">Capacítate</a></li>
        </ul>

        <div class="nav-auth" id="auth-section">
            <!-- Dinámico por JS (Firebase) -->
            <button id="btn-login-google" class="btn-google">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/action/google.svg" alt="Google">
                Entrar
            </button>
        </div>
    </div>
</nav>

<style>
    :root {
        --primary-blue: #004a99;
        --secondary-green: #28a745;
        --light-bg: #f8f9fa;
        --white: #ffffff;
        --text-dark: #333333;
    }

    .main-navbar {
        background: var(--primary-blue);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        padding: 0.8rem 2rem;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .nav-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .nav-links {
        display: flex;
        list-style: none;
        gap: 1.5rem;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .nav-links a {
        text-decoration: none;
        color: var(--white);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        transition: color 0.3s;
    }

    .nav-links a:hover {
        color: var(--secondary-green);
    }

    .btn-highlight {
        background: var(--secondary-green);
        color: white !important;
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
    }

    /* Dropdown logic */
    .dropdown {
        position: relative;
        display: inline-block;
        height: 100%;
        padding: 10px 0;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: var(--white);
        min-width: 280px;
        box-shadow: 0px 8px 20px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 8px;
        overflow: hidden;
        top: 100%;
        left: 0;
    }

    .dropdown-content a {
        color: var(--text-dark);
        padding: 12px 20px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        text-transform: none;
        font-weight: 500;
        border-bottom: 1px solid #f1f1f1;
    }

    .dropdown-content a i {
        color: var(--primary-blue);
        width: 20px;
        text-align: center;
    }

    .dropdown-content a:hover {
        background-color: #f8f9fa;
        color: var(--primary-blue);
        padding-left: 25px;
        /* Efecto animado */
        transition: all 0.3s ease;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    /* Auth Buttons */
    .btn-google {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #ddd;
        background: white;
        padding: 5px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-google:hover {
        background: #f1f1f1;
    }

    .btn-google img {
        width: 18px;
    }
</style>