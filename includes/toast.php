<?php
/**
 * Toast Notification System
 */
$flash = get_flash();
if ($flash):
?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-<?php echo $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : 'info'); ?> text-white">
            <strong class="me-auto">
                <?php 
                echo $flash['type'] === 'success' ? '✓ Success' : ($flash['type'] === 'error' ? '✗ Error' : 'ℹ Info');
                ?>
            </strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    </div>
</div>
<script>
setTimeout(function() {
    var toast = document.querySelector('.toast');
    if (toast) {
        var bsToast = new bootstrap.Toast(toast);
        bsToast.hide();
    }
}, 5000);
</script>
<?php endif; ?>
