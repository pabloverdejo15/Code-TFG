<?php
require_once __DIR__ . '/../model/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Crear tabla cuotas
    $sql_cuotas = "CREATE TABLE IF NOT EXISTS cuotas (
        id_cuota INT AUTO_INCREMENT PRIMARY KEY,
        id_comunidad INT NOT NULL,
        id_usuario INT NOT NULL,
        batch_id VARCHAR(50) NOT NULL,
        concepto VARCHAR(255) NOT NULL,
        monto DECIMAL(10,2) NOT NULL,
        fecha_vencimiento DATE NOT NULL,
        estado ENUM('pendiente', 'pagada') DEFAULT 'pendiente',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY idx_unique_fee (id_usuario, batch_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $conn->exec($sql_cuotas);
    echo "Tabla 'cuotas' verificada/creada exitosamente.\n";

    // Crear tabla pagos
    $sql_pagos = "CREATE TABLE IF NOT EXISTS pagos (
        id_pago INT AUTO_INCREMENT PRIMARY KEY,
        id_cuota INT NOT NULL,
        id_usuario INT NOT NULL,
        monto DECIMAL(10,2) NOT NULL,
        metodo VARCHAR(50) DEFAULT 'Simulacion',
        estado VARCHAR(50) DEFAULT 'completado',
        fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $conn->exec($sql_pagos);
    echo "Tabla 'pagos' verificada/creada exitosamente.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
