<?php
ob_start();
?>

<div class="container" style="max-width: 400px; margin: 4rem auto; padding: 2rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
            Create Account
        </h1>
        <p style="color: var(--text-secondary);">
            Join as a teacher to create workshops
        </p>
    </div>

    <?php if (isset($error)): ?>
    <div style="background: var(--error); color: var(--error-text); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo Paths::getRelativeUrl('register'); ?>" class="card" style="padding: 2rem;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-input" 
                required 
                autofocus
                autocomplete="name"
                minlength="2"
                maxlength="100"
            >
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input" 
                required
                autocomplete="email"
            >
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
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
            <label for="confirm_password" class="form-label">Confirm Password</label>
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
            Create Account
        </button>

        <div style="text-align: center;">
            <p style="color: var(--text-secondary); margin: 0;">
                Already have an account? 
                <a href="<?php echo Paths::getRelativeUrl('login'); ?>" style="color: var(--color-dev-dark); text-decoration: none; font-weight: 500;">
                    Sign in here
                </a>
            </p>
        </div>
    </form>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function() {
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