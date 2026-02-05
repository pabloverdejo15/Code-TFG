<?php
require_once 'model/User.php';

class UserController {
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "?controller=Auth&action=login");
            exit;
        }
    }

    public function profile() {
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        require_once 'view/user/profile.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User();
            $user = $userModel->findById($_SESSION['user_id']);
            
            $nombre = $_POST['nombre'];
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : ''; // Obtener descripcion
            $avatarPath = $user['avatar']; // Mantener anterior por defecto

            // Subida de imagen
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $target_dir = "uploads/avatars/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                
                $file_extension = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    $avatarPath = $target_file;
                    $_SESSION['user_avatar'] = $avatarPath; // Actualizar sesión
                }
            }

            $userModel->id_usuario = $_SESSION['user_id'];
            $userModel->nombre = $nombre;
            $userModel->avatar = $avatarPath;
            $userModel->descripcion = $descripcion; // Asignar descripcion

            if ($userModel->update()) {
                $_SESSION['user_name'] = $nombre; // Actualizar sesión
                // ... rest of logic
                $redirect = isset($_POST['redirect_to']) ? urldecode($_POST['redirect_to']) : BASE_URL . "?controller=User&action=profile&" . SID;
                 if (strpos($redirect, '?') === false) {
                     $redirect .= "?" . SID;
                } else {
                     if (strpos($redirect, 'PHPSESSID') === false) {
                         $redirect .= "&" . SID;
                     }
                }
                
                header("Location: " . $redirect);
            } else {
                $error = "Error al actualizar perfil.";
                require_once 'view/user/profile.php';
            }
        }
    }

    public function get_public_profile() {
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'No ID provided']);
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->findById($_GET['id']);
        
        if ($user) {
            echo json_encode([
                'id' => $user['id_usuario'],
                'nombre' => $user['nombre'],
                'avatar' => $user['avatar'] ? BASE_URL . $user['avatar'] : null,
                'descripcion' => $user['descripcion']
            ]);
        } else {
            echo json_encode(['error' => 'User not found']);
        }
        exit;
    }
}
?>
