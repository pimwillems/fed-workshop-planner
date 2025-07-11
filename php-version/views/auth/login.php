<?php
ob_start();
?>

<div class="container" style="max-width: 400px; margin: 4rem auto; padding: 2rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
            Welcome Back
        </h1>
        <p style="color: var(--text-secondary);">
            Sign in to manage your workshops
        </p>
    </div>

    <?php if (isset($error)): ?>
    <div style="background: var(--error); color: var(--error-text); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo Paths::getRelativeUrl('login'); ?>" class="card" style="padding: 2rem;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input" 
                required 
                autofocus
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
                autocomplete="current-password"
            >
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
            Sign In
        </button>

        <div style="text-align: center;">
            <p style="color: var(--text-secondary); margin: 0;">
                Don't have an account? 
                <a href="<?php echo Paths::getRelativeUrl('register'); ?>" style="color: var(--color-dev-dark); text-decoration: none; font-weight: 500;">
                    Register here
                </a>
            </p>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>