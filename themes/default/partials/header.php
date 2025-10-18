<header class="site-header" x-data="{ mobileMenuOpen: false }">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="<?= url('/') ?>">
                    <h1><?= config('app.name') ?></h1>
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
                    <li><a href="<?= url('/') ?>">Home</a></li>
                    <li><a href="<?= url('/about') ?>">About</a></li>
                    <li><a href="<?= url('/blog') ?>">Blog</a></li>
                    <li><a href="<?= url('/contact') ?>">Contact</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="<?= url('/admin') ?>">Dashboard</a></li>
                        <li><a href="<?= url('/logout') ?>">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= url('/login') ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>
