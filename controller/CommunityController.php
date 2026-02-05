<?php
require_once 'model/Community.php';
require_once 'model/User.php';

class CommunityController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?controller=Auth&action=login&" . SID);
            exit;
        }
    }

    public function index() {
        // ... (unchanged)
        $user_id = $_SESSION['user_id'];
        $user = (new User())->findById($user_id); // Para modal perfil
        $database = new Database();
        $db = $database->getConnection();
        // ...
        $query = "SELECT c.*, uc.rol FROM comunidades c 
                  JOIN usuario_comunidad uc ON c.id_comunidad = uc.id_comunidad 
                  WHERE uc.id_usuario = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $communities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'view/community/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $community = new Community();
            $community->nombre = $_POST['nombre'];
            $community->descripcion = $_POST['descripcion'];
            $community->direccion = $_POST['direccion'];

            if ($community->create()) {
                $community->join($_SESSION['user_id'], 'admin');
                header("Location: " . BASE_URL . "?controller=Community&action=index&msg=created&" . SID);
            } else {
                $error = "Error al crear la comunidad.";
                require_once 'view/community/create.php';
            }
        } else {
            require_once 'view/community/create.php';
        }
    }

    public function join() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $code = $_POST['codigo_acceso'];
            $community = new Community();
            $foundCommunity = $community->findByCode($code);

            if ($foundCommunity) {
                // Verificar si ya es miembro
                $database = new Database();
                $db = $database->getConnection();
                $checkQuery = "SELECT * FROM usuario_comunidad WHERE id_usuario = :uid AND id_comunidad = :cid";
                $stmt = $db->prepare($checkQuery);
                $stmt->bindParam(':uid', $_SESSION['user_id']);
                $stmt->bindParam(':cid', $foundCommunity['id_comunidad']);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $error = "Ya eres miembro de esta comunidad.";
                    require_once 'view/community/join.php';
                } else {
                    $community->id_comunidad = $foundCommunity['id_comunidad'];
                    if ($community->join($_SESSION['user_id'])) {
                        header("Location: " . BASE_URL . "?controller=Community&action=index&msg=joined&" . SID);
                    } else {
                        $error = "Error al unirse a la comunidad.";
                        require_once 'view/community/join.php';
                    }
                }
            } else {
                $error = "Código de comunidad inválido.";
                require_once 'view/community/join.php';
            }
        } else {
            require_once 'view/community/join.php';
        }
    }
    public function leave() {
        if (isset($_GET['id'])) {
            $community_id = $_GET['id'];
            $community = new Community();
            if ($community->leave($_SESSION['user_id'], $community_id)) {
                header("Location: " . BASE_URL . "?controller=Community&action=index&msg=left&" . SID);
            } else {
                header("Location: " . BASE_URL . "?controller=Community&action=index&error=leave_failed&" . SID);
            }
        } else {
            header("Location: " . BASE_URL . "?controller=Community&action=index&" . SID);
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $community_id = $_GET['id'];
            $community = new Community();
            
            // Verificar si es admin (doble check de seguridad)
            $database = new Database();
            $db = $database->getConnection();
            $query = "SELECT rol FROM usuario_comunidad WHERE id_usuario = :uid AND id_comunidad = :cid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':uid', $_SESSION['user_id']);
            $stmt->bindParam(':cid', $community_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['rol'] == 'admin') {
                if ($community->delete($community_id)) {
                    header("Location: " . BASE_URL . "?controller=Community&action=index&msg=deleted&" . SID);
                } else {
                    header("Location: " . BASE_URL . "?controller=Community&action=index&error=delete_failed&" . SID);
                }
            } else {
                header("Location: " . BASE_URL . "?controller=Community&action=index&error=unauthorized&" . SID);
            }
        } else {
            header("Location: " . BASE_URL . "?controller=Community&action=index&" . SID);
        }
    }
}
?>
