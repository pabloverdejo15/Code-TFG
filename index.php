<?php
// SOLUCIÓN ROBUSTA PARA MÚLTIPLES CUENTAS (Sesiones por URL)
ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
ini_set('session.use_trans_sid', 1);
ini_set('arg_separator.output', '&');

// Iniciar sesión
session_start();

// --- PERSISTENT LOGIN CHECK ---
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    // Autoload is not triggered yet, so we need to manually require or trigger it.
    // Let's rely on the autoloader defined below, but we need to move it UP or manually require User.
    // Moving autoloader UP is cleaner.
    
    // TEMPORARY: manual require to avoid re-structuring everything yet
    require_once 'model/db.php';
    require_once 'model/User.php';
    
    list($user_id, $signature) = explode(':', $_COOKIE['remember_me']);
    
    $user = new User();
    $userData = $user->findById($user_id);
    
    if ($userData) {
        $validSignature = hash_hmac('sha256', $user_id . $userData['contrasena'], 'SECRET_KEY_NEIGHBOR');
        
        if (hash_equals($validSignature, $signature)) {
            // Restore Session
            $_SESSION['user_id'] = $userData['id_usuario'];
            $_SESSION['user_name'] = $userData['nombre'];
            $_SESSION['user_avatar'] = $userData['avatar'];
            
            // Flag for welcome animation
            $_SESSION['show_welcome'] = true;
        }
    }
}

// Configuración global
define('BASE_URL', 'http://localhost/Code%20TFG/');

// Autocarga de clases (simple)
spl_autoload_register(function ($class_name) {
    if (file_exists('controller/' . $class_name . '.php')) {
        require_once 'controller/' . $class_name . '.php';
    } elseif (file_exists('model/' . $class_name . '.php')) {
        require_once 'model/' . $class_name . '.php';
    } elseif (file_exists('config/' . $class_name . '.php')) {
        require_once 'config/' . $class_name . '.php';
    }
});

// Enrutamiento básico
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'Auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

$controllerName = $controller . 'Controller';

if (class_exists($controllerName)) {
    $controllerInstance = new $controllerName();
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        // Acción no encontrada, redirigir a error o login
        echo "Error: La acción '$action' no existe en '$controllerName'.";
    }
} else {
    // Controlador no encontrado
    echo "Error: El controlador '$controllerName' no existe.";
}
?>
