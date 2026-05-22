<?php include 'view/templates/header.php'; ?>

<!-- Scoped Dark Theme -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/theme-dark.css?v=<?php echo time(); ?>">

<div class="l-app">
    <!-- Component Sidebar -->
    <?php include 'view/components/sidebar.php'; ?>
    
    <main class="l-app__main">
        <header class="l-app__header">
            <button class="hamburger-btn" id="mobileMenuBtn" aria-label="Abrir menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <h1 style="font-size: var(--fs-lg); font-weight: 600; color: var(--c-text-main);">Dashboard</h1>
        </header>

        <div class="l-app__content">
            <!-- Profile Section -->
            <section style="background: var(--c-bg-card); border: 1px solid var(--c-border); border-radius: var(--radius-lg); padding: var(--space-xl); margin-bottom: var(--space-2xl); display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow-soft);">
                <div style="display: flex; align-items: center; gap: var(--space-lg);">
                    <?php if($user['avatar']): ?>
                        <img src="<?php echo BASE_URL . $user['avatar']; ?>" class="avatar" style="width: 80px; height: 80px;" alt="Me">
                    <?php else: ?>
                        <div class="avatar" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: var(--c-primary); color: #000000; font-size: 2rem; font-weight: 700;">
                            <?php echo strtoupper(substr($user['nombre'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 style="font-size: var(--fs-xl); font-weight: 700; color: var(--c-text-main); margin-bottom: 4px;"><?php echo htmlspecialchars($user['nombre']); ?></h2>
                        <p style="color: var(--c-text-muted); font-size: var(--fs-sm); margin-bottom: 8px;"><?php echo htmlspecialchars($user['email']); ?></p>
                        <?php if($user['descripcion']): ?>
                            <p style="color: var(--c-text-main); font-size: var(--fs-sm); max-width: 400px;"><?php echo htmlspecialchars($user['descripcion']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <button onclick="openModal('editProfileModal')" class="btn" style="border: 1px solid var(--c-border); background: rgba(255, 255, 255, 0.05); color: var(--c-text-main);">Editar Perfil</button>
                </div>
            </section>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-lg);">
                <h2 style="font-size: var(--fs-lg); font-weight: 600; color: var(--c-text-main);">Comunidades</h2>
                <div style="display: flex; gap: var(--space-sm);">
                    <button onclick="openModal('createModal')" class="btn btn--primary">
                        + Nueva
                    </button>
                    <button onclick="openModal('joinModal')" class="btn" style="border: 1px solid var(--c-border); background: rgba(255, 255, 255, 0.05); color: var(--c-text-main);">
                        🔑 Unirse
                    </button>
                </div>
            </div>

            <?php if(empty($admin_communities) && empty($member_communities)): ?>
                <!-- BEM Empty State Component -->
                <div class="empty-state animate-fade-up">
                    <div class="empty-state__icon">🏘️</div>
                    <h2 style="margin-bottom: var(--space-sm); font-size: var(--fs-xl);">Sin comunidades</h2>    
                    <p class="text-muted" style="margin-bottom: var(--space-xl);">
                        Crea un nuevo espacio vecinal o únete con un código de invitación.
                    </p>
                    <button onclick="openModal('createModal')" class="btn btn--primary">Crear mi primera comunidad</button>
                </div>
            <?php else: ?>
                
                <?php if(!empty($admin_communities)): ?>
                    <h3 style="font-size: var(--fs-md); font-weight: 600; color: var(--c-text-muted); margin-bottom: var(--space-md);">Administrador</h3>
                    <!-- BEM Masonry Grid -->
                    <div class="l-grid-masonry" style="margin-bottom: var(--space-2xl);">
                        <?php foreach($admin_communities as $comm): ?>
                            <article class="notice-card animate-fade-up">
                                <header style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-md);">
                                    <h2 style="font-size: var(--fs-lg); font-weight: 600;"><?php echo htmlspecialchars($comm['nombre']); ?></h2>
                                    <span class="badge" style="background: var(--c-primary); color: #000000;"><?php echo ucfirst($comm['rol']); ?></span>
                                </header>
                                
                                <div class="text-muted" style="font-size: var(--fs-sm); display: flex; align-items: center; gap: 6px; margin-bottom: var(--space-sm);">
                                    📍 <?php echo htmlspecialchars($comm['direccion']); ?>
                                </div>

                                <?php if (!empty($comm['weather'])): $w = $comm['weather']; ?>
                                <div class="weather-widget" title="<?php echo htmlspecialchars($w['descripcion']); ?>">
                                    <div class="weather-widget__icon"><?php echo $w['emoji']; ?></div>
                                    <div class="weather-widget__info">
                                        <span class="weather-widget__temp"><?php echo $w['temp']; ?>°C</span>
                                        <span class="weather-widget__desc"><?php echo htmlspecialchars($w['descripcion']); ?></span>
                                    </div>
                                    <div class="weather-widget__extra">
                                        <span>💧 <?php echo $w['humedad']; ?>%</span>
                                        <span>💨 <?php echo $w['viento']; ?> km/h</span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <p style="font-size: var(--fs-base); color: var(--c-text-main); margin-bottom: var(--space-lg); line-height: var(--lh-relaxed);">
                                    <?php echo htmlspecialchars($comm['descripcion']); ?>
                                </p>
                                
                                <footer style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--c-border); padding-top: var(--space-md);">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?php echo BASE_URL; ?>?controller=Community&action=delete&id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn" style="padding: 6px 10px; color: var(--c-error); background: transparent;" title="Eliminar Comunidad" onclick="return confirm('¿Seguro que quieres eliminar esta comunidad permanentemente?');">
                                            🗑️ Eliminar
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>?controller=Community&action=leave&id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn" style="padding: 6px 10px; border: 1px solid var(--c-border); background: transparent;" title="Salir" onclick="return confirm('¿Seguro que quieres salir?');">
                                            Salir
                                        </a>
                                    </div>
                                    
                                    <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn btn--primary">
                                        Entrar
                                    </a>
                                </footer>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($member_communities)): ?>
                    <h3 style="font-size: var(--fs-md); font-weight: 600; color: var(--c-text-muted); margin-bottom: var(--space-md);">Miembro</h3>
                    <div class="l-grid-masonry">
                        <?php foreach($member_communities as $comm): ?>
                            <article class="notice-card animate-fade-up">
                                <header style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-md);">
                                    <h2 style="font-size: var(--fs-lg); font-weight: 600;"><?php echo htmlspecialchars($comm['nombre']); ?></h2>
                                    <span class="badge" style="background: var(--c-secondary); color: var(--c-text-main); border: 1px solid var(--c-border);"><?php echo ucfirst($comm['rol']); ?></span>
                                </header>
                                
                                <div class="text-muted" style="font-size: var(--fs-sm); display: flex; align-items: center; gap: 6px; margin-bottom: var(--space-sm);">
                                    📍 <?php echo htmlspecialchars($comm['direccion']); ?>
                                </div>

                                <?php if (!empty($comm['weather'])): $w = $comm['weather']; ?>
                                <div class="weather-widget" title="<?php echo htmlspecialchars($w['descripcion']); ?>">
                                    <div class="weather-widget__icon"><?php echo $w['emoji']; ?></div>
                                    <div class="weather-widget__info">
                                        <span class="weather-widget__temp"><?php echo $w['temp']; ?>°C</span>
                                        <span class="weather-widget__desc"><?php echo htmlspecialchars($w['descripcion']); ?></span>
                                    </div>
                                    <div class="weather-widget__extra">
                                        <span>💧 <?php echo $w['humedad']; ?>%</span>
                                        <span>💨 <?php echo $w['viento']; ?> km/h</span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <p style="font-size: var(--fs-base); color: var(--c-text-main); margin-bottom: var(--space-lg); line-height: var(--lh-relaxed);">
                                    <?php echo htmlspecialchars($comm['descripcion']); ?>
                                </p>
                                
                                <footer style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--c-border); padding-top: var(--space-md);">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?php echo BASE_URL; ?>?controller=Community&action=leave&id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn" style="padding: 6px 10px; border: 1px solid var(--c-border); background: transparent;" title="Salir" onclick="return confirm('¿Seguro que quieres salir?');">
                                            Salir
                                        </a>
                                    </div>
                                    
                                    <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn btn--primary">
                                        Entrar
                                    </a>
                                </footer>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </main>
