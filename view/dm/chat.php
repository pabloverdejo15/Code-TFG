<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat con <?php echo htmlspecialchars($target_user['nombre']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=<?php echo time(); ?>">
    <style>
        /* Reusing styles from channel/index.php broadly */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            display: block !important;
            width: 100%;
            background-color: #1f2937;
        }

        .chat-layout {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            position: relative;
            z-index: 10;
        }
        
        /* DarkVeil specific transparency override if needed, but for DM maybe keep it solid or glass? */
        /* Let's go with Glassmorphism to match app feel */
        .chat-layout {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-left: 1px solid rgba(255,255,255,0.1);
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .messages-area {
            flex-grow: 1;
            padding: 1rem;
            overflow-y: auto;
            background: rgba(243, 244, 246, 0.5);
        }
        
        .input-area {
            padding: 1rem;
            border-top: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.95);
        }

        .message-card {
            display: flex;
            margin-bottom: 1rem;
        }
        
        .message-card.own {
            flex-direction: row-reverse;
        }
        
        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            position: relative;
            word-wrap: break-word;
        }
        
        .message-card.own .message-bubble {
            background-color: var(--primary);
            color: white;
            border-bottom-right-radius: 0.25rem;
        }
        
        .message-card:not(.own) .message-bubble {
            background-color: white;
            color: #111827;
            border-bottom-left-radius: 0.25rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .back-btn {
            text-decoration: none;
            color: #4b5563;
            margin-right: 1rem;
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Avatar styles */
        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 0.75rem;
        }
    </style>
</head>
<body>

    <!-- Reuse DarkVeil Background -->
    <canvas id="darkveil-canvas" style="position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; pointer-events:none;"></canvas>
    <script type="module" src="<?php echo BASE_URL; ?>js/DarkVeil.js?v=<?php echo time(); ?>"></script>

    <div class="chat-layout">
        <header class="chat-header">
            <a href="javascript:history.back()" class="back-btn">&larr;</a>
            
            <?php if($target_user['avatar']): ?>
                <img src="<?php echo BASE_URL . $target_user['avatar']; ?>" class="chat-avatar">
            <?php else: ?>
                <div class="chat-avatar" style="background:#6b7280; display:flex; align-items:center; justify-content:center; color:white;">
                    <?php echo strtoupper(substr($target_user['nombre'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            
            <div>
                <div style="font-weight: bold; font-size: 1.1rem; color: #111827;">
                    <?php echo htmlspecialchars($target_user['nombre']); ?>
                </div>
                <div style="font-size: 0.8rem; color: #6b7280;">Mensaje Directo</div>
            </div>
        </header>

        <div class="messages-area" id="messages-container">
            <?php if(empty($messages)): ?>
                <div style="text-align: center; color: #9ca3af; margin-top: 2rem;">
                    Comienza la conversación con <?php echo htmlspecialchars($target_user['nombre']); ?>
                </div>
            <?php else: ?>
                <?php foreach($messages as $msg): ?>
                    <div class="message-card <?php echo ($msg['id_emisor'] == $my_user_id) ? 'own' : ''; ?>">
                        <div class="message-bubble">
                            <div style="line-height: 1.5; margin-bottom: 0.25rem;">
                                <?php echo nl2br(htmlspecialchars($msg['contenido'])); ?>
                            </div>
                            <div style="text-align: right; font-size: 0.7rem; opacity: 0.7;">
                                <?php echo date('H:i', strtotime($msg['fecha_envio'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="input-area">
            <form action="<?php echo BASE_URL; ?>?controller=DirectMessage&action=send&<?php echo SID; ?>" method="POST" style="display: flex; gap: 1rem;">
                <input type="hidden" name="receptor_id" value="<?php echo $target_user_id; ?>">
                <input type="text" name="contenido" style="flex-grow:1; padding:0.75rem; border-radius:1.5rem; border:1px solid #ccc; width: 100%; box-sizing: border-box;" placeholder="Escribe un mensaje..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary" style="border-radius:50%; width:48px; height:48px; padding:0; font-size:1.2rem; flex-shrink: 0;">➤</button>
            </form>
        </div>
    </div>

    <script>
        var msgContainer = document.getElementById("messages-container");
        msgContainer.scrollTop = msgContainer.scrollHeight;
        
        // Ensure body is transparent for DarkVeil
        document.body.style.backgroundColor = "transparent";
    </script>
</body>
</html>
