<?php
require_once 'model/db.php';

class Pago {
    private $conn;
    private $table = 'pagos';

    public $id_pago;
    public $id_cuota;
    public $id_usuario;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Marcar un pago como pagado
    public function marcarPagado($id_cuota, $id_usuario) {
        $query = "UPDATE " . $this->table . "
                  SET estado = 'pagado', fecha_pago = NOW()
                  WHERE id_cuota = :id_cuota AND id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cuota',   $id_cuota);
        $stmt->bindParam(':id_usuario', $id_usuario);
        return $stmt->execute();
    }

    // Obtener todos los pagos de una cuota concreta (para el admin)
    public function getByCuota($id_cuota) {
        $query = "SELECT p.*, u.nombre as nombre_usuario, u.avatar
                  FROM " . $this->table . " p
                  JOIN usuarios u ON p.id_usuario = u.id_usuario
                  WHERE p.id_cuota = :id_cuota
                  ORDER BY p.estado ASC, u.nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cuota', $id_cuota);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los pagos de un usuario en una comunidad (para el vecino)
    public function getByUsuarioYComunidad($id_usuario, $id_comunidad) {
        $query = "SELECT p.*, c.concepto, c.importe, c.fecha_vencimiento, c.periodicidad
                  FROM " . $this->table . " p
                  JOIN cuotas c ON p.id_cuota = c.id_cuota
                  WHERE p.id_usuario = :id_usuario
                    AND c.id_comunidad = :id_comunidad
                  ORDER BY c.fecha_vencimiento ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario',   $id_usuario);
        $stmt->bindParam(':id_comunidad', $id_comunidad);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Comprobar si un usuario ya ha pagado una cuota concreta
    public function haPagado($id_cuota, $id_usuario) {
        $query = "SELECT estado FROM " . $this->table . "
                  WHERE id_cuota = :id_cuota AND id_usuario = :id_usuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cuota',   $id_cuota);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['estado'] === 'pagado';
    }
}
?>
