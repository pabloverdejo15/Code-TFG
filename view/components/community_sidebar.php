<?php
// Requires: $community, $community_id, $channels, $user_role, $current_channel_id (optional), $active_section (optional)
$current_channel_id = isset($current_channel_id) ? $current_channel_id : null;
$active_section = isset($active_section) ? $active_section : 'channels';
?>
<aside class="l-app__secondary" style="flex: 0 0 260px; display: flex; flex-direction: column; border-right: 1px solid var(--c-border); background: var(--c-bg-secondary);">
    <div style="padding: var(--space-md); border-bottom: 1px solid var(--c-border); position: sticky; top: 0; z-index: 5;">
        <a href="<?php echo BASE_URL; ?>?controller=Community&action=index&<?php echo SID; ?>" style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; color: var(--c-text-muted); text-decoration: none; margin-bottom: var(--space-sm); transition: color 0.2s;" onmouseover="this.style.color='var(--c-text-main)'" onmouseout="this.style.color='var(--c-text-muted)'">
            <span>←</span> Volver a comunidades
        </a>
        <h2 style="font-size: var(--fs-md); font-weight: 700; color: var(--c-text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($community['nombre']); ?></h2>
        <div style="font-size: var(--fs-xs); color: var(--c-text-muted);">Entorno interactivo</div>
    </div>
    
    <div style="flex: 1; overflow-y: auto; padding: var(--space-lg) var(--space-sm);">
        <a href="<?php echo BASE_URL; ?>?controller=Notice&action=index&community_id=<?php echo $community_id; ?>&<?php echo SID; ?>" 
           class="btn-sidebar <?php echo ($active_section == 'notices') ? 'is-active' : ''; ?>" 
           style="margin-bottom: var(--space-xs); <?php echo ($active_section == 'notices') ? 'background: var(--c-secondary); color: var(--c-primary-hover);' : 'color: var(--c-text-main);'; ?>">
            📢 Tablón de Avisos
        </a>
        
        <a href="<?php echo BASE_URL; ?>?controller=Fee&action=index&community_id=<?php echo $community_id; ?>&<?php echo SID; ?>" 
           class="btn-sidebar <?php echo ($active_section == 'fees') ? 'is-active' : ''; ?>" 
           style="margin-bottom: var(--space-lg); <?php echo ($active_section == 'fees') ? 'background: var(--c-secondary); color: var(--c-primary-hover);' : 'color: var(--c-text-main);'; ?>">
            💳 Cuotas de Comunidad
        </a>
        
        <div style="font-size: 0.70rem; text-transform: uppercase; color: var(--c-text-muted); font-weight: 700; padding: 0 var(--space-sm) var(--space-sm); letter-spacing: 0.5px;">Comunicaciones</div>
        <ul style="display: flex; flex-direction: column; gap: 4px;">
             <?php foreach($channels as $c): ?>
                <li>
                    <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $community_id; ?>&channel_id=<?php echo $c['id_canal']; ?>&<?php echo SID; ?>" 
                       class="btn-sidebar <?php echo ($current_channel_id == $c['id_canal'] && $active_section == 'channels') ? 'is-active' : ''; ?>">
                       
                       <span style="opacity: <?php echo ($current_channel_id == $c['id_canal']) ? '1' : '0.6'; ?>; margin-right: 8px;">#</span> 
                       <span style="flex: 1; text-align: left; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($c['nombre']); ?></span>
                       
                       <?php if($c['unread_count'] > 0 && $c['id_canal'] != $current_channel_id): ?>
                           <span style="background-color: var(--c-error); width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;"></span>
                       <?php endif; ?>
                    </a>
                </li>
             <?php endforeach; ?>
             <?php if(empty($channels)): ?>
                <li style="padding: 0.5rem 1rem; font-size: var(--fs-xs); color: var(--c-text-muted);">No hay canales aún.</li>
             <?php endif; ?>
        </ul>
    </div>
    
    <div style="padding: var(--space-md); border-top: 1px solid var(--c-border); background: var(--c-bg-app);">
        <?php if(isset($user_role) && $user_role == 'admin'): ?>
            <button onclick="openModal('createChannelModal')" class="btn" style="width: 100%; justify-content: space-between; background: transparent; border: 1px dashed var(--c-text-muted); color: var(--c-text-muted);">
                <span>+ Crear Canal</span>
            </button>
        <?php endif; ?>
    </div>
</aside>
