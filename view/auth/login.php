<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Hello Neighbor</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=2.2">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/welcome.css">
    <style>
        body {
            background-color: var(--bg-base);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            position: relative;
        }
        .navbar-minimal {
            position: absolute;
            top: 2rem;
            left: 2rem;
            z-index: 100;
        }
        .navbar-minimal .brand {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        .navbar-minimal .brand:hover {
            opacity: 1;
        }
        .auth-container {
            width: 100%;
            max-width: 420px;
        }
    </style>
</head>
<body>
    <!-- Premium Background Glows -->
    <div class="glow-bg glow-1"></div>
    <div class="glow-bg glow-2"></div>

    <div class="navbar-minimal">
        <a href="<?php echo BASE_URL; ?>" class="brand">
            <i class="ph-fill ph-buildings"></i>
            <span>Hello Neighbor</span>
        </a>
    </div>

    <div class="auth-container auth-container-dark">
        <div style="text-align: left; margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem; font-size: 1.75rem; color: white;">Bienvenido de nuevo</h1>
            <p class="text-muted">Inicio de sesión en Hello Neighbor</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Cuenta creada. Por favor inicia sesión.</div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>?controller=Auth&action=authenticate" method="POST" style="text-align: left;">
            <div class="form__group field">
                <input type="email" class="form__field" placeholder="Email" name="email" id="email" required="">
                <label for="email" class="form__label">Correo Electrónico</label>
            </div>
            <div class="form__group field">
                <input type="password" class="form__field" placeholder="Password" name="contrasena" id="contrasena" required="">
                <label for="contrasena" class="form__label">Contraseña</label>
            </div>
            <button class="cssbuttons-io-button" type="submit">
                Iniciar Sesión
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0055FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </div>
            </button>
        </form>
        
        <div style="margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem; font-size: 0.9rem; display: flex; justify-content: space-between; align-items: center;">
            <p style="color: var(--text-muted); margin: 0;">¿No tienes cuenta?</p>
            <a href="<?php echo BASE_URL; ?>?controller=Auth&action=register" style="color: var(--primary-light); font-weight: 600;">Crear cuenta</a>
        </div>
    </div>

    <!-- Global Loader -->
    <?php include 'view/templates/loader.php'; ?>

</body>
</html>
