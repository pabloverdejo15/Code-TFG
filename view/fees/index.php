<?php include 'view/templates/header.php'; ?>

<!-- Scoped Dark Theme -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/theme-dark.css?v=<?php echo time(); ?>">

<div class="l-app">
    <?php include 'view/components/sidebar.php'; ?>

    <?php 
    $active_section = 'fees';
    include 'view/components/community_sidebar.php'; 
    ?>

    <main class="l-app__main">
        <header class="l-app__header">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div style="display: flex; align-items: center;">
                    <div>
                        <h1 style="font-size: var(--fs-lg); font-weight: 600; color: var(--c-text-main);">Cuotas de Comunidad</h1>
                        <div style="font-size: var(--fs-xs); color: var(--c-text-muted);"><?php echo htmlspecialchars($community['nombre']); ?></div>
                    </div>
                </div>
                <?php if(isset($is_admin) && $is_admin): ?>
                    <button onclick="openModal('createFeeModal')" class="btn btn--primary">
                        + Crear Cuota
                    </button>
                <?php endif; ?>
            </div>
        </header>

        <div class="l-app__content" style="display: flex; gap: var(--space-2xl);">
            <div style="flex: 1;">
                <?php if(empty($fees)): ?>
                    <div class="empty-state animate-fade-up">
                        <div class="empty-state__icon">💳</div>
                        <p class="text-muted">No tienes cuotas pendientes o registradas en esta comunidad.</p>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                        <?php foreach($fees as $fee): ?>
                            <article class="notice-card animate-fade-up" style="position: relative; overflow: hidden; padding-left: 24px;" id="fee-card-<?php echo $fee['id_cuota']; ?>">
                                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 6px; background-color: <?php 
                                    if($fee['estado_calculado'] == 'vencida') echo 'var(--c-error)';
                                    elseif($fee['estado_calculado'] == 'pagada') echo 'var(--c-success, #10b981)';
                                    else echo 'var(--c-primary)';
                                ?>;"></div>
                                
                                <header style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-sm);">
                                    <span class="badge" style="background: var(--c-bg-body); border: 1px solid var(--c-border); color: var(--c-text-main); text-transform: uppercase; font-size: 0.65rem; font-weight: 700;">
                                        <?php 
                                            if($fee['estado_calculado'] == 'vencida') echo '<span style="color: var(--c-error);">🔴 Vencida</span>';
                                            elseif($fee['estado_calculado'] == 'pagada') echo '<span style="color: var(--c-success, #10b981);">🟢 Pagada</span>';
                                            else echo '<span style="color: var(--c-primary);">🟡 Pendiente</span>';
                                        ?>
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: var(--fs-xs); color: var(--c-text-muted);">
                                            Vence: <?php echo date('d M Y', strtotime($fee['fecha_vencimiento'])); ?>
                                        </span>
                                    </div>
                                </header>
                                
                                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                    <div>
                                        <h3 style="font-size: var(--fs-lg); font-weight: 600; color: var(--c-text-main); margin-bottom: 4px;">
                                            <?php echo htmlspecialchars($fee['concepto']); ?>
                                        </h3>
                                        <div style="font-size: var(--fs-2xl); font-weight: 700; color: var(--c-text-main);">
                                            <?php echo number_format($fee['monto'], 2, ',', '.'); ?> €
                                        </div>
                                    </div>
                                    
                                    <?php if($fee['estado_calculado'] != 'pagada'): ?>
                                        <button onclick="openPaymentModal(<?php echo $fee['id_cuota']; ?>, <?php echo $fee['monto']; ?>, '<?php echo htmlspecialchars($fee['concepto']); ?>')" class="btn btn--primary" style="padding: 0.5rem 1.5rem; border-radius: var(--radius-pill); box-shadow: var(--shadow-soft);">
                                            Pagar ahora
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Create Fee Modal (Admin only) -->
<?php if(isset($is_admin) && $is_admin): ?>
<div id="createFeeModal" class="modal-overlay" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px);">
    <div style="background: var(--c-bg-card); padding: var(--space-xl); border-radius: var(--radius-lg); width: 100%; max-width: 450px; box-shadow: var(--shadow-floating); border: 1px solid var(--c-border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-lg);">
            <h2 style="font-size: var(--fs-xl); font-weight: 700;">💳 Crear Cuota Global</h2>
            <button class="btn-icon" onclick="closeModal('createFeeModal')" style="padding: 4px;">✕</button>
        </div>
        
        <p style="font-size: var(--fs-sm); color: var(--c-text-muted); margin-bottom: var(--space-lg);">Esta cuota se generará automáticamente para <strong>todos</strong> los vecinos de la comunidad.</p>
        
        <form action="<?php echo BASE_URL; ?>?controller=Fee&action=create&<?php echo SID; ?>" method="POST">
            <input type="hidden" name="community_id" value="<?php echo $community['id_comunidad']; ?>">
            
            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Concepto</label>
                <input type="text" class="input chat-input-box__field" style="background: var(--c-bg-app); border: 1px solid var(--c-border);" name="concepto" placeholder="Ej. Cuota Mensual Mayo 2026" required>
            </div>
            
            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Monto (€)</label>
                <input type="number" step="0.01" class="input chat-input-box__field" style="background: var(--c-bg-app); border: 1px solid var(--c-border);" name="monto" placeholder="50.00" required>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-xl);">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: var(--fs-sm);">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" class="input chat-input-box__field" style="background: var(--c-bg-app); border: 1px solid var(--c-border); color: var(--c-text-main);" required>
            </div>
            
            <div style="display: flex; gap: var(--space-sm);">
                <button type="submit" class="btn btn--primary" style="flex: 1;">Crear y Asignar</button>
                <button type="button" class="btn" style="border: 1px solid var(--c-border);" onclick="closeModal('createFeeModal')">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Payment Simulation Modal -->
