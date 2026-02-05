<?php
require_once '../model/db.php';

try {
    $db = (new Database())->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS mensajes_privados (
        id_mensaje_privado INT AUTO_INCREMENT PRIMARY KEY,
        id_emisor INT NOT NULL,
        id_receptor INT NOT NULL,
        contenido TEXT NOT NULL,
        fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        leido BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (id_emisor) REFERENCES usuarios(id_usuario),
        FOREIGN KEY (id_receptor) REFERENCES usuarios(id_usuario)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    echo "Tabla 'mensajes_privados' creada con éxito.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
