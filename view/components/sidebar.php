<aside class="l-app__sidebar">
    <!-- Brand Area -->
    <div style="padding: var(--space-lg); text-align: center; border-bottom: 1px solid var(--c-border); margin-bottom: var(--space-md);">
        <span style="font-weight: 700; color: var(--c-primary); font-size: var(--fs-xl); letter-spacing: -1px;">HN</span>
    </div>
    
    <!-- Navigation Links -->
    <nav style="flex: 1; display: flex; flex-direction: column; padding: 0 var(--space-sm); gap: var(--space-xs);">
        
        <?php 
            $currentAction = isset($_GET['action']) ? $_GET['action'] : 'index';
            $currentController = isset($_GET['controller']) ? $_GET['controller'] : 'Community';
        ?>
        
        <a href="<?php echo BASE_URL; ?>?controller=Community&action=index&<?php echo SID; ?>" class="btn-sidebar <?php echo ($currentController == 'Community') ? 'is-active' : ''; ?>">
            <span style="opacity: 0.8; margin-right: 12px; font-size: 1.2rem;">🏠</span> Inicio
        </a>
        
        <!-- Future Chat & Notices integration goes here -->
    </nav>
    
    <!-- User / Bottom Actions -->
    <div style="padding: var(--space-md) var(--space-sm); border-top: 1px solid var(--c-border);">
        <div style="display: flex; align-items: center; gap: 12px; padding: 8px; margin-bottom: 12px;">
            <?php if(isset($_SESSION['user_avatar']) && !empty($_SESSION['user_avatar'])): ?>
                <img src="<?php echo BASE_URL . $_SESSION['user_avatar']; ?>" class="avatar avatar--sm" alt="Me">
            <?php else: ?>
                <div class="avatar avatar--sm" style="display: flex; align-items: center; justify-content: center; background: var(--c-primary); color: white; font-size: 12px;">
                    HN
                </div>
            <?php endif; ?>
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: var(--fs-xs); font-weight: 600; color: var(--c-text-main);">
                    <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Vecino'; ?>
                </span>
                <span style="font-size: 0.65rem; color: var(--c-accent); font-weight: 500;">En línea</span>
            </div>
        </div>
        
        <a href="<?php echo BASE_URL; ?>?controller=Auth&action=logout&<?php echo SID; ?>" class="btn" style="width: 100%; justify-content: flex-start; color: var(--c-error); background: rgba(239, 68, 68, 0.1);">
            <span style="margin-right: 12px; font-size: 1.2rem;">🚪</span> Salir
        </a>
    </div>
</aside>
