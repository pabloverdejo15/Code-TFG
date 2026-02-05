<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablón - <?php echo htmlspecialchars($community['nombre']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <style>
        .notice-container { width: 80%; max-width: 1400px; margin: 4rem auto; padding: 0; }
        
        /* Grid layout for notices */
        .notice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .notice-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }
        .notice-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        .notice-type-bar { height: 6px; width: 100%; }
        .type-averia { background-color: var(--danger); }
        .type-anuncio { background-color: var(--primary); }
        .type-reunion { background-color: var(--secondary); }
        .type-none { background-color: var(--gray); }

        .notice-content { padding: 1.5rem; flex-grow: 1; }
        .notice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        
        .badge { 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            font-weight: 700; 
            padding: 0.25rem 0.5rem; 
            border-radius: 4px; 
            letter-spacing: 0.05em;
        }
        .badge-averia { background: #fef2f2; color: var(--danger); }
        .badge-anuncio { background: #e0e7ff; color: var(--primary); }
        .badge-reunion { background: #ecfdf5; color: var(--secondary); }

        .date { color: var(--text-light); font-size: 0.85rem; }
        
        .creation-form {
            background: white;
            padding: 2.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 3rem;
            border: 1px solid var(--border);
        }
        .creation-form h3 { font-size: 1.5rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

<div class="notice-container">
    <div style="margin-bottom: 2rem;">
        <a href="<?php echo BASE_URL; ?>?controller=Channel&action=index&community_id=<?php echo $community['id_comunidad']; ?>&<?php echo SID; ?>" style="display:inline-flex; align-items:center; color: var(--text-muted); font-weight: 500;">
            &larr; Volver al Chat
        </a>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h1>Tablón de Avisos</h1>
            <p style="color:var(--text-muted); margin:0;">Información importante de <strong><?php echo htmlspecialchars($community['nombre']); ?></strong></p>
        </div>
        <!-- Toggle button or logic could go here -->
    </div>

    <div class="layout-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
        
        <!-- Left: Notices List -->
        <div class="notices-column">
            <?php if(empty($notices)): ?>
                <div style="text-align: center; padding: 4rem; color: var(--text-light); background: white; border-radius: var(--radius); border: 1px solid var(--border);">
                    <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📋</div>
                    <p>No hay avisos publicados todavía.</p>
                </div>
            <?php else: ?>
                <div class="notice-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach($notices as $notice): ?>
                        <div class="notice-card">
                            <div class="notice-type-bar type-<?php echo $notice['tipo']; ?>"></div>
                            <div class="notice-content">
                                <div class="notice-header">
                                    <span class="badge badge-<?php echo $notice['tipo']; ?>">
                                        <?php echo htmlspecialchars($notice['tipo']); ?>
                                    </span>
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <span class="date"><?php echo date('d M', strtotime($notice['fecha_publicacion'])); ?></span>
                                        <?php if($is_admin): ?>
                                            <a href="<?php echo BASE_URL; ?>?controller=Notice&action=delete&id=<?php echo $notice['id_aviso']; ?>&community_id=<?php echo $community['id_comunidad']; ?>&<?php echo SID; ?>" 
                                               onclick="return confirm('¿Estás seguro de querer eliminar este aviso?');"
                                               title="Eliminar Aviso"
                                               style="color: var(--text-light); opacity: 0.6; font-size: 1rem; margin-left: 0.5rem;">
                                                🗑️
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem; line-height: 1.4;">
                                    <?php echo htmlspecialchars($notice['titulo']); ?>
                                </h3>
                                <div style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($notice['descripcion'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Create Form -->
        <div class="creation-form" style="position: sticky; top: 2rem;">
            <h3 style="margin-bottom: 1rem;">📢 Publicar aviso</h3>
            <form action="<?php echo BASE_URL; ?>?controller=Notice&action=create&<?php echo SID; ?>" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                <input type="hidden" name="community_id" value="<?php echo $community['id_comunidad']; ?>">
                
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Título</label>
                    <input type="text" name="titulo" placeholder="Ej. Corte de agua" required style="font-weight: 600; width: 100%;">
                </div>
                
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Tipo</label>
                    <select name="tipo" style="cursor:pointer; width: 100%;">
                        <option value="anuncio">ℹ️ Información</option>
                        <option value="averia">⚠️ Avería / Incidencia</option>
                        <option value="reunion">📅 Reunión</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Detalles</label>
                    <textarea name="descripcion" rows="4" placeholder="Describe el aviso..." required style="resize: none; width: 100%;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Publicar Aviso</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>
