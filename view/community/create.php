<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Comunidad - Hello Neighbor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <style>
        .page-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background-color: #f3f4f6;
        }
        .create-card {
            background: white;
            width: 100%;
            max-width: 550px;
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: var(--shadow-xl);
        }
    </style>
</head>
<body>

<div class="page-content">
    <div class="create-card">
        <h1 style="margin-bottom: 0.5rem;">Crea tu Comunidad</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Configura un nuevo espacio para tus vecinos</p>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>?controller=Community&action=create&<?php echo SID; ?>" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre de la Comunidad *</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej. Edificio Las Flores">
            </div>
            
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" placeholder="Calle Ejemplo, 123">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción Breve</label>
                <textarea id="descripcion" name="descripcion" rows="3" placeholder="Un espacio para coordinarnos..."></textarea>
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; align-items: center;">
                <a href="<?php echo BASE_URL; ?>?controller=Community&action=index&<?php echo SID; ?>" style="color: var(--text-muted); font-size: 0.95rem;">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="padding-left: 2rem; padding-right: 2rem;">Crear Comunidad</button>
            </div>
        </form>
    </div>
</div>

<!-- Canvas for Animated Background -->
<canvas id="darkveil-canvas" style="position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; pointer-events:none;"></canvas>

<style>
    /* Transparency overrides for DarkVeil visibility */
    .page-content { background-color: transparent !important; }
</style>

<script type="module" src="<?php echo BASE_URL; ?>js/DarkVeil.js?v=<?php echo time(); ?>"></script>
</body>
</html>
