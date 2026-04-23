<?php include 'view/templates/header.php'; ?>

<div class="l-app">
    <!-- 1. Primary Global Sidebar -->
    <?php include 'view/components/sidebar.php'; ?>

    <!-- 2. Secondary Context Sidebar (Channels) -->
    <aside class="l-app__secondary" style="flex: 0 0 260px; display: flex; flex-direction: column; border-right: 1px solid var(--c-border); background: #FAFAFA;">
        <div style="padding: var(--space-md); border-bottom: 1px solid var(--c-border); background: var(--c-bg-app); position: sticky; top: 0;">
            <h2 style="font-size: var(--fs-md); font-weight: 700; color: var(--c-text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($community['nombre']); ?></h2>
            <div style="font-size: var(--fs-xs); color: var(--c-text-muted);">Entorno interactivo</div>
        </div>
        
        <div style="flex: 1; overflow-y: auto; padding: var(--space-lg) var(--space-sm);">
            <a href="<?php echo BASE_URL; ?>?controller=Notice&action=index&community_id=<?php echo $community_id; ?>&<?php echo SID; ?>" class="btn-sidebar" style="margin-bottom: var(--space-lg); background: var(--c-secondary); color: var(--c-primary-hover);">
                📢 Tablón de Avisos
            </a>
            
            <div style="font-size: 0.70rem; text-transform: uppercase; color: var(--c-text-muted); font-weight: 700; padding: 0 var(--space-sm) var(--space-sm); letter-spacing: 0.5px;">Comunicaciones</div>
            <ul style="display: flex; flex-direction: column; gap: 4px;">
                 <?php foreach($channels as $c): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $community_id; ?>&channel_id=<?php echo $c['id_canal']; ?>&<?php echo SID; ?>" 
                           class="btn-sidebar <?php echo ($current_channel_id == $c['id_canal']) ? 'is-active' : ''; ?>">
                           
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
            <?php if($user_role == 'admin'): ?>
                <button onclick="openModal('createChannelModal')" class="btn" style="width: 100%; justify-content: space-between; background: transparent; border: 1px dashed var(--c-text-muted); color: var(--c-text-muted);">
                    <span>+ Crear Canal</span>
                </button>
            <?php endif; ?>
        </div>
    </aside>

    <!-- 3. Main Chat Interface -->
    <main class="l-app__main">
        <?php if($current_channel): ?>
            <!-- Sticky Glass Header -->
            <header class="l-app__header" style="justify-content: space-between; border-bottom: 1px solid var(--c-border); background: rgba(253, 253, 253, 0.85); backdrop-filter: blur(12px); position: absolute; width: 100%; top: 0; z-index: 10;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.5rem; color: var(--c-text-muted); font-weight: 300;">#</span>
                    <div>
                        <h2 style="font-size: var(--fs-md); font-weight: 600; color: var(--c-text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($current_channel['nombre']); ?></h2>
                        <div style="font-size: var(--fs-xs); color: var(--c-text-muted);"><?php echo htmlspecialchars($current_channel['descripcion']); ?></div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Message Feed -->
            <div class="l-app__content chat-feed" id="messages-container" style="padding-top: 80px; scroll-behavior: auto;">
                <div class="chat-container-inner">
                    <?php if(empty($messages)): ?>
                        <div class="empty-state animate-fade-up" style="margin-top: 10vh;">
                            <div class="empty-state__icon" style="background: transparent; font-size: 4rem;">👋</div>
                            <h2 style="font-size: var(--fs-xl); margin-bottom: var(--space-sm);">¡Hola, vecino!</h2>
                            <p class="text-muted">Este es el comienzo del canal #<?php echo htmlspecialchars($current_channel['nombre']); ?>. Escribe el primer mensaje.</p>
                        </div>
                    <?php else: ?>
                        <?php 
                            $unread_bar_shown = false; 
                            foreach($messages as $msg) {
                                // Logic for Unread Divider
                                $is_unread = false;
                                if ($last_read_at === null) {
                                    $is_unread = true;
                                } else {
                                    if (strtotime($msg['fecha_publicacion']) > strtotime($last_read_at)) {
                                        $is_unread = true;
                                    }
                                }
                                if ($msg['id_usuario'] == $user_id) $is_unread = false;

                                if ($is_unread && !$unread_bar_shown) {
                                    echo '<div class="chat-divider">Nuevos Mensajes</div>';
                                    $unread_bar_shown = true;
                                }

                                // Load the Component!
                                include 'view/components/chat_message.php';
                            }
                        ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Floating Input Box -->
            <div class="chat-input-area">
                <form action="<?php echo BASE_URL; ?>?controller=Channel&action=send&<?php echo SID; ?>" method="POST" style="width: 100%; max-width: 900px;">
                    <div class="chat-input-box">
                        <input type="hidden" name="community_id" value="<?php echo $community_id; ?>">
                        <input type="hidden" name="channel_id" value="<?php echo $current_channel_id; ?>">
                        
                        <button type="button" class="btn-icon" style="padding: 4px; color: var(--c-text-muted);" title="Adjuntar (Pronto)">📎</button>
                        
                        <input type="text" class="chat-input-box__field" name="contenido" placeholder="Enviar mensaje a #<?php echo htmlspecialchars($current_channel['nombre']); ?>..." autocomplete="off" required autofocus>
                        
                        <button type="submit" class="btn btn--primary" style="padding: 0.5rem 1.2rem; border-radius: var(--radius-pill); box-shadow: var(--shadow-soft);">
                            Enviar
                        </button>
                    </div>
                </form>
            </div>
            
            <script>
                // Instant Auto-Scroll to Bottom purely on load
                window.addEventListener('DOMContentLoaded', () => {
                    const msgContainer = document.getElementById("messages-container");
                    if (msgContainer) {
                        msgContainer.scrollTop = msgContainer.scrollHeight;
                    }
                });
            </script>
        <?php else: ?>
            <div class="empty-state animate-fade-up" style="margin: auto;">
                <div class="empty-state__icon" style="background: transparent; font-size: 3rem;">📭</div>
                <h2 style="color: var(--c-text-muted);">Selecciona un canal para comenzar</h2>
            </div>
        <?php endif; ?>
    </main>

    <!-- 4. Right Panel (Members) -->
    <aside class="l-app__context" style="display: flex; flex-direction: column; border-left: 1px solid var(--c-border); background: #FAFAFA;">
        <div style="padding: var(--space-md); border-bottom: 1px solid var(--c-border); position: sticky; top: 0; background: #FAFAFA;">
            <h2 style="font-size: var(--fs-md); font-weight: 700; color: var(--c-text-main);">Miembros</h2>
            <div style="font-size: var(--fs-xs); color: var(--c-text-muted);"><?php echo count($members); ?> vecinos</div>
        </div>
        <div style="flex: 1; overflow-y: auto; padding: var(--space-sm);">
            <?php foreach($members as $member): ?>
                <div style="display: flex; align-items: center; padding: var(--space-sm); border-radius: var(--radius); transition: background 0.2s; cursor: default;">
                    <?php if($member['avatar']): ?>
                        <img src="<?php echo BASE_URL . $member['avatar']; ?>" class="avatar avatar--md" style="margin-right: var(--space-sm);">
                    <?php else: ?>
                        <div class="avatar avatar--md" style="margin-right: var(--space-sm); display: flex; align-items: center; justify-content: center; background: var(--c-border); font-weight: 600;">
                            <?php echo strtoupper(substr($member['nombre'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="flex-grow: 1; overflow: hidden;">
                        <div style="font-weight: 500; color: var(--c-text-main); font-size: var(--fs-sm); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($member['nombre']); ?>
                        </div>
                        <div style="font-size: 0.70rem; color: var(--c-text-muted); display: flex; align-items: center; gap: 4px;">
                            <?php if($member['rol'] == 'admin'): ?>
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--c-primary);"></span>
                            <?php endif; ?>
                            <?php echo ucfirst($member['rol']); ?>
                        </div>
                    </div>
                    <?php if($member['id_usuario'] != $_SESSION['user_id']): ?>
                        <a href="<?php echo BASE_URL; ?>?controller=DirectMessage&action=chat&user_id=<?php echo $member['id_usuario']; ?>&<?php echo SID; ?>" class="btn-icon" style="padding: 4px; font-size: 0.9rem;" title="Mensaje">💬</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </aside>
</div>

<!-- ==============================================
     MODALS (Retained Structure, BEM Styles)
     ============================================== -->



<!-- Create Channel Modal -->
<div id="createChannelModal" class="modal-overlay" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px);">
    <div style="background: var(--c-bg-card); padding: var(--space-xl); border-radius: var(--radius-lg); width: 100%; max-width: 450px; box-shadow: var(--shadow-floating);">
        <h2 style="font-size: var(--fs-xl); margin-bottom: var(--space-sm);">Nuevo Canal</h2>
        <p class="text-muted" style="margin-bottom: var(--space-lg);">Crea un nuevo tema de conversación.</p>
        
        <form action="<?php echo BASE_URL; ?>?controller=Channel&action=create&<?php echo SID; ?>" method="POST">
            <input type="hidden" name="community_id" value="<?php echo $community_id; ?>">
            
            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Nombre</label>
                <input type="text" class="input" name="nombre" placeholder="ej. mascotas" required>
            </div>
            
            <div class="form-group" style="margin-bottom: var(--space-lg);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Descripción</label>
                <textarea class="input" name="descripcion" rows="2" placeholder="Opcional"></textarea>
            </div>

            <input type="hidden" name="tipo" value="publico">
            
            <div style="display: flex; gap: var(--space-sm);">
                <button type="submit" class="btn btn--primary" style="flex: 1;">Crear Canal</button>
                <button type="button" class="btn" style="border: 1px solid var(--c-border);" onclick="closeModal('createChannelModal')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    
    function filterMembers() {
        let input = document.getElementById('memberSearch').value.toLowerCase();
        let nodes = document.getElementsByClassName('member-item');
        for (let i = 0; i < nodes.length; i++) {
            nodes[i].style.display = nodes[i].getAttribute('data-name').includes(input) ? "flex" : "none";
        }
    }
</script>

<?php include 'view/templates/footer.php'; ?>
