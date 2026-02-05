<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Canal - Hello Neighbor</title>
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
        <h1 style="margin-bottom: 0.5rem;">Nuevo Canal</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Crea un espacio de conversación para la comunidad</p>

        <form action="<?php echo BASE_URL; ?>?controller=Channel&action=create&<?php echo SID; ?>" method="POST">
            <input type="hidden" name="community_id" value="<?php echo $_GET['community_id']; ?>">
            
            <div class="form-group">
                <label for="nombre">Nombre del Canal</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej. General, Deportes..." autofocus>
            </div>
            
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #fff;">
                    <option value="general">General</option>
                    <option value="averias">Averías</option>
                    <option value="anuncios">Anuncios</option>
                    <option value="social">Social</option>
                    <option value="otros">Otros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <input type="text" id="descripcion" name="descripcion" placeholder="Breve descripción del canal...">
            </div>
            
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; align-items: center;">
                <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $_GET['community_id']; ?>&<?php echo SID; ?>" style="color: var(--text-muted); font-size: 0.95rem;">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="padding-left: 2rem; padding-right: 2rem;">Crear Canal</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
