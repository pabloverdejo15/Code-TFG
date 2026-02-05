<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Hello Neighbor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=<?php echo time(); ?>">
    <style>
        .profile-card { max-width: 500px; margin: 3rem auto; background: white; padding: 2.5rem; border-radius: 1rem; box-shadow: var(--shadow-lg); text-align: center; }
        .avatar-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-light); margin-bottom: 1.5rem; }
        .no-avatar { width: 120px; height: 120px; border-radius: 50%; background: #e5e7eb; display: inline-flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 1.5rem; color: #9ca3af; }
    </style>
</head>
<body>

<div class="container-lg">
    <div class="header-bar">
        <h1>Mi Perfil</h1>
        <a href="<?php echo BASE_URL; ?>?controller=Community&action=index&<?php echo SID; ?>" class="btn btn-secondary">← Volver</a>
    </div>

    <div class="profile-card">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 2rem;">✅ Perfil actualizado correctamente.</div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>?controller=User&action=update&<?php echo SID; ?>" method="POST" enctype="multipart/form-data">
            
            <div style="position: relative; display: inline-block; margin-bottom: 2rem;">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo BASE_URL . $user['avatar']; ?>" alt="Avatar" class="avatar-preview" id="avatarPreview">
                <?php else: ?>
                    <div class="no-avatar" id="avatarPlaceholder">👤</div>
                <?php endif; ?>
                
                <label for="avatar" class="btn btn-primary" style="position: absolute; bottom: 0; right: -10px; border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-md);" title="Cambiar foto">
                    📷
                </label>
                <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewImage(this)">
            </div>

            <div class="form-group" style="text-align: left;">
                <label for="nombre" style="font-weight: 600; color: var(--text-dark);">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius);">
            </div>

            <div class="form-group" style="text-align: left; margin-top: 1.5rem;">
                <label style="font-weight: 600; color: var(--text-dark);">Correo Electrónico (No editable)</label>
                <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius); background-color: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">El correo electrónico no se puede cambiar por motivos de seguridad.</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 2rem; padding: 1rem;">Guardar Cambios</button>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('avatarPreview');
            var placeholder = document.getElementById('avatarPlaceholder');
            
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                // Si no había imagen previa, crear una
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'avatar-preview';
                img.id = 'avatarPreview';
                placeholder.parentNode.replaceChild(img, placeholder);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>
