<?php
require_once 'model/db.php';

class Message {
    private $conn;
    private $table = 'mensajes';

    public $id_mensaje;
    public $id_canal;
    public $id_usuario;
    public $contenido;
    public $fecha_publicacion;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (id_canal, id_usuario, contenido) VALUES (:id_canal, :id_usuario, :contenido)";
        $stmt = $this->conn->prepare($query);

        $this->contenido = htmlspecialchars(strip_tags($this->contenido));

        $stmt->bindParam(':id_canal', $this->id_canal);
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':contenido', $this->contenido);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByChannel($id_canal) {
        // Query mejorada para obtener info de usuario, lecturas y totales
        $query = "SELECT m.*, u.nombre as nombre_usuario, u.avatar,
                  (SELECT COUNT(*) FROM mensaje_lecturas ml WHERE ml.id_mensaje = m.id_mensaje) as num_leidos,
                  (SELECT COUNT(*) FROM usuario_comunidad uc 
                      JOIN canales c ON c.id_comunidad = uc.id_comunidad 
                      WHERE c.id_canal = m.id_canal) as total_miembros
                  FROM " . $this->table . " m 
                  JOIN usuarios u ON m.id_usuario = u.id_usuario 
                  WHERE m.id_canal = :id_canal 
                  ORDER BY m.fecha_publicacion ASC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_canal', $id_canal);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id_canal, $id_usuario) {
        // Marcar todos los mensajes de este canal LEÍDOS por este usuario
        // Usamos INSERT IGNORE para evitar duplicados si ya leyó
        $query = "INSERT IGNORE INTO mensaje_lecturas (id_mensaje, id_usuario)
                  SELECT id_mensaje, :id_usuario FROM mensajes WHERE id_canal = :id_canal";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_canal', $id_canal);
        return $stmt->execute();
    }
}
?>
