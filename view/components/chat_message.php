<?php
/**
 * Expected variables in scope:
 * @var array $msg (Contains 'id_usuario', 'contenido', 'time_formatted', 'spacing_tier', 'hide_avatar')
 * @var array $community (Community info)
 * @var array $members (Array of members in the community keyed by ID or standard array)
 * @var int $user_id (Current signed-in user)
 */

$isSelf = ($msg['id_usuario'] == $user_id);
$alignmentClass = $isSelf ? 'chat-group--self' : 'chat-group--other';
$bubbleClass = $isSelf ? 'chat-bubble--self' : 'chat-bubble--other';
$spacingClass = 'chat-group--' . ($msg['spacing_tier'] ?? 'wide');

// Find sender details
$senderName = "Vecino";
$senderAvatar = null;

if ($isSelf) {
    $senderName = "Tú";
} else {
    foreach ($members as $m) {
        if ($m['id_usuario'] == $msg['id_usuario']) {
            $senderName = $m['nombre'];
            $senderAvatar = isset($m['avatar']) ? $m['avatar'] : null;
            break;
        }
    }
}
?>

<div class="chat-group <?php echo $alignmentClass; ?> <?php echo $spacingClass; ?> animate-fade-up">
    <div class="chat-group__row">
        
        <!-- Avatar (Only if not self and not hidden by rhythm logic) -->
        <?php if (!$isSelf): ?>
            <?php if (empty($msg['hide_avatar'])): ?>
                <?php if ($senderAvatar): ?>
                    <img src="<?php echo BASE_URL . $senderAvatar; ?>" alt="<?php echo htmlspecialchars($senderName); ?>" class="avatar avatar--md chat-group__avatar">
                <?php else: ?>
                    <div class="avatar avatar--md chat-group__avatar" style="display: flex; align-items: center; justify-content: center; background: var(--c-primary); color: white; font-weight: 600; font-size: 14px;">
                        <?php echo strtoupper(substr($senderName, 0, 1)); ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Hidden Avatar Spacer to align clustered messages -->
                <div class="avatar avatar--md chat-group__avatar" style="background: transparent;"></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="chat-group__content">
            <!-- Sender Name (Only on 'wide' spaced blocks) -->
            <?php if (empty($msg['hide_avatar']) && !$isSelf): ?>
                <span class="chat-group__name"><?php echo htmlspecialchars($senderName); ?></span>
            <?php endif; ?>
            
            <div class="chat-bubble-wrapper">
                <div class="chat-bubble-tools">
                    <button class="btn-icon" title="Responder">↩️</button>
                    <button class="btn-icon" title="Reaccionar">❤️</button>
                    <!-- More options could be here -->
                </div>
                
                <div class="chat-bubble <?php echo $bubbleClass; ?>">
                    <p class="chat-bubble__text"><?php echo htmlspecialchars($msg['contenido']); ?></p>
                    <span class="chat-bubble__meta"><?php echo $msg['time_formatted'] ?? ''; ?></span>
                </div>
            </div>
        </div>
        
    </div>
</div>
