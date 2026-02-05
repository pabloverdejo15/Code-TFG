<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Hello Neighbor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=2.2">
    <style>
        /* CRITICAL CSS - INJECTED TO BYPASS CACHE */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevent scrollbars */
            background-color: #000000; /* Fallback */
        }
        
        body {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100vw;
            background: transparent !important; /* Allow canvas */
        }

        #darkveil-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0; /* Base layer */
            display: block;
        }

        .auth-container {
            position: relative;
            z-index: 10; /* Above canvas */
            background: rgba(10, 10, 10, 0.6) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 85, 255, 0.3);
            box-shadow: 0 0 50px rgba(0,0,0,0.9);
            color: white !important;
            padding: 3rem;
            border-radius: 1rem;
            width: 100%;
            max-width: 400px;
            margin: auto; /* Fallback */
        }

        /* Force text colors in container */
        .auth-container *, .auth-container h1, .auth-container p, .auth-container label {
            color: white !important;
        }
    </style>
</head>
<body>
    <div id="auth-canvas-placeholder"></div>

<div class="auth-container auth-container-dark">
    <h1 style="margin-bottom: 0.5rem; font-size: 1.75rem;">Bienvenido de nuevo</h1>
    <p class="text-muted" style="margin-bottom: 2rem;">Inicio de sesión en Hello Neighbor</p>
    
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
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="#0055FF"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <line x1="5" y1="12" x2="19" y2="12"></line>
      <polyline points="12 5 19 12 12 19"></polyline>
    </svg>
  </div>
</button>
    </form>
    
    <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem; font-size: 0.9rem;">
        <p style="color: var(--text-muted);">¿No tienes cuenta?</p>
        <a href="<?php echo BASE_URL; ?>?controller=Auth&action=register" style="color: var(--primary); font-weight: 600;">Crear cuenta nueva</a>
    </div>
</div>

<!-- Canvas for Animated Background used by module -->
<canvas id="darkveil-canvas" style="position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; pointer-events:none;"></canvas>

<script type="module" src="<?php echo BASE_URL; ?>js/DarkVeil.js?v=<?php echo time(); ?>"></script>

<!-- Global Loader -->
<?php include 'view/templates/loader.php'; ?>

</body>
<!-- Vite Development Scripts -->
</html>
