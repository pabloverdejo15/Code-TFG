<?php
require_once 'model/Fee.php';
require_once 'model/Community.php';
require_once 'model/db.php';
require_once 'model/Channel.php'; // Required if we extract sidebar

class FeeController {

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

        // Fetch Channels for the sidebar
        $channelModel = new Channel();
        $channels = $channelModel->getByCommunity($community_id, $user_id);

        // Fetch Fees
        $feeModel = new Fee();
        $fees = $feeModel->getByUserAndCommunity($user_id, $community_id);

        require_once 'view/fees/index.php';
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
                die("Acceso denegado. Solo administradores pueden crear cuotas.");
            }
            
            // Get all community members
            $communityModel = new Community();
            $members = $communityModel->getMembers($community_id);
            
            $concepto = $_POST['concepto'];
            $monto = $_POST['monto'];
            $fecha_vencimiento = $_POST['fecha_vencimiento'];
            $batch_id = uniqid('fee_batch_');
            
            $feeModel = new Fee();
            if ($feeModel->createForCommunity($community_id, $concepto, $monto, $fecha_vencimiento, $batch_id, $members)) {
                header("Location: " . BASE_URL . "?controller=Fee&action=index&community_id=" . $community_id . "&" . SID);
            } else {
                echo "Error al crear cuotas.";
            }
        }
    }

    public function pay() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $fee_id = isset($input['fee_id']) ? $input['fee_id'] : null;
            $user_id = $_SESSION['user_id'];
            
            if (!$fee_id) {
                echo json_encode(['success' => false, 'message' => 'Falta el ID de la cuota']);
                exit;
            }
            
            $feeModel = new Fee();
            if ($feeModel->simulatePayment($fee_id, $user_id)) {
                echo json_encode(['success' => true, 'message' => 'Pago realizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al procesar el pago o la cuota ya está pagada.']);
            }
            exit;
        }
    }
}
?>
