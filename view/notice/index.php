<?php include 'view/templates/header.php'; ?>

<div class="l-app">
    <?php include 'view/components/sidebar.php'; ?>

    <main class="l-app__main">
        <header class="l-app__header">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div style="display: flex; align-items: center;">
                    <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $community['id_comunidad']; ?>&<?php echo SID; ?>" class="btn-icon" style="margin-right: var(--space-md);">
                        &larr;
                    </a>
                    <div>
                        <h1 style="font-size: var(--fs-lg); font-weight: 600; color: var(--c-text-main);">Tablón de Avisos</h1>
                        <div style="font-size: var(--fs-xs); color: var(--c-text-muted);"><?php echo htmlspecialchars($community['nombre']); ?></div>
                    </div>
                </div>
                <?php if($is_admin): ?>
                    <button onclick="openModal('createNoticeModal')" class="btn btn--primary">
                        + Nuevo Aviso
                    </button>
                <?php endif; ?>
            </div>
        </header>

        <div class="l-app__content" style="display: flex; gap: var(--space-2xl);">
            
            <div style="flex: 1;">
                <?php if(empty($notices)): ?>
                    <div class="empty-state animate-fade-up">
                        <div class="empty-state__icon">📋</div>
                        <p class="text-muted">No hay avisos publicados todavía.</p>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                        <?php foreach($notices as $notice): ?>
                            <article class="notice-card animate-fade-up" style="position: relative; overflow: hidden; padding-left: 24px;">
                                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 6px; background-color: <?php 
                                    if($notice['tipo'] == 'averia') echo 'var(--c-error)';
                                    elseif($notice['tipo'] == 'reunion') echo 'var(--c-secondary)';
                                    else echo 'var(--c-primary)';
                                ?>;"></div>
                                
                                <header style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-sm);">
                                    <span class="badge" style="background: var(--c-bg-body); border: 1px solid var(--c-border); color: var(--c-text-main);">
                                        <?php echo htmlspecialchars($notice['tipo']); ?>
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: var(--fs-xs); color: var(--c-text-muted);">
                                            <?php echo date('d M', strtotime($notice['fecha_publicacion'])); ?>
                                        </span>
                                        <?php if($is_admin): ?>
                                            <a href="<?php echo BASE_URL; ?>?controller=Notice&action=delete&id=<?php echo $notice['id_aviso']; ?>&community_id=<?php echo $community['id_comunidad']; ?>&<?php echo SID; ?>" 
                                               onclick="return confirm('¿Eliminar aviso?');" class="btn-icon" style="padding: 4px; color: var(--c-error);" title="Eliminar">🗑️</a>
                                        <?php endif; ?>
                                    </div>
                                </header>
                                
                                <h3 style="font-size: var(--fs-lg); font-weight: 600; color: var(--c-text-main); margin-bottom: var(--space-sm);">
                                    <?php echo htmlspecialchars($notice['titulo']); ?>
                                </h3>
                                <p style="font-size: var(--fs-sm); color: var(--c-text-muted); line-height: var(--lh-relaxed);">
                                    <?php echo nl2br(htmlspecialchars($notice['descripcion'])); ?>
                                </p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>



        </div>
    </main>
</div>

<!-- Create Notice Modal -->
<div id="createNoticeModal" class="modal-overlay" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px);">
    <div style="background: var(--c-bg-card); padding: var(--space-xl); border-radius: var(--radius-lg); width: 100%; max-width: 450px; box-shadow: var(--shadow-floating);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-lg);">
            <h2 style="font-size: var(--fs-xl); font-weight: 700;">📢 Publicar aviso</h2>
            <button class="btn-icon" onclick="closeModal('createNoticeModal')" style="padding: 4px;">✕</button>
        </div>
        
        <form action="<?php echo BASE_URL; ?>?controller=Notice&action=create&<?php echo SID; ?>" method="POST">
            <input type="hidden" name="community_id" value="<?php echo $community['id_comunidad']; ?>">
            
            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Título</label>
                <input type="text" class="input" name="titulo" placeholder="Ej. Corte de agua" required>
            </div>
            
            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Tipo</label>
                <select name="tipo" class="input">
                    <option value="anuncio">ℹ️ Información</option>
                    <option value="averia">⚠️ Avería / Incidencia</option>
                    <option value="reunion">📅 Reunión</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-xl);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Detalles</label>
                <textarea name="descripcion" class="input" rows="4" placeholder="Describe el aviso..." required></textarea>
            </div>
            
            <div style="display: flex; gap: var(--space-sm);">
                <button type="submit" class="btn btn--primary" style="flex: 1;">Publicar</button>
                <button type="button" class="btn" style="border: 1px solid var(--c-border);" onclick="closeModal('createNoticeModal')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>

<?php include 'view/templates/footer.php'; ?>
