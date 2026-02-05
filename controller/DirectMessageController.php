<?php
require_once 'model/DirectMessage.php';
require_once 'model/User.php';

class DirectMessageController {
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?controller=Auth&action=login");
            exit;
        }
    }

    public function chat() {
        if (!isset($_GET['user_id'])) {
            die("Usuario no especificado.");
        }

        $target_user_id = $_GET['user_id'];
        $my_user_id = $_SESSION['user_id'];

        if ($target_user_id == $my_user_id) {
            die("No puedes hablar contigo mismo.");
        }

        $userModel = new User();
        $target_user = $userModel->findById($target_user_id);
        
        if (!$target_user) {
            die("Usuario no encontrado.");
        }

        $dmModel = new DirectMessage();
        $messages = $dmModel->getConversation($my_user_id, $target_user_id);

        require_once 'view/dm/chat.php';
    }

    public function send() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $receptor_id = $_POST['receptor_id'];
            $contenido = trim($_POST['contenido']);
            
            if (!empty($contenido)) {
                $dm = new DirectMessage();
                $dm->id_emisor = $_SESSION['user_id'];
                $dm->id_receptor = $receptor_id;
                $dm->contenido = $contenido;
                $dm->create();
            }
            
            // Redirect back to chat
            header("Location: " . BASE_URL . "?controller=DirectMessage&action=chat&user_id=" . $receptor_id . "&" . SID);
        }
    }
}
?>
