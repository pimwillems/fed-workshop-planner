<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'FED Workshop Planner'); ?></title>
    <link rel="stylesheet" href="<?php echo Paths::asset('css/style.css'); ?>">
    <meta name="description" content="FED Workshop Planner - Schedule and manage educational workshops">
    <meta name="robots" content="index, follow">
    
    <!-- Security headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo Paths::asset('images/favicon.ico'); ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo Paths::asset('images/apple-touch-icon.png'); ?>">
    
    <!-- Theme detection -->
    <script>
        // Apply theme before page load to prevent flash
        const theme = localStorage.getItem('theme') || 
                     (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        if (theme === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>
<body>
    <nav class="nav">
        <div class="container">
            <div class="nav-content">
                <a href="<?php echo Paths::getRelativeUrl(); ?>" class="nav-brand">🎓 FED Learning Hub</a>
                
                <ul class="nav-links">
                    <li><a href="<?php echo Paths::getRelativeUrl(); ?>" class="nav-link <?php echo $_SERVER['REQUEST_URI'] === Paths::getRelativeUrl() ? 'active' : ''; ?>">📅 Schedule</a></li>
                    
                    <?php if ($user ?? false): ?>
                        <li><a href="<?php echo Paths::getRelativeUrl('dashboard'); ?>" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : ''; ?>">📊 Dashboard</a></li>
                        <li><a href="<?php echo Paths::getRelativeUrl('change-password'); ?>" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'change-password') !== false ? 'active' : ''; ?>">🔑 Change Password</a></li>
                        <li><a href="<?php echo Paths::getRelativeUrl('logout'); ?>" class="nav-link">🚪 Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo Paths::getRelativeUrl('login'); ?>" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'login') !== false ? 'active' : ''; ?>">🔐 Login</a></li>
                        <li><a href="<?php echo Paths::getRelativeUrl('register'); ?>" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'register') !== false ? 'active' : ''; ?>">✏️ Register</a></li>
                    <?php endif; ?>
                    
                    <li>
                        <button id="theme-toggle" class="btn" title="Toggle theme">
                            <span id="theme-icon">🌙</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <main>
        <?php echo $content; ?>
    </main>
    
    <footer style="background: var(--bg-secondary); border-top: 1px solid var(--border-color); padding: 2rem 0; margin-top: 3rem; text-align: center;">
        <div class="container">
            <p style="color: var(--text-muted); margin: 0;">
                &copy; <?php echo date('Y'); ?> FED Learning Hub. All rights reserved.
            </p>
        </div>
    </footer>
    
    <script src="<?php echo Paths::asset('js/app.js'); ?>"></script>
    
    <!-- Configuration for AJAX requests -->
    <script>
        window.appConfig = {
            basePath: '<?php echo Paths::getRelativeUrl(); ?>',
            apiPath: '<?php echo Paths::getRelativeUrl('api/'); ?>'
        };
        <?php if (isset($csrf_token)): ?>
        window.csrfToken = '<?php echo $csrf_token; ?>';
        <?php endif; ?>
    </script>
</body>
</html>