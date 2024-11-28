<?php if (!empty($_SESSION['message'])): ?>
        <div id="message" class="message <?= htmlspecialchars($_SESSION['message_type']) ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); // Rensa meddelandet ?>
    <?php endif; ?>