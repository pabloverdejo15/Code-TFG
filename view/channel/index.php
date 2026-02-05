<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($community['nombre']); ?> - Hello Neighbor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=<?php echo time(); ?>">
    <style>
        .channel-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; margin-right: 0.5rem; background: #ddd; display: inline-flex; justify-content: center; align-items: center; font-size: 0.8rem; color: #666; }
        .tick-icon { font-size: 0.8rem; margin-left: 0.25rem; vertical-align: middle; }
        .tick-gray { color: #9ca3af; } /* Un check (enviado/no leido por todos) */
        .tick-blue { color: #3b82f6; } /* Doble check azul (leído por todos) */
        
        .user-bar img.nav-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid white; }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: flex-start;
            padding-top: 10vh;
            opacity: 0;
            transition: opacity 0.3s ease;
            overflow-y: auto;
        }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card {
            background: white;
            width: 90%;
            max-width: 500px;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            transform: scale(0.95);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
        }
        .modal-overlay.active .modal-card { transform: scale(1); }
        .close-modal {
            position: absolute;
            top: 1rem; right: 1rem;
            background: transparent; border: none;
            font-size: 1.5rem; cursor: pointer; color: #6b7280;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 0.75rem;
            border: 1px solid #d1d5db; border-radius: 0.5rem;
            font-size: 1rem;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        /* FORCE FULL SCREEN LAYOUT RESET */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            display: block !important; /* Override flex centering from global css */
            width: 100%;
            background-color: #1f2937; /* Dark background matches sidebar */
        }

        .app-layout {
            width: 100vw;
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* FIX: Force Dark Text on Light Main Content */
        .main-content {
            color: #111827 !important; /* Dark text for chat area */
        }

        .chat-header {
            color: #111827 !important;
        }

        .message-bubble {
            color: #111827 !important; /* Dark text for received messages */
        }

        /* Keep white text for own messages (Blue Bubble) */
        .message-card.own .message-bubble {
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<div class="app-layout">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div>
                <?php echo htmlspecialchars($community['nombre']); ?>
                <?php if($user_role == 'admin'): ?>
                    <div style="font-size: 0.75rem; font-weight: normal; margin-top: 4px; opacity: 0.8;">
                        Code: <span title="Share this code"><?php echo htmlspecialchars($community['codigo_acceso']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <?php if($user_role == 'admin'): ?>
                <button onclick="openModal('createChannelModal')" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 1.2rem; line-height: 1;">+</button>
            <?php endif; ?>
        </div>

        <!-- Member List Trigger -->
        <div class="member-trigger" style="padding: 1rem; border-bottom: 1px solid #374151; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="openModal('memberListModal')">
            <div style="font-size: 0.9rem; font-weight: 600; color: #e5e7eb;">👥 Miembros</div>
            <div style="color: #9ca3af; font-size: 0.8rem;">Ver todos</div>
        </div>
        
        <div class="channel-list-container">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; margin-bottom: 0.75rem; font-weight: 600; padding: 1rem 1rem 0 1rem;">Canales</div>
            <ul class="channel-list" style="margin: 0; padding: 0.5rem 1rem;">
                <?php foreach($channels as $c): ?>
                    <li class="channel-item">
                        <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $community_id; ?>&channel_id=<?php echo $c['id_canal']; ?>&<?php echo SID; ?>" 
                           class="channel-link <?php echo ($current_channel_id == $c['id_canal']) ? 'active' : ''; ?>"
                           style="display: flex; justify-content: space-between; align-items: center;">
                           <span>
                               <span class="channel-icon">#</span> 
                               <?php echo htmlspecialchars($c['nombre']); ?>
                           </span>
                           <?php if($c['unread_count'] > 0 && $c['id_canal'] != $current_channel_id): ?>
                               <span style="background-color: #ef4444; width: 8px; height: 8px; border-radius: 50%; display: inline-block;" title="<?php echo $c['unread_count']; ?> mensajes nuevos"></span>
                           <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if(empty($channels)): ?>
                    <li style="padding: 0.5rem; font-size: 0.9rem; color: #9ca3af;">No hay canales aún.</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div style="padding: 0 1rem; margin-bottom: 1rem; margin-top: auto;">
             <a href="<?php echo BASE_URL; ?>?controller=Notice&action=index&community_id=<?php echo $community_id; ?>&<?php echo SID; ?>" class="channel-link" style="background-color: #4f46e5; color: white; text-align: center;">📢 Tablón de Avisos</a>
        </div>

        <div class="user-bar" style="border-top: 1px solid #374151; padding: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <?php if(!empty($_SESSION['user_avatar'])): ?>
                <a href="#" onclick="toggleProfilePopover(event)" style="text-decoration:none;"><img src="<?php echo BASE_URL . $_SESSION['user_avatar']; ?>" class="nav-avatar" alt="Avatar"></a>
            <?php else: ?>
                <a href="#" onclick="toggleProfilePopover(event)" style="text-decoration:none;"><div class="nav-avatar" style="background:#6b7280; display:flex;align-items:center;justify-content:center;color:white;font-size:0.8rem;">Yo</div></a>
            <?php endif; ?>
            
            <div style="flex-grow:1; overflow:hidden;">
                <div style="font-weight:600; font-size:0.9rem; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">
                    <?php echo $_SESSION['user_name']; ?>
                </div>
                <div style="font-size:0.75rem;">
                    <a href="#" onclick="toggleProfilePopover(event)" style="color:#d1d5db;">Editar Perfil</a>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>?controller=Auth&action=logout&<?php echo SID; ?>" style="color:#ef4444;text-decoration:none;">↩</a>
        </div>
        
        <div style="padding: 0 1rem 1rem;">
             <a href="<?php echo BASE_URL; ?>?controller=Community&action=index&<?php echo SID; ?>" class="back-btn">&larr; Volver</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <?php if($current_channel): ?>
            <header class="chat-header">
                <div style="font-weight: bold; font-size: 1.1rem;">
                    <span style="color: #6b7280; margin-right: 0.25rem;">#</span>
                    <?php echo htmlspecialchars($current_channel['nombre']); ?>
                </div>
                <div style="font-size: 0.9rem; color: #6b7280;"><?php echo htmlspecialchars($current_channel['descripcion']); ?></div>
            </header>

            <div class="messages-area" id="messages-container">
                <?php if(empty($messages)): ?>
                    <p style="text-align:center; color:#9ca3af;">No hay mensajes en este canal. ¡Sé el primero en escribir!</p>
                <?php else: ?>
                    <?php 
                        $unread_bar_shown = false; 
                    ?>
                    <?php foreach($messages as $msg): ?>
                        
                        <?php 
                            // Mostrar barra de no leídos si el mensaje es más nuevo que last_read_at
                            // Y si NO soy yo el que lo escribió (opcional, pero Whatsapp no te muestra "no leidio" tus propios mensajes nuevos en otro dispositivo usualmente, 
                            // pero aquí asumimos simple fecha).
                            // Ojo: last_read_at puede ser null (nunca entrado). Si es null, mostrar barra arriba del todo (primer mensaje ajeno).
                            
                            $is_unread = false;
                            if ($last_read_at === null) {
                                // Nunca entrado: Todos son no leídos.
                                $is_unread = true;
                            } else {
                                if (strtotime($msg['fecha_publicacion']) > strtotime($last_read_at)) {
                                    $is_unread = true;
                                }
                            }

                            // No marcar mis propios mensajes como "no leídos" para mi (no tiene sentido)
                            if ($msg['id_usuario'] == $user_id) {
                                $is_unread = false;
                            }

                            if ($is_unread && !$unread_bar_shown): 
                        ?>
                            <div style="text-align: center; margin: 1rem 0; position: relative;">
                                <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.25rem 1rem; border-radius: 1rem; font-size: 0.75rem; font-weight: bold;">
                                    Mensajes No Leídos
                                </span>
                                <hr style="border: 0; border-top: 1px solid rgba(239, 68, 68, 0.2); position: absolute; top: 50%; left: 0; right: 0; z-index: -1;">
                            </div>
                        <?php 
                                $unread_bar_shown = true; 
                            endif; 
                        ?>

                        <div class="message-card <?php echo ($msg['id_usuario'] == $user_id) ? 'own' : ''; ?>">
                            <?php if($msg['id_usuario'] != $user_id): ?>
                                <?php if(!empty($msg['avatar'])): ?>
                                    <img src="<?php echo BASE_URL . $msg['avatar']; ?>" class="channel-avatar" title="<?php echo htmlspecialchars($msg['nombre_usuario']); ?>" onclick="showUserProfile(<?php echo $msg['id_usuario']; ?>)" style="cursor: pointer;">
                                <?php else: ?>
                                    <div class="channel-avatar" title="<?php echo htmlspecialchars($msg['nombre_usuario']); ?>" onclick="showUserProfile(<?php echo $msg['id_usuario']; ?>)" style="cursor: pointer; display: flex; align-items: center; justify-content: center;">👤</div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="message-bubble">
                                <?php if($msg['id_usuario'] != $user_id): ?>
                                    <div style="font-size: 0.75rem; font-weight: bold; margin-bottom: 0.25rem; opacity: 0.8;"><?php echo htmlspecialchars($msg['nombre_usuario']); ?></div>
                                <?php endif; ?>
                                
                                <div style="line-height: 1.5; margin-bottom: 0.25rem;"><?php echo nl2br(htmlspecialchars($msg['contenido'])); ?></div>
                                
                                <div style="text-align: right; font-size: 0.7rem; opacity: 0.7; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                    <?php echo date('H:i', strtotime($msg['fecha_publicacion'])); ?>
                                    
                                    <?php if($msg['id_usuario'] == $user_id): ?>
                                        <?php 
                                            // Lógica de ticks:
                                            // num_leidos >= total_miembros -> Todos leyeron (Doble tick azul)
                                            // num_leidos > 1 (yo y alguien mas) -> Alguien leyó (Doble tick gris - versión simple)
                                            // Por defecto: un tick gris (enviado)
                                            
                                            // Simplificación:
                                            // 1 tick gris: Enviado
                                            // 2 ticks azules: Leído por todos
                                            $all_read = ($msg['num_leidos'] >= $msg['total_miembros']);
                                        ?>
                                        <?php if($all_read): ?>
                                            <span class="tick-icon tick-blue">✓✓</span>
                                        <?php else: ?>
                                            <span class="tick-icon tick-gray">✓</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="input-area" style="padding: 1.5rem;">
                <form action="<?php echo BASE_URL; ?>?controller=Channel&action=send&<?php echo SID; ?>" method="POST" style="display: flex; gap: 1rem;">
                    <input type="hidden" name="community_id" value="<?php echo $community_id; ?>">
                    <input type="hidden" name="channel_id" value="<?php echo $current_channel_id; ?>">
                    <input type="text" name="contenido" style="flex-grow:1; padding:0.75rem; border-radius:1.5rem; border:1px solid #ccc;" placeholder="Escribe un mensaje..." autocomplete="off" required>
                    <button type="submit" class="btn btn-primary" style="border-radius:50%; width:48px; height:48px; padding:0; font-size:1.2rem;">➤</button>
                </form>
            </div>
            
            <script>
                var msgContainer = document.getElementById("messages-container");
                msgContainer.scrollTop = msgContainer.scrollHeight;
            </script>
        <?php else: ?>
            <div style="display:flex; justify-content:center; align-items:center; height:100%; color:#9ca3af;">
                Selecciona o crea un canal para comenzar.
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include 'view/templates/profile_modal.php'; ?>

<!-- Create Channel Modal -->
<div class="modal-overlay" id="createChannelModal">
    <div class="modal-card">
        <button class="close-modal" onclick="closeModal('createChannelModal')">&times;</button>
        <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem; color: #111827;">Crear Nuevo Canal</h2>
        
        <form action="<?php echo BASE_URL; ?>?controller=Channel&action=create&<?php echo SID; ?>" method="POST">
            <input type="hidden" name="community_id" value="<?php echo $community_id; ?>">
            
            <div class="form-group">
                <label for="channel_name">Nombre del Canal</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 0.75rem; color: #9ca3af;">#</span>
                    <input type="text" id="channel_name" name="nombre" placeholder="ej. general" required style="padding-left: 2rem;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="channel_desc">Descripción (Opcional)</label>
                <textarea id="channel_desc" name="descripcion" rows="3" placeholder="¿De qué trata este canal?"></textarea>
            </div>

            <div class="form-group">
                <label for="channel_type">Tipo de Canal</label>
                <select id="channel_type" name="tipo">
                    <option value="publico">Público - Visible para todos</option>
                    <option value="privado">Privado - Solo invitación (Próximamente)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Crear Canal</button>
        </form>
    </div>
</div>

<!-- Member List Modal -->
<div class="modal-overlay" id="memberListModal">
    <div class="modal-card" style="max-width: 400px;">
        <button class="close-modal" onclick="closeModal('memberListModal')">&times;</button>
        <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem; color: #111827;">Miembros</h2>
        
        <!-- Search -->
        <div style="margin-bottom: 1rem;">
            <input type="text" id="memberSearch" placeholder="Buscar por nombre..." onkeyup="filterMembers()" 
                   style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: #f3f4f6;">
        </div>
        
        <!-- List -->
        <div class="member-list-content" style="max-height: 400px; overflow-y: auto;">
            <?php foreach($members as $member): ?>
                <div class="member-item" data-name="<?php echo strtolower(htmlspecialchars($member['nombre'])); ?>" 
                     onclick="showUserProfile(<?php echo $member['id_usuario']; ?>)"
                     style="display: flex; align-items: center; padding: 0.75rem; border-bottom: 1px solid #f3f4f6; transition: background 0.2s; cursor: pointer;">
                    <?php if($member['avatar']): ?>
                        <img src="<?php echo htmlspecialchars($member['avatar']); ?>" alt="" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 1rem;">
                    <?php else: ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #6b7280; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: white; margin-right: 1rem;">
                            <?php echo strtoupper(substr($member['nombre'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="flex-grow: 1;">
                        <div style="font-weight: 500; font-size: 1rem; color: #111827;"><?php echo htmlspecialchars($member['nombre']); ?></div>
                        <div style="font-size: 0.8rem; color: #6b7280;"><?php echo ucfirst($member['rol']); ?></div>
                    </div>
                    
                    <?php if($member['rol'] == 'admin'): ?>
                        <span title="Admin" style="font-size: 1.2rem; color: #fbbf24;">👑</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Public Profile Viewer Modal -->
<div class="modal-overlay" id="publicProfileModal">
    <div class="modal-card" style="max-width: 400px; text-align: center;">
        <button class="close-modal" onclick="closeModal('publicProfileModal')">&times;</button>
        
        <div style="margin-bottom: 1.5rem; display: flex; justify-content: center;">
            <img id="publicProfileAvatar" src="" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #f3f4f6; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div id="publicProfileNoAvatar" style="width: 100px; height: 100px; border-radius: 50%; background-color: #6b7280; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; display: none;"></div>
        </div>
        
        <h2 id="publicProfileName" style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem; color: #111827;"></h2>
        <div id="publicProfileDesc" style="color: #6b7280; font-size: 0.95rem; line-height: 1.5; background: #f9fafb; padding: 1rem; border-radius: 0.5rem; text-align: left; margin-bottom: 1.5rem;"></div>
        
        <!-- DM Button -->
        <a id="dmButton" href="#" class="btn btn-primary" style="width: 100%; display: block; text-decoration: none; padding: 0.75rem;">
            💬 Enviar Mensaje
        </a>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
    
    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    function showUserProfile(userId) {
        console.log("Opening profile for user: " + userId);
        // Fetch user data
        fetch('<?php echo BASE_URL; ?>?controller=User&action=get_public_profile&id=' + userId + '&<?php echo SID; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert("Error al cargar perfil");
                    return;
                }
                
                // Populate Modal
                document.getElementById('publicProfileName').innerText = data.nombre;
                
                const avatarImg = document.getElementById('publicProfileAvatar');
                const noAvatarDiv = document.getElementById('publicProfileNoAvatar');
                
                if (data.avatar) {
                    avatarImg.src = data.avatar;
                    avatarImg.style.display = 'block';
                    noAvatarDiv.style.display = 'none';
                } else {
                    avatarImg.style.display = 'none';
                    noAvatarDiv.innerText = data.nombre.charAt(0).toUpperCase();
                    noAvatarDiv.style.display = 'flex';
                }
                
                const desc = data.descripcion ? data.descripcion : "Este usuario no ha añadido una descripción.";
                document.getElementById('publicProfileDesc').innerText = desc;
                
                // Update DM Button
                document.getElementById('dmButton').href = "<?php echo BASE_URL; ?>?controller=DirectMessage&action=chat&user_id=" + data.id + "&<?php echo SID; ?>";
                
                // Hide DM button if it's me
                if (data.id == <?php echo $_SESSION['user_id']; ?>) {
                    document.getElementById('dmButton').style.display = 'none';
                } else {
                    document.getElementById('dmButton').style.display = 'block';
                }

                openModal('publicProfileModal');
            })
            .catch(err => console.error(err));
    }

    function filterMembers() {
        var input = document.getElementById('memberSearch');
        var filter = input.value.toLowerCase();
        var nodes = document.getElementsByClassName('member-item');

        for (i = 0; i < nodes.length; i++) {
            if (nodes[i].getAttribute('data-name').includes(filter)) {
                nodes[i].style.display = "flex";
            } else {
                nodes[i].style.display = "none";
            }
        }
    }
</script>

</body>
</html>
