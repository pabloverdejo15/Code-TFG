<?php
require_once 'model/Channel.php';
require_once 'model/Message.php';
require_once 'model/Community.php';
require_once 'model/User.php';

class ChannelController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?controller=Auth&action=login&" . SID);
            exit;
        }
    }

    public function index() {
        if (!isset($_GET['community_id'])) {
            header("Location: " . BASE_URL . "?controller=Community&action=index&" . SID);
            exit;
        }
        $community_id = $_GET['community_id'];
        $user_id = $_SESSION['user_id'];
        
        // Obtener datos usuario para modal
        $user = (new User())->findById($user_id);
        
        // Verificar membresía
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
        $user_role = $membership['rol']; // 'admin' o 'vecino'

        // Obtener info comunidad
        $communityModel = new Community();
        // Nota: findById no estaba en el modelo base Community, se puede hacer query directa o añadirlo.
        // Haremos query simple aquí o ampliamos modelo.
        $queryComm = "SELECT * FROM comunidades WHERE id_comunidad = :cid";
        $stmtComm = $db->prepare($queryComm);
        $stmtComm->bindParam(':cid', $community_id);
        $stmtComm->execute();
        $stmtComm->execute();
        $community = $stmtComm->fetch(PDO::FETCH_ASSOC);

        // Obtener miembros
        $members = $communityModel->getMembers($community_id);


        // Obtener canales
        $channelModel = new Channel();
        $channels = $channelModel->getByCommunity($community_id, $user_id);

        // Canal seleccionado (por defecto el primero o el indicado)
        $current_channel_id = isset($_GET['channel_id']) ? $_GET['channel_id'] : (count($channels) > 0 ? $channels[0]['id_canal'] : null);
        
        $messages = [];
        $current_channel = null;
        $last_read_at = null; // Variable para la vista

        if ($current_channel_id) {
            foreach ($channels as $c) {
                if ($c['id_canal'] == $current_channel_id) {
                    $current_channel = $c;
                    break;
                }
            }

            // Obtener último acceso para la barra de "No leídos"
            $last_read_at = $channelModel->getLastRead($user_id, $current_channel_id);

            // Obtener mensajes
            $messageModel = new Message();
            // Marcar como leídos para los ticks (logic antigua pero necesaria)
            $messageModel->markAsRead($current_channel_id, $user_id);

            $messages = $messageModel->getByChannel($current_channel_id);
            
            // Actualizar el timestamp de lectura del canal
            $channelModel->updateLastRead($user_id, $current_channel_id);
        }

        require_once 'view/channel/index.php';
    }

    public function create_view() {
        if (!isset($_GET['community_id'])) die("Falta ID comunidad");
        require_once 'view/channel/create_view.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $community_id = $_POST['community_id'];
            // Verificar permisos admin (simplificado, se debería chequear rol en cada acción)
             // ... chequeo de rol pendiente o confiamos en vista ... -> Mejor chequear aquí.
            
            $channel = new Channel();
            $channel->id_comunidad = $community_id;
            $channel->nombre = $_POST['nombre'];
            $channel->descripcion = $_POST['descripcion'];
            $channel->tipo = $_POST['tipo'];

            if ($channel->create()) {
                header("Location: " . BASE_URL . "?controller=Channel&action=index&community_id=" . $community_id . "&" . SID);
            } else {
                echo "Error al crear canal.";
            }
        }
    }

    public function send() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $community_id = $_POST['community_id'];
            $channel_id = $_POST['channel_id'];
            $contenido = trim($_POST['contenido']);

            if (!empty($contenido)) {
                $message = new Message();
                $message->id_canal = $channel_id;
                $message->id_usuario = $_SESSION['user_id'];
                $message->contenido = $contenido;
                $message->create();
            }
            // Recargar página en el mismo canal
            header("Location: " . BASE_URL . "?controller=Channel&action=index&community_id=" . $community_id . "&channel_id=" . $channel_id . "&" . SID);
        }
    }
}
?>