<div id="simulatePaymentModal" class="modal-overlay" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px);">
    <div style="background: var(--c-bg-card); padding: var(--space-xl); border-radius: var(--radius-lg); width: 100%; max-width: 400px; box-shadow: var(--shadow-floating); border: 1px solid var(--c-border); text-align: center;">
        
        <div id="payment-form-view">
            <h2 style="font-size: var(--fs-xl); font-weight: 700; margin-bottom: var(--space-sm);">Simulación de Pago</h2>
            <p id="pay-concept" style="font-size: var(--fs-sm); color: var(--c-text-muted); margin-bottom: var(--space-md);"></p>
            <div id="pay-amount" style="font-size: 2.5rem; font-weight: 800; color: var(--c-text-main); margin-bottom: var(--space-xl);"></div>
            
            <div style="display: flex; gap: var(--space-sm);">
                <button type="button" class="btn btn--primary" style="flex: 1; padding: 0.8rem;" onclick="processPayment()">
                    Confirmar Pago
                </button>
                <button type="button" class="btn" style="border: 1px solid var(--c-border);" onclick="closeModal('simulatePaymentModal')">Cancelar</button>
            </div>
        </div>

        <div id="payment-loading-view" style="display: none; padding: 2rem 0;">
            <div style="font-size: 3rem; animation: pulse 1.5s infinite;">💳</div>
            <p style="margin-top: 1rem; color: var(--c-text-main);">Procesando pago seguro...</p>
        </div>

        <div id="payment-success-view" style="display: none; padding: 2rem 0;">
            <div style="font-size: 4rem; color: var(--c-success, #10b981); animation: popIn 0.5s ease-out;">✓</div>
            <h3 style="font-size: var(--fs-lg); color: var(--c-text-main); margin-top: 1rem;">¡Pago realizado correctamente!</h3>
            <p style="font-size: var(--fs-sm); color: var(--c-text-muted); margin-top: 0.5rem; margin-bottom: 1.5rem;">Tu cuota ha sido registrada.</p>
            <button type="button" class="btn" style="border: 1px solid var(--c-border); width: 100%;" onclick="window.location.reload()">Cerrar</button>
        </div>

    </div>
</div>

<style>
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes popIn {
    0% { transform: scale(0.5); opacity: 0; }
    80% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
    let currentFeeId = null;

    function openModal(id) { 
        document.getElementById(id).style.display = 'flex'; 
    }
    
    function closeModal(id) { 
        document.getElementById(id).style.display = 'none'; 
    }

    function openPaymentModal(feeId, amount, concept) {
        currentFeeId = feeId;
        document.getElementById('pay-concept').innerText = concept;
        document.getElementById('pay-amount').innerText = Number(amount).toLocaleString('es-ES', { minimumFractionDigits: 2 }) + ' €';
        
        // Reset views
        document.getElementById('payment-form-view').style.display = 'block';
        document.getElementById('payment-loading-view').style.display = 'none';
        document.getElementById('payment-success-view').style.display = 'none';
        
        openModal('simulatePaymentModal');
    }

    async function processPayment() {
        if (!currentFeeId) return;
        
        document.getElementById('payment-form-view').style.display = 'none';
        document.getElementById('payment-loading-view').style.display = 'block';
        
        try {
            const response = await fetch('<?php echo BASE_URL; ?>?controller=Fee&action=pay&<?php echo SID; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ fee_id: currentFeeId })
            });
            
            const data = await response.json();
            
            // Simulate network delay for realism
            setTimeout(() => {
                document.getElementById('payment-loading-view').style.display = 'none';
                
                if (data.success) {
                    document.getElementById('payment-success-view').style.display = 'block';
                } else {
                    alert(data.message || 'Hubo un error al procesar el pago.');
                    closeModal('simulatePaymentModal');
                }
            }, 1500);
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión.');
            closeModal('simulatePaymentModal');
        }
    }
</script>

<?php include 'view/templates/footer.php'; ?>
