<?php
require_once 'model/User.php';

class AuthController {
    
    // Mostrar formulario de registro
    public function register() {
        require_once 'view/auth/register.php';
    }

    // Procesar registro
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();
            $user->nombre = $_POST['nombre'];
            $user->email = $_POST['email'];
            $user->contrasena = $_POST['contrasena'];

            if (strlen($_POST['contrasena']) < 8 || !preg_match("/[A-Z]/", $_POST['contrasena']) || !preg_match("/[0-9]/", $_POST['contrasena'])) {
                $error = "La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.";
                require_once 'view/auth/register.php';
            } elseif ($user->emailExists()) {
                $error = "El correo electrónico ya está registrado.";
                require_once 'view/auth/register.php';
            } elseif ($user->create()) {
                // Registro exitoso, redirigir al login
                header("Location: " . BASE_URL . "?controller=Auth&action=login&success=1");
            } else {
                $error = "Error al registrar el usuario.";
                require_once 'view/auth/register.php';
            }
        }
    }

    // Mostrar formulario de login
    public function login() {
        require_once 'view/auth/login.php';
    }

    // Procesar login
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User(); 
            $user->email = $_POST['email'];
            $user->contrasena = $_POST['contrasena'];

            if ($user->login()) {
                $_SESSION['user_id'] = $user->id_usuario;
                $_SESSION['user_name'] = $user->nombre;
                $_SESSION['user_avatar'] = $user->avatar;
                
                // --- REMEMBER ME LOGIC ---
                // Create a secure signature: UserID + PasswordHash (from DB)
                // If user changes password, the hash changes, invalidating the cookie.
                // We need the password hash. The login() method might not populate it in $user object public props, 
                // but we can fetch it or trust the logic. 
                // Let's verify: $user->login() verifies partials but doesn't store the hash in public prop 'contrasena'.
                // We should re-fetch or modify login to store it. 
                // Actually, let's fetch the user data properly or use a simpler secret key for now if hash isn't available, 
                // BUT the plan said to use the hash.
                
                // Let's get the hash cleanly.
                $loggedUser = $user->findById($user->id_usuario);
                if ($loggedUser) {
                    $signature = hash_hmac('sha256', $user->id_usuario . $loggedUser['contrasena'], 'SECRET_KEY_NEIGHBOR');
                    $cookieValue = $user->id_usuario . ':' . $signature;
                    // Set cookie for 30 days
                    setcookie('remember_me', $cookieValue, time() + (86400 * 30), "/");
                }
                
                // Flag for welcome animation
                $_SESSION['show_welcome'] = true;
                
                // SID se anexa para mantener la sesión
                header("Location: " . BASE_URL . "?controller=Community&action=index&" . SID);
            } else {
                $error = "Credenciales incorrectas.";
                require_once 'view/auth/login.php';
            }
        }
    }

    public function logout() {
        session_destroy();
        // Delete cookie
        setcookie('remember_me', '', time() - 3600, "/");
        header("Location: " . BASE_URL . "?controller=Auth&action=login");
    }
}
?>
