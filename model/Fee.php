<?php
require_once 'model/db.php';

class Fee {
    private $conn;
    private $table = 'cuotas';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createForCommunity($community_id, $concepto, $monto, $fecha_vencimiento, $batch_id, $members) {
        $query = "INSERT IGNORE INTO " . $this->table . " 
                  (id_comunidad, id_usuario, batch_id, concepto, monto, fecha_vencimiento) 
                  VALUES (:id_comunidad, :id_usuario, :batch_id, :concepto, :monto, :fecha_vencimiento)";
        
        $stmt = $this->conn->prepare($query);
        $success = true;
        
        // Loop through each member and assign the fee
        foreach ($members as $member) {
            $stmt->bindValue(':id_comunidad', $community_id, PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario', $member['id_usuario'], PDO::PARAM_INT);
            $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_STR);
            $stmt->bindValue(':concepto', htmlspecialchars(strip_tags($concepto)), PDO::PARAM_STR);
            $stmt->bindValue(':monto', $monto, PDO::PARAM_STR);
            $stmt->bindValue(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                $success = false;
            }
        }
        
        return $success;
    }

    public function getByUserAndCommunity($user_id, $community_id) {
        // Calculate overdue dynamically and sort correctly
        $query = "SELECT *, 
                  IF(estado = 'pendiente' AND fecha_vencimiento < CURDATE(), 'vencida', estado) AS estado_calculado
                  FROM " . $this->table . "
                  WHERE id_usuario = :id_usuario AND id_comunidad = :id_comunidad
                  ORDER BY FIELD(estado_calculado, 'pendiente', 'vencida', 'pagada'), fecha_vencimiento ASC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':id_comunidad', $community_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function simulatePayment($fee_id, $user_id) {
        // First check if fee exists and belongs to user
        $query = "SELECT monto FROM " . $this->table . " WHERE id_cuota = :id_cuota AND id_usuario = :id_usuario AND estado = 'pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cuota', $fee_id, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $fee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$fee) {
            return false;
        }
        
        $this->conn->beginTransaction();
        
        try {
            // Update fee to paid
            $updQuery = "UPDATE " . $this->table . " SET estado = 'pagada' WHERE id_cuota = :id_cuota";
            $updStmt = $this->conn->prepare($updQuery);
            $updStmt->bindParam(':id_cuota', $fee_id, PDO::PARAM_INT);
            $updStmt->execute();
            
            // Insert into pagos
            $insQuery = "INSERT INTO pagos (id_cuota, id_usuario, monto, metodo, estado) VALUES (:id_cuota, :id_usuario, :monto, 'Simulacion', 'completado')";
            $insStmt = $this->conn->prepare($insQuery);
            $insStmt->bindParam(':id_cuota', $fee_id, PDO::PARAM_INT);
            $insStmt->bindParam(':id_usuario', $user_id, PDO::PARAM_INT);
            $insStmt->bindParam(':monto', $fee['monto'], PDO::PARAM_STR);
            $insStmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>