</div>


<!-- Simple Modals (Can be isolated later) -->
<div id="createModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(17, 24, 39, 0.7); align-items: center; justify-content: center; z-index: 100;">
    <div style="background: var(--c-bg-app); padding: var(--space-xl); border-radius: var(--radius-lg); width: 100%; max-width: 450px; box-shadow: var(--shadow-floating);">
        <h2 style="font-size: var(--fs-xl); margin-bottom: var(--space-sm);">Nueva Comunidad</h2>
        <p class="text-muted" style="margin-bottom: var(--space-lg);">Configura un nuevo espacio para tus vecinos.</p>
        
        <form action="<?php echo BASE_URL; ?>?controller=Community&action=create&<?php echo SID; ?>" method="POST">
            <div class="form-group">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Nombre de la Comunidad *</label>
                <input type="text" class="input" name="nombre" required placeholder="Ej. Edificio Las Flores 23">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Dirección</label>
                <input type="text" class="input" name="direccion" placeholder="Calle Ejemplo, 123">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Descripción</label>
                <textarea class="input" name="descripcion" rows="3" placeholder="Un espacio para coordinarnos..."></textarea>
            </div>
            <div style="margin-top: var(--space-xl); display: flex; gap: var(--space-sm);">
                <button type="submit" class="btn btn--primary" style="flex: 1;">Crear Comunidad</button>
                <button type="button" class="btn" style="border: 1px solid var(--c-border);" onclick="closeModal('createModal')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="joinModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(17, 24, 39, 0.7); align-items: center; justify-content: center; z-index: 100;">
    <div style="background: var(--c-bg-app); padding: var(--space-xl); border-radius: var(--radius-lg); width: 100%; max-width: 400px; text-align: center; box-shadow: var(--shadow-floating);">
        <div style="font-size: 3rem; margin-bottom: var(--space-sm);">🔑</div>
        <h2 style="font-size: var(--fs-xl); margin-bottom: var(--space-sm);">Unirse a Comunidad</h2>
        <p class="text-muted" style="margin-bottom: var(--space-lg);">Introduce el código de invitación de tu grupo.</p>

        <form action="<?php echo BASE_URL; ?>?controller=Community&action=join&<?php echo SID; ?>" method="POST">
            <div class="form-group">
                <input type="text" class="input" name="codigo_acceso" required placeholder="A1B2C3" maxlength="10" style="text-align: center; letter-spacing: 0.3rem; font-size: var(--fs-xl); text-transform: uppercase; font-weight: 700;">
            </div>
            <div style="display: flex; gap: var(--space-sm); margin-top: var(--space-lg);">
                <button type="button" class="btn" style="flex:1; border: 1px solid var(--c-border);" onclick="closeModal('joinModal')">Cancelar</button>
                <button type="submit" class="btn btn--primary" style="flex:1;">Unirse</button>
            </div>
        </form>
    </div>
