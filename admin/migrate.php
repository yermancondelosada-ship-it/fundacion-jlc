<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

try {
    $db = Database::getInstance();

    // 1. Añadir 'tipo' a 'lecciones' si no existe
    $col_exists = $db->query("SHOW COLUMNS FROM lecciones LIKE 'tipo'")->fetch();
    if (!$col_exists) {
        $db->exec("ALTER TABLE lecciones ADD COLUMN tipo ENUM('video', 'actividad', 'evaluacion') DEFAULT 'video' AFTER modulo_id");
        echo "Columna 'tipo' añadida a 'lecciones'.\n";
    } else {
        echo "Columna 'tipo' ya existe en 'lecciones'.\n";
    }

    // 2. Crear tabla 'progreso_estudiantes'
    $db->exec("CREATE TABLE IF NOT EXISTS progreso_estudiantes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        curso_id INT NOT NULL,
        leccion_id INT NOT NULL,
        fecha_completado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY idx_user_leccion (user_id, leccion_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
        FOREIGN KEY (leccion_id) REFERENCES lecciones(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "Tabla 'progreso_estudiantes' verificada/creada.\n";

} catch (PDOException $e) {
    echo "Error de DB: " . $e->getMessage();
}
?>