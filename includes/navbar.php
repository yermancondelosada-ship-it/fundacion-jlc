<?php
/**
 * Navbar Profesional - Fundación JLC
 */
?>
<nav class="main-navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <img src="https://via.placeholder.com/150x50?text=Fundación+JLC" alt="Fundación JLC">
        </a>
        
        <ul class="nav-links">
            <li class="dropdown">
                <a href="#" class="dropbtn">Pilares Estratégicos <i class="fas fa-chevron-down"></i></a>
                <div class="dropdown-content">
                    <a href="pilares.php"><i class="fas fa-laptop-code"></i> Tecnología e Innovación</a>
                    <a href="pilares.php"><i class="fas fa-user-graduate"></i> Educación Multinivel</a>
                    <a href="pilares.php"><i class="fas fa-leaf"></i> Turismo Sostenible</a>
                    <a href="pilares.php"><i class="fas fa-hand-holding-heart"></i> Gestión Socio-Ambiental</a>
                </div>
            </li>
            <li><a href="nuestra-fundacion.php">Nosotros</a></li>
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
    background: var(--white);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 0.5rem 2rem;
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
    gap: 2rem;
    align-items: center;
}

.nav-links a {
    text-decoration: none;
    color: var(--text-dark);
    font-weight: 500;
    transition: color 0.3s;
}

.nav-links a:hover {
    color: var(--primary-blue);
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
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 250px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 8px;
    overflow: hidden;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {background-color: #f1f1f1}

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
