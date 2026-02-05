<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unirse a Comunidad - Hello Neighbor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <style>
        .page-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .join-card {
            background: white;
            width: 100%;
            max-width: 480px;
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: var(--shadow-xl);
            text-align: center;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
        }
        .input-code {
            font-size: 1.5rem;
            letter-spacing: 0.2rem;
            text-align: center;
            text-transform: uppercase;
            padding: 1rem;
        }
    </style>
</head>
<body>

<div class="page-content">
    <div class="join-card">
        <div class="icon-circle">🔑</div>
        <h1 style="margin-bottom: 0.5rem;">Unirse a una Comunidad</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem; line-height: 1.6;">
            Introduce el <strong>Código de Acceso</strong> proporcionado por el administrador de tu comunidad para entrar.
        </p>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error" style="text-align: left;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>?controller=Community&action=join&<?php echo SID; ?>" method="POST">
            <div class="form-group">
                <input type="text" id="codigo_acceso" name="codigo_acceso" class="input-code" required placeholder="A1B2C3" maxlength="10" autofocus>
            </div>
            
            <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary" style="justify-content: center; width: 100%; padding: 1rem; font-size: 1.1rem;">
                    Unirse ahora
                </button>
                <a href="<?php echo BASE_URL; ?>?controller=Community&action=index&<?php echo SID; ?>" class="btn" style="justify-content: center; color: var(--text-muted);">
                    Cancelar y volver
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Canvas for Animated Background -->
<canvas id="darkveil-canvas" style="position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; pointer-events:none;"></canvas>

<script type="module" src="<?php echo BASE_URL; ?>js/DarkVeil.js?v=<?php echo time(); ?>"></script>
</body>
</html>
