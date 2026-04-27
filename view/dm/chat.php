<?php include 'view/templates/header.php'; ?>

<!-- Scoped Dark Theme -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/theme-dark.css?v=<?php echo time(); ?>">

<div class="l-app">
    <?php include 'view/components/sidebar.php'; ?>

    <main class="l-app__main">
        <header class="l-app__header" style="justify-content: flex-start; border-bottom: 1px solid var(--c-border); position: absolute; width: 100%; top: 0; z-index: 10;">
            <a href="javascript:history.back()" class="btn-icon" style="margin-right: var(--space-md);">&larr;</a>
            <div style="display: flex; align-items: center; gap: 12px;">
                <?php if($target_user['avatar']): ?>
                    <img src="<?php echo BASE_URL . $target_user['avatar']; ?>" class="avatar avatar--md">
                <?php else: ?>
                    <div class="avatar avatar--md" style="display:flex; align-items:center; justify-content:center; background: var(--c-border); font-weight: 600;">
                        <?php echo strtoupper(substr($target_user['nombre'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 style="font-size: var(--fs-md); font-weight: 600; color: var(--c-text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($target_user['nombre']); ?></h2>
                    <div style="font-size: var(--fs-xs); color: var(--c-text-muted);">Mensaje Directo</div>
                </div>
            </div>
        </header>

        <div class="l-app__content chat-feed" id="messages-container" style="padding-top: 80px; scroll-behavior: auto;">
            <div class="chat-container-inner">
                <?php if(empty($messages)): ?>
                    <div class="empty-state animate-fade-up" style="margin-top: 10vh;">
                        <div class="empty-state__icon" style="background: transparent; font-size: 4rem;">👋</div>
                        <h2 style="font-size: var(--fs-xl); margin-bottom: var(--space-sm);">¡Hola!</h2>
                        <p class="text-muted">Comienza la conversación con <?php echo htmlspecialchars($target_user['nombre']); ?>.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($messages as $msg): 
                        $isSelf = ($msg['id_emisor'] == $my_user_id);
                        $alignmentClass = $isSelf ? 'chat-group--self' : 'chat-group--other';
                        $bubbleClass = $isSelf ? 'chat-bubble--self' : 'chat-bubble--other';
                    ?>
                        <div class="chat-group <?php echo $alignmentClass; ?> chat-group--wide animate-fade-up">
                            <div class="chat-group__row">
                                <div class="chat-group__content">
                                    <div class="chat-bubble-wrapper">
                                        <div class="chat-bubble <?php echo $bubbleClass; ?>">
                                            <p class="chat-bubble__text"><?php echo nl2br(htmlspecialchars($msg['contenido'])); ?></p>
                                            <span class="chat-bubble__meta"><?php echo date('H:i', strtotime($msg['fecha_envio'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-input-area">
            <form action="<?php echo BASE_URL; ?>?controller=DirectMessage&action=send&<?php echo SID; ?>" method="POST" style="width: 100%; max-width: 900px;">
                <div class="chat-input-box">
                    <input type="hidden" name="receptor_id" value="<?php echo $target_user_id; ?>">
                    <input type="text" class="chat-input-box__field" name="contenido" placeholder="Enviar mensaje a <?php echo htmlspecialchars($target_user['nombre']); ?>..." autocomplete="off" required autofocus>
                    <button type="submit" class="btn btn--primary" style="padding: 0.5rem 1.2rem; border-radius: var(--radius-pill); box-shadow: var(--shadow-soft);">Enviar</button>
                </div>
            </form>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const msgContainer = document.getElementById("messages-container");
                if (msgContainer) {
                    msgContainer.scrollTop = msgContainer.scrollHeight;
                }
            });
        </script>
    </main>
</div>

<?php include 'view/templates/footer.php'; ?>
