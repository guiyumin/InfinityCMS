<header class="site-header" x-data="{ mobileMenuOpen: false }">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="<?= $theme->url('/') ?>">
                    <h1><?= $theme->config('app.name') ?></h1>
                </a>
            </div>

            <!-- Mobile menu toggle -->
            <button
                class="mobile-menu-toggle"
                @click="mobileMenuOpen = !mobileMenuOpen"
                aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Navigation -->
            <nav class="main-nav" :class="{ 'active': mobileMenuOpen }">
                <ul>
                    <li><a href="<?= $theme->url('/') ?>">Home</a></li>
                    <li><a href="<?= $theme->url('/about') ?>">About</a></li>
                    <li><a href="<?= $theme->url('/blog') ?>">Blog</a></li>
                    <li><a href="<?= $theme->url('/contact') ?>">Contact</a></li>
                    <?php if ($theme->isLoggedIn()): ?>
                        <li><a href="<?= $theme->url('/admin') ?>">Dashboard</a></li>
                        <li><a href="<?= $theme->url('/logout') ?>">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= $theme->url('/login') ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>
