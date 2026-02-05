<?php
require_once 'model/db.php';

class Community {
    private $conn;
    private $table = 'comunidades';

    public $id_comunidad;
    public $nombre;
    public $descripcion;
    public $direccion;
    public $codigo_acceso;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear comunidad
    public function create() {
        $query = "INSERT INTO " . $this->table . " (nombre, descripcion, direccion, codigo_acceso) VALUES (:nombre, :descripcion, :direccion, :codigo_acceso)";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        // Generar código único si no existe
        if (empty($this->codigo_acceso)) {
            $this->codigo_acceso = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        }

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':codigo_acceso', $this->codigo_acceso);

        if ($stmt->execute()) {
            $this->id_comunidad = $this->conn->lastInsertId(); // Guardar ID
            return true;
        }
        return false;
    }

    // Unirse a comunidad (Tabla pivote)
    public function join($user_id, $role = 'vecino') {
        $query = "INSERT INTO usuario_comunidad (id_usuario, id_comunidad, rol) VALUES (:id_usuario, :id_comunidad, :rol)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_usuario', $user_id);
        $stmt->bindParam(':id_comunidad', $this->id_comunidad);
        $stmt->bindParam(':rol', $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Buscar por código
    public function findByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE codigo_acceso = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Salir de la comunidad
    public function leave($user_id, $community_id) {
        // Verificar rol actual
        $queryCheck = "SELECT rol FROM usuario_comunidad WHERE id_usuario = :uid AND id_comunidad = :cid";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(':uid', $user_id);
        $stmtCheck->bindParam(':cid', $community_id);
        $stmtCheck->execute();
        $currentUser = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($currentUser && $currentUser['rol'] == 'admin') {
            // Contar otros admins
            $queryCount = "SELECT COUNT(*) as count FROM usuario_comunidad WHERE id_comunidad = :cid AND rol = 'admin' AND id_usuario != :uid";
            $stmtCount = $this->conn->prepare($queryCount);
            $stmtCount->bindParam(':cid', $community_id);
            $stmtCount->bindParam(':uid', $user_id);
            $stmtCount->execute();
            $result = $stmtCount->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] == 0) {
                // No hay otros admins, buscar el usuario más antiguo
                $queryOldest = "SELECT id_usuario FROM usuario_comunidad WHERE id_comunidad = :cid AND id_usuario != :uid ORDER BY fecha_union ASC LIMIT 1";
                $stmtOldest = $this->conn->prepare($queryOldest);
                $stmtOldest->bindParam(':cid', $community_id);
                $stmtOldest->bindParam(':uid', $user_id);
                $stmtOldest->execute();
                
                if ($stmtOldest->rowCount() > 0) {
                    // Promover a admin
                    $queryPromote = "UPDATE usuario_comunidad SET rol = 'admin' WHERE id_usuario = :uid AND id_comunidad = :cid";
                    $stmtPromote = $this->conn->prepare($queryPromote);
                    $stmtPromote->bindParam(':uid', $newAdminId);
                    $stmtPromote->bindParam(':cid', $community_id);
                    $stmtPromote->execute();
                }
            }
        }

        // Eliminar relación
        $query = "DELETE FROM usuario_comunidad WHERE id_usuario = :uid AND id_comunidad = :cid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':cid', $community_id);
        return $stmt->execute();
    }

    // Obtener miembros de la comunidad
    public function getMembers($community_id) {
        $query = "SELECT u.id_usuario, u.nombre, u.avatar, uc.rol 
                  FROM usuarios u 
                  JOIN usuario_comunidad uc ON u.id_usuario = uc.id_usuario 
                  WHERE uc.id_comunidad = :cid 
                  ORDER BY uc.rol ASC, u.nombre ASC"; // rol ASC effectively puts 'admin' before 'vecino' alphabetically? 'admin' < 'vecino', yes.
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cid', $community_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar comunidad
    public function delete($community_id) {
        // Primero eliminar relaciones en usuario_comunidad (si no hay CASCADE)
        $queryRel = "DELETE FROM usuario_comunidad WHERE id_comunidad = :cid";
        $stmtRel = $this->conn->prepare($queryRel);
        $stmtRel->bindParam(':cid', $community_id);
        $stmtRel->execute();

        // Eliminar comunidad
        $query = "DELETE FROM " . $this->table . " WHERE id_comunidad = :cid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cid', $community_id);
        return $stmt->execute();
    }
}
?>
