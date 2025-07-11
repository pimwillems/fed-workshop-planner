<?php
ob_start();
?>

<div class="container" style="max-width: 400px; margin: 4rem auto; padding: 2rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
            Change Password
        </h1>
        <p style="color: var(--text-secondary);">
            Update your account password
        </p>
    </div>

    <?php if (isset($error)): ?>
    <div style="background: var(--error); color: var(--error-text); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
    <div style="background: var(--success); color: var(--success-text); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
        <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo Paths::getRelativeUrl('change-password'); ?>" class="card" style="padding: 2rem;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label for="current_password" class="form-label">Current Password</label>
            <input 
                type="password" 
                id="current_password" 
                name="current_password" 
                class="form-input" 
                required 
                autofocus
                autocomplete="current-password"
            >
        </div>

        <div class="form-group">
            <label for="new_password" class="form-label">New Password</label>
            <input 
                type="password" 
                id="new_password" 
                name="new_password" 
                class="form-input" 
                required
                autocomplete="new-password"
                minlength="6"
            >
            <small style="color: var(--text-muted); font-size: 0.875rem;">
                Minimum 6 characters
            </small>
        </div>

        <div class="form-group">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                class="form-input" 
                required
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
            Update Password
        </button>

        <div style="text-align: center;">
            <a href="<?php echo Paths::getRelativeUrl('dashboard'); ?>" style="color: var(--color-dev-dark); text-decoration: none; font-weight: 500;">
                ← Back to Dashboard
            </a>
        </div>
    </form>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>