</div>

<div id="editProfileModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(17, 24, 39, 0.7); align-items: center; justify-content: center; z-index: 100;">
    <div style="background: var(--c-bg-app); padding: var(--space-xl); border-radius: var(--radius-lg); width: 100%; max-width: 450px; box-shadow: var(--shadow-floating);">
        <h2 style="font-size: var(--fs-xl); margin-bottom: var(--space-sm);">Editar Perfil</h2>
        
        <form action="<?php echo BASE_URL; ?>?controller=User&action=update&<?php echo SID; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="redirect_to" value="<?php echo urlencode(BASE_URL . "?controller=Community&action=index"); ?>">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <label for="avatar_upload" style="cursor: pointer; display: inline-block; position: relative;">
                    <?php if($user['avatar']): ?>
                        <img src="<?php echo BASE_URL . $user['avatar']; ?>" class="avatar" style="width: 100px; height: 100px; border: 2px solid var(--c-primary);" id="avatarPreview">
                    <?php else: ?>
                        <div class="avatar" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; background: var(--c-primary); color: #000000; font-size: 2.5rem; font-weight: 700;" id="avatarPreviewDiv">
                            <?php echo strtoupper(substr($user['nombre'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div style="position: absolute; bottom: 0; right: 0; background: var(--c-bg-card); border: 1px solid var(--c-border); border-radius: 50%; padding: 6px; box-shadow: var(--shadow-sm);">📷</div>
                </label>
                <input type="file" id="avatar_upload" name="avatar" style="display: none;" accept="image/*" onchange="previewAvatar(this)">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Nombre</label>
                <input type="text" class="input" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Descripción</label>
                <textarea class="input" name="descripcion" rows="3"><?php echo htmlspecialchars($user['descripcion']); ?></textarea>
            </div>
            <div style="margin-top: var(--space-xl); display: flex; gap: var(--space-sm);">
                <button type="submit" class="btn btn--primary" style="flex: 1;">Guardar</button>
                <button type="button" class="btn" style="border: 1px solid var(--c-border);" onclick="closeModal('editProfileModal')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var previewImg = document.getElementById('avatarPreview');
                if (previewImg) {
                    previewImg.src = e.target.result;
                } else {
                    var div = document.getElementById('avatarPreviewDiv');
                    div.outerHTML = '<img src="'+e.target.result+'" class="avatar" style="width: 100px; height: 100px; border: 2px solid var(--c-primary);" id="avatarPreview">';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'view/templates/footer.php'; ?>
