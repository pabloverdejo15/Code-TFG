<?php

require_once '../model/db.php';

try {
    $db = (new Database())->getConnection();
    
    // Add descripcion column if it doesn't exist
    $sql = "ALTER TABLE usuarios ADD COLUMN descripcion TEXT DEFAULT NULL AFTER avatar";
    
    $db->exec($sql);
    echo "Columna 'descripcion' añadida con éxito.";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "La columna 'descripcion' ya existe.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
