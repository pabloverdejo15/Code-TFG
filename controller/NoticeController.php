<?php
require_once 'model/Notice.php';
require_once 'model/Community.php';
require_once 'model/db.php';

class NoticeController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?controller=Auth&action=login&" . SID);
            exit;
        }
    }

    public function index() {
        if (!isset($_GET['community_id'])) {
            die("Falta ID comunidad");
        }
        $community_id = $_GET['community_id'];
        $user_id = $_SESSION['user_id'];

        // Verificar membresía y rol
        $db = (new Database())->getConnection();
        $query = "SELECT rol FROM usuario_comunidad WHERE id_usuario = :uid AND id_comunidad = :cid";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':cid', $community_id);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            die("No tienes permiso para acceder a esta comunidad.");
        }
        $membership = $stmt->fetch(PDO::FETCH_ASSOC);
        $is_admin = ($membership['rol'] === 'admin');

        // Obtener info comunidad
        $queryComm = "SELECT * FROM comunidades WHERE id_comunidad = :cid";
        $stmtComm = $db->prepare($queryComm);
        $stmtComm->bindParam(':cid', $community_id);
        $stmtComm->execute();
        $community = $stmtComm->fetch(PDO::FETCH_ASSOC);

        // Obtener avisos
        $noticeModel = new Notice();
        $notices = $noticeModel->getByCommunity($community_id);

        require_once 'view/notice/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['community_id'])) die("Falta ID comunidad");
            
            $community_id = $_POST['community_id'];
            $user_id = $_SESSION['user_id'];
            
            // Verificar permisos de admin
            $db = (new Database())->getConnection();
            $query = "SELECT rol FROM usuario_comunidad WHERE id_usuario = :uid AND id_comunidad = :cid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':uid', $user_id);
            $stmt->bindParam(':cid', $community_id);
            $stmt->execute();
            $membership = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$membership || $membership['rol'] !== 'admin') {
                die("Acceso denegado. Solo administradores pueden crear avisos.");
            }
            
            $notice = new Notice();
            $notice->id_comunidad = $community_id;
            $notice->titulo = $_POST['titulo'];
            $notice->descripcion = $_POST['descripcion'];
            $notice->tipo = $_POST['tipo'];

            if ($notice->create()) {
                header("Location: " . BASE_URL . "?controller=Notice&action=index&community_id=" . $community_id . "&" . SID);
            } else {
                echo "Error al crear aviso.";
            }
        }
    }

    public function delete() {
        if (!isset($_GET['id']) || !isset($_GET['community_id'])) {
            die("Datos insuficientes.");
        }

        $community_id = $_GET['community_id'];
        $user_id = $_SESSION['user_id'];
        
        // Verificar permisos de admin
        $db = (new Database())->getConnection();
        $query = "SELECT rol FROM usuario_comunidad WHERE id_usuario = :uid AND id_comunidad = :cid";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':cid', $community_id);
        $stmt->execute();
        $membership = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$membership || $membership['rol'] !== 'admin') {
            die("Acceso denegado. Solo administradores pueden borrar avisos.");
        }

        $notice = new Notice();
        $notice->id_aviso = $_GET['id'];
        
        if ($notice->delete()) {
            header("Location: " . BASE_URL . "?controller=Notice&action=index&community_id=" . $community_id . "&msg=deleted&" . SID);
        } else {
            echo "Error al eliminar.";
        }
    }
}
?>
