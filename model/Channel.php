<?php
require_once 'model/db.php';

class Channel {
    private $conn;
    private $table = 'canales';

    public $id_canal;
    public $id_comunidad;
    public $nombre;
    public $tipo;
    public $descripcion;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (id_comunidad, nombre, tipo, descripcion) VALUES (:id_comunidad, :nombre, :tipo, :descripcion)";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        $stmt->bindParam(':id_comunidad', $this->id_comunidad);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':descripcion', $this->descripcion);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByCommunity($id_comunidad, $id_usuario = null) {
        $query = "SELECT c.*, 
                 (SELECT COUNT(*) FROM mensajes m 
                  WHERE m.id_canal = c.id_canal 
                  AND m.fecha_publicacion > COALESCE(
                      (SELECT ucl.fecha_ultimo_acceso FROM usuarios_canales_lectura ucl 
                       WHERE ucl.id_canal = c.id_canal AND ucl.id_usuario = :id_usuario), 
                      '1970-01-01')
                 ) as unread_count
                 FROM " . $this->table . " c 
                 WHERE c.id_comunidad = :id_comunidad 
                 ORDER BY c.fecha_creacion ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_comunidad', $id_comunidad);
        if ($id_usuario) {
            $stmt->bindParam(':id_usuario', $id_usuario);
        } else {
             // Fallback if no user - bind dummy/null or handle query differently. 
             // Simplest here: pass 0 or handle logic in query. 
             // Actually, simplest is to bind a non-existent user id if null passed, though we expect user usually.
             $dummy = 0;
             $stmt->bindParam(':id_usuario', $dummy);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastRead($id_usuario, $id_canal) {
        $query = "SELECT fecha_ultimo_acceso FROM usuarios_canales_lectura WHERE id_usuario = :id_usuario AND id_canal = :id_canal LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_canal', $id_canal);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['fecha_ultimo_acceso'];
        }
        return null;
    }

    public function updateLastRead($id_usuario, $id_canal) {
        // Toca insertar o actualizar
        $query = "INSERT INTO usuarios_canales_lectura (id_usuario, id_canal, fecha_ultimo_acceso) 
                  VALUES (:id_usuario, :id_canal, NOW()) 
                  ON DUPLICATE KEY UPDATE fecha_ultimo_acceso = NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_canal', $id_canal);
        return $stmt->execute();
    }
}
?>
