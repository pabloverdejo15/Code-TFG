<?php include 'view/templates/header.php'; ?>

<!-- Scoped Dark Theme -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/theme-dark.css?v=<?php echo time(); ?>">

<div class="l-app">
    <!-- 1. Primary Global Sidebar -->
    <?php include 'view/components/sidebar.php'; ?>

    <!-- 2. Secondary Context Sidebar -->
    <?php 
    $active_section = 'channels';
    include 'view/components/community_sidebar.php'; 
    ?>

    <!-- 3. Main Chat Interface -->
    <main class="l-app__main">
        <?php if($current_channel): ?>
            <!-- Sticky Glass Header -->
            <header class="l-app__header" style="justify-content: space-between; border-bottom: 1px solid var(--c-border); position: absolute; width: 100%; top: 0; z-index: 10;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button class="hamburger-btn" id="mobileMenuBtn" aria-label="Abrir menú" style="margin-right: 8px;">
                        <span></span><span></span><span></span>
                    </button>
                    <span style="font-size: 1.5rem; color: var(--c-text-muted); font-weight: 300;">#</span>
                    <div>
                        <h2 style="font-size: var(--fs-md); font-weight: 600; color: var(--c-text-main); margin-bottom: 2px;"><?php echo htmlspecialchars($current_channel['nombre']); ?></h2>
                        <div style="font-size: var(--fs-xs); color: var(--c-text-muted);"><?php echo htmlspecialchars($current_channel['descripcion']); ?></div>
                    </div>
                </div>
                <button class="mobile-context-btn" id="mobileMembersBtn" title="Ver vecinos">👥</button>
            </header>

            <!-- Scrollable Message Feed -->
            <div class="l-app__content chat-feed" id="messages-container" style="padding-top: 80px; scroll-behavior: auto;">
                <div class="chat-container-inner" data-oldest-id="<?php echo empty($messages) ? 0 : $messages[0]['id_mensaje']; ?>">
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
                <form action="<?php echo BASE_URL; ?>?controller=Channel&action=send&<?php echo SID; ?>" method="POST" style="width: 100%;">
                    <div class="chat-input-box">
                        <input type="hidden" name="community_id" value="<?php echo $community_id; ?>">
                        <input type="hidden" name="channel_id" value="<?php echo $current_channel_id; ?>">
                        <input type="file" id="fileUpload" style="display: none;" onchange="handleFileUpload(this)">
                        
                        <button type="button" class="btn-icon" style="padding: 6px; color: var(--c-text-muted); font-size: 1.1rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" title="Adjuntar Archivo" onclick="document.getElementById('fileUpload').click();" onmouseover="this.style.color='var(--c-text-main)'" onmouseout="this.style.color='var(--c-text-muted)'">📎</button>
                        
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

                function handleFileUpload(input) {
                    if (input.files && input.files.length > 0) {
                        console.log("Archivo seleccionado:", input.files[0].name);
                    }
                }

                // Infinite Scroll Pagination
                const msgContainer = document.getElementById("messages-container");
                const innerContainer = document.querySelector(".chat-container-inner");
                let isLoading = false;
                let hasMore = true;

                if (msgContainer && innerContainer) {
                    msgContainer.addEventListener("scroll", async () => {
                        if (msgContainer.scrollTop <= 10 && !isLoading && hasMore) {
                            isLoading = true;
                            const oldestId = innerContainer.getAttribute("data-oldest-id");
                            
                            // Save exact height before any changes
                            const oldHeight = msgContainer.scrollHeight;

                            // Add a subtle loader at the top
                            const loader = document.createElement("div");
                            loader.id = "chat-top-loader";
                            loader.style = "text-align: center; padding: 10px; color: var(--c-text-muted); font-size: 0.8rem;";
                            loader.innerHTML = "<span>Cargando mensajes...</span>";
                            innerContainer.prepend(loader);

                            try {
                                const response = await fetch(`<?php echo BASE_URL; ?>?controller=Channel&action=ajax_fetch_messages&channel_id=<?php echo $current_channel_id; ?>&before_id=${oldestId}&<?php echo SID; ?>`);
                                const data = await response.json();
                                
                                loader.remove();

                                if (data.html) {
                                    innerContainer.insertAdjacentHTML('afterbegin', data.html);
                                    
                                    if (data.new_oldest_id) {
                                        innerContainer.setAttribute("data-oldest-id", data.new_oldest_id);
                                    }
                                    
                                    // CRITICAL: Restore scroll position accurately
                                    const newHeight = msgContainer.scrollHeight;
                                    msgContainer.scrollTop = newHeight - oldHeight + msgContainer.scrollTop;
                                }
                                
                                if (data.has_more === false) {
                                    hasMore = false;
                                }

                            } catch (err) {
                                console.error("Error loading messages:", err);
                                loader.remove();
                            } finally {
                                isLoading = false;
                            }
                        }
                    });
                }
            </script>
        <?php else: ?>
            <div class="empty-state animate-fade-up" style="margin: auto;">
                <div class="empty-state__icon" style="background: transparent; font-size: 3rem;">📭</div>
                <h2 style="color: var(--c-text-muted);">Selecciona un canal para comenzar</h2>
            </div>
        <?php endif; ?>
    </main>

    <!-- 4. Right Panel (Members) -->
    <aside class="l-app__context" style="display: flex; flex-direction: column; border-left: 1px solid var(--c-border);">
        <div style="padding: var(--space-md); border-bottom: 1px solid var(--c-border); position: sticky; top: 0; z-index: 5;">
            <h2 style="font-size: var(--fs-md); font-weight: 700; color: var(--c-text-main); margin-bottom: 4px;">Miembros</h2>
            <div style="font-size: var(--fs-xs); color: var(--c-text-muted); margin-bottom: var(--space-md);"><?php echo count($members); ?> vecinos</div>
            <div class="chat-input-box" style="padding: 6px 12px; background: rgba(0, 0, 0, 0.3); border: 1px solid var(--c-border); border-radius: var(--radius-full); display: flex; align-items: center; gap: 8px;">
                <span style="color: var(--c-text-muted); font-size: 0.8rem;">🔍</span>
                <input type="text" id="memberSearch" placeholder="Buscar..." onkeyup="filterMembers()" style="background: transparent; border: none; outline: none; color: var(--c-text-main); font-size: var(--fs-xs); width: 100%;" autocomplete="off">
            </div>
        </div>
        <div style="flex: 1; overflow-y: auto; padding: var(--space-sm);">
            <?php foreach($members as $member): ?>
                <div class="member-item" data-name="<?php echo strtolower(htmlspecialchars($member['nombre'])); ?>" style="display: flex; align-items: center; padding: var(--space-sm); border-radius: var(--radius); transition: background 0.2s; cursor: default;">
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
