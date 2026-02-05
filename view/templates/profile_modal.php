<div id="profilePopover" class="profile-popover">
    <div class="profile-card" style="position: relative;">
        <!-- Close button for mobile or explicit closing -->
        <span class="close-popover" onclick="document.getElementById('profilePopover').style.display='none'" style="position: absolute; top: 0; right: 0; cursor: pointer; color: #6b7280; font-weight: bold;">&times;</span>
        
        <h3 style="margin-bottom: 1.5rem; text-align: center;">Editar Perfil</h3>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1rem; padding: 0.5rem; font-size: 0.85rem;">✅ Actualizado</div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>?controller=User&action=update&<?php echo SID; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">

            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="position: relative; display: inline-block;">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo BASE_URL . $user['avatar']; ?>" alt="Avatar" class="avatar-preview" id="avatarPreview">
                    <?php else: ?>
                        <div class="no-avatar" id="avatarPlaceholder">👤</div>
                    <?php endif; ?>
                    
                    <label for="avatar" class="btn btn-primary" style="position: absolute; bottom: 0; right: -5px; border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-md);" title="Cambiar foto">
                        <span style="font-size: 1rem;">📷</span>
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewImage(this)">
                </div>
            </div>

            <div class="form-group" style="text-align: left; margin-bottom: 1rem;">
                <label for="nombre_perfil" style="font-size: 0.85rem;">Nombre Completo</label>
                <input type="text" id="nombre_perfil" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required style="padding: 0.5rem;">
            </div>

            <div class="form-group" style="text-align: left; margin-bottom: 1rem;">
                <label for="descripcion_perfil" style="font-size: 0.85rem;">Sobre mí</label>
                <textarea id="descripcion_perfil" name="descripcion" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; box-sizing: border-box; font-family: inherit;" placeholder="Cuéntanos algo sobre ti..."><?php echo htmlspecialchars($user['descripcion']); ?></textarea>
            </div>

            <div class="form-group" style="text-align: left; margin-bottom: 1rem;">
                <label style="font-size: 0.85rem;">Correo</label>
                <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="padding: 0.5rem; background-color: #f3f4f6; color: #6b7280;">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">Guardar</button>
        </form>
    </div>
</div>

<script>
    var activeTrigger = null;

    function toggleProfilePopover(event) {
        event.preventDefault();
        event.stopPropagation();
        
        var popover = document.getElementById('profilePopover');
        activeTrigger = event.currentTarget;
        
        if (popover.style.display === 'block') {
            closeProfilePopover();
        } else {
            popover.style.display = 'block';
            repositionPopover();
        }
    }
    
    function repositionPopover() {
        var popover = document.getElementById('profilePopover');
        if (!popover || popover.style.display !== 'block' || !activeTrigger) return;
        
        var trigger = activeTrigger;
        
        // Positioning Logic
        var rect = trigger.getBoundingClientRect();
        
        // Ensure we get the correct rendered width (including padding/borders)
        var popoverWidth = popover.offsetWidth; 
        // Fallback or override if display:none prevents reading (though we set block above)
        if (popoverWidth === 0) popoverWidth = 370; // Approximation if needed, but display=block should work
        
        var popoverHeight = popover.offsetHeight; // Get actual height
        
        var top;
        // Check if we should render above (if in bottom half of screen)
        var spaceBelow = window.innerHeight - rect.bottom;
        var openUpwards = (rect.bottom > window.innerHeight / 2) && (spaceBelow < popoverHeight);

        popover.style.boxShadow = "0 25px 50px -12px rgba(0, 0, 0, 0.9)"; // Enhanced shadow

        if (openUpwards) {
            // Open Upwards
            top = rect.top + window.scrollY - popoverHeight - 10;
            popover.classList.add('popover-above');
            // Animate from Bottom Right (approx arrow loc)
            popover.style.transformOrigin = (actualArrowRight + 8) + 'px bottom'; 
        } else {
            // Open Downwards (Default)
            top = rect.bottom + window.scrollY + 10;
            popover.classList.remove('popover-above');
            // Animate from Top Right (approx arrow loc)
            popover.style.transformOrigin = (actualArrowRight + 8) + 'px top'; 
        }
        
        // Use the proper left
        popover.style.setProperty('--arrow-right', actualArrowRight + 'px');
        
        // Because we're using "right" for arrow, but transformOrigin X is from the LEFT of the element.
        // Wait, 'actualArrowRight' is CSS 'right' value for pseudo element (distance from right edge).
        // transform-origin x-offset is usually from LEFT.
        // So OriginX = Width - ActualArrowRight - 8 (center of arrow)
        
        var originX = popoverWidth - actualArrowRight - 8;
        if (openUpwards) {
             popover.style.transformOrigin = originX + 'px bottom';
        } else {
             popover.style.transformOrigin = originX + 'px top';
        }

        popover.style.top = top + 'px';
        popover.style.left = left + 'px';
        
        // Re-trigger animation
        popover.style.animation = 'none';
        popover.offsetHeight; /* trigger reflow */
        popover.style.animation = 'popIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards';
    }

    function closeProfilePopover() {
        var popover = document.getElementById('profilePopover');
        popover.style.display = 'none';
        activeTrigger = null;
    }

    // Close when clicking outside
    document.addEventListener('click', function(event) {
        var popover = document.getElementById('profilePopover');
        var trigger = activeTrigger;
        
        if (popover && popover.style.display === 'block') {
            if (!popover.contains(event.target) && (!trigger || !trigger.contains(event.target))) {
                closeProfilePopover();
            }
        }
    });
    
    // Reposition on resize
    window.addEventListener('resize', repositionPopover);
    window.addEventListener('scroll', repositionPopover); // Optional: also update on scroll

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('avatarPreview');
                var placeholder = document.getElementById('avatarPlaceholder');
                
                if (preview) {
                    preview.src = e.target.result;
                } else if (placeholder) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'avatar-preview';
                    img.id = 'avatarPreview';
                    placeholder.parentNode.replaceChild(img, placeholder);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
