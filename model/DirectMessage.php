<?php
require_once 'model/db.php';

class DirectMessage {
    private $conn;
    private $table = 'mensajes_privados';

    public $id_mensaje_privado;
    public $id_emisor;
    public $id_receptor;
    public $contenido;
    public $fecha_envio;
    public $leido;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (id_emisor, id_receptor, contenido) VALUES (:id_emisor, :id_receptor, :contenido)";
        $stmt = $this->conn->prepare($query);

        $this->contenido = htmlspecialchars(strip_tags($this->contenido));

        $stmt->bindParam(':id_emisor', $this->id_emisor);
        $stmt->bindParam(':id_receptor', $this->id_receptor);
        $stmt->bindParam(':contenido', $this->contenido);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener conversación entre dos usuarios
    public function getConversation($user1, $user2) {
        // Seleccionar mensajes donde (emisor=u1 Y receptor=u2) O (emisor=u2 Y receptor=u1)
        $query = "SELECT m.*, u.nombre as nombre_emisor, u.avatar as avatar_emisor
                  FROM " . $this->table . " m
                  JOIN usuarios u ON m.id_emisor = u.id_usuario
                  WHERE (m.id_emisor = :user1 AND m.id_receptor = :user2)
                     OR (m.id_emisor = :user2 AND m.id_receptor = :user1)
                  ORDER BY m.fecha_envio ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user1', $user1);
        $stmt->bindParam(':user2', $user2);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
