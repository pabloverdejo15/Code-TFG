<?php
require_once 'model/db.php';

class User {
    private $conn;
    private $table = 'usuarios';

    public $id_usuario;
    public $nombre;
    public $email;
    public $contrasena;
    public $avatar;
    public $descripcion;
    public $estado;

    // ... (existing code)

    public function update() {
        $query = "UPDATE " . $this->table . " SET nombre = :nombre, avatar = :avatar, descripcion = :descripcion WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':avatar', $this->avatar);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Registrar nuevo usuario
    public function create() {
        $query = "INSERT INTO " . $this->table . " (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));
        // Hash de contraseña
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contrasena', $password_hash);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar login
    public function login() {
        $query = "SELECT id_usuario, nombre, contrasena, avatar, estado FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($this->contrasena, $row['contrasena'])) {
                $this->id_usuario = $row['id_usuario'];
                $this->nombre = $row['nombre'];
                $this->avatar = $row['avatar'];
                $this->estado = $row['estado'];
                return true;
            }
        }
        return false;
    }

    // Comprobar si email existe
    public function emailExists() {
        $query = "SELECT id_usuario FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_usuario = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
