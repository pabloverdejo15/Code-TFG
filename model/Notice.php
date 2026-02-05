<?php
require_once 'model/db.php';

class Notice {
    private $conn;
    private $table = 'avisos';

    public $id_aviso;
    public $id_comunidad;
    public $titulo;
    public $descripcion;
    public $tipo;
    public $fecha_publicacion;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (id_comunidad, titulo, descripcion, tipo, estado) VALUES (:id_comunidad, :titulo, :descripcion, :tipo, 'abierto')";
        $stmt = $this->conn->prepare($query);

        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        $stmt->bindParam(':id_comunidad', $this->id_comunidad);
        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':tipo', $this->tipo);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByCommunity($id_comunidad) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_comunidad = :id_comunidad ORDER BY fecha_publicacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_comunidad', $id_comunidad);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id_aviso = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_aviso);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
