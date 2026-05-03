<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';

try {
    $db = Database::getInstance();
    
    $email = 'yermanconde@hotmail.com';
    $password = 'German2026';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "UPDATE users SET password = :password WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':password' => $hashed_password,
        ':email' => $email
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo "<h1>Éxito</h1>";
        echo "<p>La contraseña para el usuario <strong>" . htmlspecialchars($email) . "</strong> ha sido actualizada correctamente.</p>";
        echo "<p><strong>IMPORTANTE:</strong> Por favor, elimina este archivo (reset.php) inmediatamente después de verificar que puedes iniciar sesión por razones de seguridad.</p>";
    } else {
        echo "<h1>Atención</h1>";
        echo "<p>No se encontró ningún usuario con el correo electrónico <strong>" . htmlspecialchars($email) . "</strong>.</p>";
        
        // Let's check if the user exists at all
        $check = $db->query("SELECT email FROM users");
        $users = $check->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Usuarios en la base de datos:</h3><ul>";
        foreach ($users as $user) {
            echo "<li>" . htmlspecialchars($user['email']) . "</li>";
        }
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "<h1>Error</h1>";
    echo "<p>Ocurrió un error al actualizar la base de datos: " . $e->getMessage() . "</p>";
}
?>
