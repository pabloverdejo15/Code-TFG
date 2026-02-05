<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Comunidades - Hello Neighbor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass-bg: rgba(20, 20, 20, 0.6);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shine: rgba(255, 255, 255, 0.05);
            --primary-glow: rgba(0, 85, 255, 0.5);
            --text-main: #ffffff;
            --text-muted: #a0a0a0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #000000;
            color: var(--text-main);
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated Background Canvas */
        #darkveil-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            pointer-events: none;
        }

        /* Layout */
        .main-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 2rem;
            position: relative;
            z-index: 1;
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6rem;
            padding-top: 2rem;
            animation: slideDown 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .page-title h1 {
            font-size: 3rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }

        .page-title p {
            margin: 0.5rem 0 0;
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* User Profile Badge */
        .user-badge {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--glass-bg);
            padding: 0.5rem 1rem 0.5rem 0.5rem;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .user-badge:hover {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(40, 40, 40, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0055FF;
        }

        .user-info span {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .user-info small {
            color: var(--text-muted);
            font-size: 0.75rem;
        }
        
        /* Logout Button */
        .btn-logout {
            margin-left: 1rem;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.1);
        }

        /* Action Buttons */
        .actions-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2.5rem;
            opacity: 0;
            animation: fadeIn 0.8s ease 0.3s forwards;
        }

        .btn-glass {
            background: var(--glass-bg);
            color: white;
            border: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0, 85, 255, 0.2);
        }

        .btn-glass.primary {
            background: rgba(0, 85, 255, 0.2);
            border-color: rgba(0, 85, 255, 0.4);
        }

        .btn-glass.primary:hover {
            background: rgba(0, 85, 255, 0.4);
            box-shadow: 0 0 30px rgba(0, 85, 255, 0.4);
        }

        /* Community Grid - 2x2 Fixed */
        .community-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3rem;
            list-style: none;
            padding: 0;
        }

        @media (max-width: 900px) {
            .community-grid {
                grid-template-columns: 1fr;
            }
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }
        }

        .community-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid var(--glass-border);
            border-radius: 2rem;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            animation: slideUp 0.6s ease forwards;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 320px;
        }
        
        .community-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        }

        .community-card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: rgba(0, 85, 255, 0.5);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 85, 255, 0.1);
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 2rem;
            font-weight: 600;
            color: #fff;
            margin: 0;
            line-height: 1.2;
        }

        .role-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
            margin-left: 1rem;
        }

        .role-admin {
            background: rgba(255, 180, 0, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(255, 180, 0, 0.3);
        }

        .role-vecino {
            background: rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-desc {
            color: #d1d5db;
            font-size: 1.1rem;
            line-height: 1.6;
            flex-grow: 1;
            margin-bottom: 2rem;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 1.5rem;
        }

        /* Buttons Styling */
        .btn-enter {
            background: #0055FF;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(0, 85, 255, 0.3);
        }
        
        .btn-enter:hover {
            background: #0044cc;
            transform: translateX(3px);
            box-shadow: 0 6px 15px rgba(0, 85, 255, 0.4);
        }

        .btn-leave {
            background: #000000;
            color: white;
            border: 2px solid #0055FF;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-leave:hover {
            background: rgba(0, 85, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(0, 85, 255, 0.4);
        }

        .btn-icon {
            background: transparent;
            color: var(--text-muted);
            border: none;
            cursor: pointer;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 1.2rem;
        }

        .btn-icon:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .btn-icon.danger:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 2rem;
            border: 2px dashed rgba(255, 255, 255, 0.1);
            animation: fadeIn 1s ease;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            display: block;
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal-overlay.active { display: flex; opacity: 1; }
        
        .modal-content {
            background: #111;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2.5rem;
            border-radius: 1.5rem;
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: scale(0.9);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .modal-overlay.active .modal-content { transform: scale(1); }
        
        .close-modal {
            position: absolute;
            top: 1.5rem; right: 1.5rem;
            background: none; border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            transition: 0.2s;
        }
        .close-modal:hover { color: white; transform: rotate(90deg); }

        .modal-title { font-size: 1.8rem; margin-bottom: 0.5rem; font-weight: 700; color: white; }
        .modal-subtitle { color: var(--text-muted); margin-bottom: 2rem; }

        /* Form Elements */
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; color: #ccc; font-size: 0.9rem; }
        .form-input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-family: inherit;
            transition: 0.3s;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            background: rgba(0, 85, 255, 0.05);
            border-color: #0055FF;
            box-shadow: 0 0 0 4px rgba(0, 85, 255, 0.1);
        }
        
        .btn-block {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            justify-content: center;
        }

        /* Staggered animation delays for cards */
        .community-card:nth-child(1) { animation-delay: 0.1s; }
        .community-card:nth-child(2) { animation-delay: 0.2s; }
        .community-card:nth-child(3) { animation-delay: 0.3s; }
        .community-card:nth-child(4) { animation-delay: 0.4s; }
        .community-card:nth-child(5) { animation-delay: 0.5s; }

        /* Keyframes */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        /* Profile Popover Overrides for Dark Theme */
        .profile-popover {
            background: rgba(30, 30, 30, 0.95) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5) !important;
        }
        
        .profile-popover::before {
            background-color: #1e1e1e !important;
            border-left: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-bottom: none !important;
            border-right: none !important;
        }

        .profile-popover.popover-above::before {
            border-top: none !important;
            border-left: none !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        
        .profile-popover h3 { color: white !important; }
        .profile-popover label { color: #ccc !important; }
        
        .profile-popover input, 
        .profile-popover textarea {
            background: rgba(0, 0, 0, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        
        .profile-popover input:focus,
        .profile-popover textarea:focus {
            border-color: #0055FF !important;
        }
        
        .profile-popover .close-popover {
            color: #aaa !important;
        }
        .profile-popover .close-popover:hover {
            color: white !important;
        }

    </style>
</head>
<body>

    <!-- Animated Background -->
    <canvas id="darkveil-canvas"></canvas>

    <div class="main-wrapper">
        
        <!-- Header -->
        <header class="header-section">
            <div class="page-title">
                <h1>Mis Comunidades</h1>
                <p>Gestiona tus espacios de vecindad</p>
            </div>
            
            <div style="display: flex; align-items: center;">
                <div class="user-badge" onclick="toggleProfilePopover(event)" style="cursor: pointer;">
                    <?php if(!empty($_SESSION['user_avatar'])): ?>
                        <img src="<?php echo BASE_URL . $_SESSION['user_avatar']; ?>" class="user-avatar" alt="Avatar">
                    <?php else: ?>
                        <div class="user-avatar" style="background: linear-gradient(135deg, #6366f1, #0055FF); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">👤</div>
                    <?php endif; ?>
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <small>Ver Perfil</small>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>?controller=Auth&action=logout&<?php echo SID; ?>" class="btn-logout" title="Cerrar Sesión">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </header>

        <!-- Actions -->
        <div class="actions-bar">
            <button onclick="openModal('createModal')" class="btn-glass primary">
                <span style="font-size: 1.2rem;">+</span> Nueva Comunidad
            </button>
            <button onclick="openModal('joinModal')" class="btn-glass">
                <span style="font-size: 1.2rem;">#</span> Unirse con Código
            </button>
        </div>

        <!-- Community Grid -->
        <?php if(empty($communities)): ?>
            <div class="empty-state">
                <span class="empty-icon">🏘️</span>
                <h2 style="color: white; margin-bottom: 1rem;">No tienes comunidades aún</h2>    
                <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 2rem;">
                    Las comunidades son el corazón de Hello Neighbor. Crea una para tu edificio o únete a una existente.
                </p>
                <button onclick="openModal('createModal')" class="btn-glass primary">Crear mi primera comunidad</button>
            </div>
        <?php else: ?>
            <div class="community-grid">
                <?php foreach($communities as $comm): ?>
                    <div class="community-card">
                        <div>
                            <div class="card-header">
                                <h2 class="card-title"><?php echo htmlspecialchars($comm['nombre']); ?></h2>
                                <span class="role-badge role-<?php echo $comm['rol']; ?>"><?php echo ucfirst($comm['rol']); ?></span>
                            </div>
                            <div class="card-location">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <?php echo htmlspecialchars($comm['direccion']); ?>
                            </div>
                            <div class="card-desc">
                                <?php echo htmlspecialchars($comm['descripcion']); ?>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <?php if($comm['rol'] == 'admin'): ?>
                                    <a href="<?php echo BASE_URL; ?>?controller=Community&action=delete&id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn-icon danger" title="Eliminar Comunidad" onclick="return confirm('¿Estás seguro de que quieres eliminar esta comunidad permanentemente?');">
                                        🗑️
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo BASE_URL; ?>?controller=Community&action=leave&id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn-leave" title="Salir de la comunidad" onclick="return confirm('¿Seguro que quieres salir?');">
                                    Salir
                                </a>
                            </div>
                            
                            <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $comm['id_comunidad']; ?>&<?php echo SID; ?>" class="btn-enter">
                                Entrar <span style="margin-left: 5px;">&rarr;</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Create Community Modal -->
    <div id="createModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('createModal')">&times;</button>
            <h2 class="modal-title">Nueva Comunidad</h2>
            <p class="modal-subtitle">Configura un nuevo espacio para tus vecinos.</p>
            
            <form action="<?php echo BASE_URL; ?>?controller=Community&action=create&<?php echo SID; ?>" method="POST">
                <div class="form-group">
                    <label class="form-label" for="nombre">Nombre de la Comunidad *</label>
                    <input type="text" class="form-input" id="nombre" name="nombre" required placeholder="Ej. Edificio Las Flores 23">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="direccion">Dirección</label>
                    <input type="text" class="form-input" id="direccion" name="direccion" placeholder="Calle Ejemplo, 123">
                </div>

                <div class="form-group">
                    <label class="form-label" for="descripcion">Descripción</label>
                    <textarea class="form-input" id="descripcion" name="descripcion" rows="3" placeholder="Un espacio para coordinarnos..."></textarea>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn-glass primary btn-block">Crear Comunidad</button>
                    <button type="button" onclick="closeModal('createModal')" style="background: none; border: none; color: #6b7280; width: 100%; margin-top: 1rem; cursor: pointer; text-decoration: underline;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Join Community Modal -->
    <div id="joinModal" class="modal-overlay">
        <div class="modal-content" style="text-align: center;">
            <button class="close-modal" onclick="closeModal('joinModal')">&times;</button>
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔑</div>
            <h2 class="modal-title">Unirse a una Comunidad</h2>
            <p class="modal-subtitle">Introduce el código de invitación que te han compartido.</p>

            <form action="<?php echo BASE_URL; ?>?controller=Community&action=join&<?php echo SID; ?>" method="POST">
                <div class="form-group">
                    <input type="text" class="form-input" id="codigo_acceso" name="codigo_acceso" required placeholder="A1B2C3" maxlength="10" style="text-align: center; letter-spacing: 0.3rem; font-size: 1.5rem; text-transform: uppercase; font-weight: 700;">
                </div>
                
                <button type="submit" class="btn-glass primary btn-block" style="margin-top: 1rem;">Unirse ahora</button>
            </form>
        </div>
    </div>

    <!-- Profile Popover Integration -->
    <?php include 'view/templates/profile_modal.php'; ?>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Close on click outside
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeModal(overlay.id);
                }
            });
        });
    </script>
    
    <!-- Scripts for Background -->
    <script type="module" src="<?php echo BASE_URL; ?>js/DarkVeil.js?v=<?php echo time(); ?>"></script>

    <?php if(isset($_SESSION['show_welcome']) && $_SESSION['show_welcome']): ?>
        <?php unset($_SESSION['show_welcome']); // Show only once ?>
        
        <div id="welcome-overlay">
            <!-- Wave Decorations -->
            <div class="welcome-waves">
                <div class="wave-ring"></div>
                <div class="wave-ring"></div>
                <div class="wave-ring"></div>
            </div>
            
            <div class="welcome-content">
                <div class="welcome-avatar">
                   <?php if(!empty($_SESSION['user_avatar'])): ?>
                        <img src="<?php echo BASE_URL . $_SESSION['user_avatar']; ?>" alt="Avatar">
                    <?php else: ?>
                        <div class="welcome-placeholder">👤</div>
                    <?php endif; ?>
                </div>
                <h1 class="welcome-title">Bienvenido de nuevo, <span class="text-gradient"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></h1>
                <p class="welcome-subtitle">¿Qué quieres hacer hoy?</p>
            </div>
        </div>

        <style>
            #welcome-overlay {
                position: fixed;
                top: 0; left: 0; width: 100%; height: 100%;
                background: #000;
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: fadeOutOverlay 0.8s ease 3.5s forwards;
                overflow: hidden;
            }
            
            /* Wave Decorations */
            .welcome-waves {
                position: absolute;
                top: 50%; left: 50%;
                transform: translate(-50%, -50%);
                width: 100%; height: 100%;
                z-index: 0;
                pointer-events: none;
            }
            
            .wave-ring {
                position: absolute;
                top: 50%; left: 50%;
                transform: translate(-50%, -50%);
                border: 1px solid rgba(0, 85, 255, 0.3);
                border-radius: 50%;
                opacity: 0;
                box-shadow: 0 0 20px rgba(0, 85, 255, 0.1);
            }
            
            .wave-ring:nth-child(1) { width: 300px; height: 300px; animation: wavePulse 3s cubic-bezier(0, 0.2, 0.8, 1) infinite; }
            .wave-ring:nth-child(2) { width: 500px; height: 500px; animation: wavePulse 3s cubic-bezier(0, 0.2, 0.8, 1) 0.5s infinite; }
            .wave-ring:nth-child(3) { width: 700px; height: 700px; animation: wavePulse 3s cubic-bezier(0, 0.2, 0.8, 1) 1s infinite; }

            .welcome-content {
                text-align: center;
                opacity: 0;
                animation: contentIn 1s cubic-bezier(0.2, 0.8, 0.2, 1) 0.5s forwards;
                position: relative;
                z-index: 10;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .welcome-avatar {
                width: 100px;
                height: 100px;
                margin: 0 auto 2rem;
                opacity: 0;
                transform: scale(0.5);
                animation: popIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.8s forwards;
                position: relative;
            }
            
            .welcome-avatar img, .welcome-avatar .welcome-placeholder {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #0055FF;
                box-shadow: 0 0 40px rgba(0, 85, 255, 0.5);
            }
            
            .welcome-placeholder {
                background: linear-gradient(135deg, #6366f1, #0055FF);
                display: flex; align-items: center; justify-content: center;
                font-size: 3rem;
            }

            .welcome-title {
                font-size: 3.5rem;
                font-weight: 700;
                color: white;
                margin: 0 0 1rem;
                letter-spacing: -0.03em;
                transform: translateY(20px);
                opacity: 0;
                animation: slideUpFade 0.8s ease 1.2s forwards;
            }

            .welcome-subtitle {
                font-size: 1.5rem;
                color: var(--text-muted);
                margin: 0 0 2rem;
                transform: translateY(20px);
                opacity: 0;
                animation: slideUpFade 0.8s ease 1.6s forwards;
            }

            .text-gradient {
                background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            /* Animations */
            @keyframes fadeOutOverlay {
                to { opacity: 0; visibility: hidden; pointer-events: none; }
            }
            
            @keyframes contentIn {
                to { opacity: 1; }
            }

            @keyframes popIn {
                to { opacity: 1; transform: scale(1); }
            }

            @keyframes slideUpFade {
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes wavePulse {
                0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0; border-width: 1px; }
                50% { opacity: 0.5; border-width: 2px; border-color: rgba(0, 85, 255, 0.6); }
                100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; border-width: 0px; }
            }
        </style>
    <?php endif; ?>

    <!-- Global Loader for Actions -->
    <?php include 'view/templates/loader.php'; ?>

</body>
</html>
