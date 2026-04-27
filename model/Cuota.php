<?php
require_once 'model/db.php';

class Cuota {
    private $conn;
    private $table = 'cuotas';

    public $id_cuota;
    public $id_comunidad;
    public $concepto;
    public $importe;
    public $fecha_vencimiento;
    public $periodicidad;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear una nueva cuota y generar un registro de pago pendiente
    // para cada miembro actual de la comunidad
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (id_comunidad, concepto, importe, fecha_vencimiento, periodicidad)
                  VALUES (:id_comunidad, :concepto, :importe, :fecha_vencimiento, :periodicidad)";
        $stmt = $this->conn->prepare($query);

        $this->concepto = htmlspecialchars(strip_tags($this->concepto));

        $stmt->bindParam(':id_comunidad',      $this->id_comunidad);
        $stmt->bindParam(':concepto',          $this->concepto);
        $stmt->bindParam(':importe',           $this->importe);
        $stmt->bindParam(':fecha_vencimiento', $this->fecha_vencimiento);
        $stmt->bindParam(':periodicidad',      $this->periodicidad);

        if ($stmt->execute()) {
            $this->id_cuota = $this->conn->lastInsertId();

            // Crear un registro de pago pendiente para cada vecino de la comunidad
            $queryPagos = "INSERT INTO pagos (id_cuota, id_usuario)
                           SELECT :id_cuota, id_usuario
                           FROM usuario_comunidad
                           WHERE id_comunidad = :id_comunidad";
            $stmtPagos = $this->conn->prepare($queryPagos);
            $stmtPagos->bindParam(':id_cuota',      $this->id_cuota);
            $stmtPagos->bindParam(':id_comunidad',  $this->id_comunidad);
            $stmtPagos->execute();

            return true;
        }
        return false;
    }

    // Obtener todas las cuotas de una comunidad con el resumen de pagos
    public function getByCommunity($id_comunidad) {
        $query = "SELECT c.*,
                  (SELECT COUNT(*) FROM pagos p WHERE p.id_cuota = c.id_cuota AND p.estado = 'pagado')  as num_pagados,
                  (SELECT COUNT(*) FROM pagos p WHERE p.id_cuota = c.id_cuota AND p.estado = 'pendiente') as num_pendientes
                  FROM " . $this->table . " c
                  WHERE c.id_comunidad = :id_comunidad
                  ORDER BY c.fecha_vencimiento ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_comunidad', $id_comunidad);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una cuota por su ID
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_cuota = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cerrar una cuota (ya no acepta nuevos pagos)
    public function close($id_cuota) {
        $query = "UPDATE " . $this->table . " SET estado = 'cerrada' WHERE id_cuota = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_cuota);
        return $stmt->execute();
    }

    // Eliminar una cuota (borra también sus pagos por CASCADE)
    public function delete($id_cuota) {
        $query = "DELETE FROM " . $this->table . " WHERE id_cuota = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_cuota);
        return $stmt->execute();
    }
}
?>
