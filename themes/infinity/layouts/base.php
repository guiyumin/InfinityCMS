<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $theme->get('title', $theme->config('app.name')) ?></title>

    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?= $theme->asset('css/style.css') ?>">

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <?php if ($theme->has('head')): ?>
        <?= $theme->raw('head') ?>
    <?php endif; ?>
</head>
<body>
    <?php $theme->partial('header'); ?>

    <main class="main-content">
        <?php if ($theme->has('hasPendingMigrations') && $theme->raw('hasPendingMigrations') && $theme->uriIs('/admin*')): ?>
        <div class="migration-alert" style="background: #fff3cd; border: 1px solid #ffc107; border-left: 4px solid #ffc107; padding: 1rem 1.5rem; margin: 1rem auto; max-width: 1200px; border-radius: 4px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <div>
                    <strong style="color: #856404; display: block; margin-bottom: 0.25rem;">Database Update Required</strong>
                    <span style="color: #856404;">You have <?= $theme->raw('pendingMigrationsCount') ?> pending database migration<?= $theme->raw('pendingMigrationsCount') > 1 ? 's' : '' ?> that need to be run.</span>
                </div>
            </div>
            <a href="<?= $theme->url('/admin/migrations') ?>" style="background: #ffc107; color: #000; padding: 0.5rem 1.25rem; border-radius: 4px; text-decoration: none; font-weight: 600; white-space: nowrap; transition: background 0.2s;" onmouseover="this.style.background='#e0a800'" onmouseout="this.style.background='#ffc107'">
                Run Migrations
            </a>
        </div>
        <?php endif; ?>

        <?= $theme->raw('content') ?>
    </main>

    <?php $theme->partial('footer'); ?>

    <!-- Theme JS -->
    <script src="<?= $theme->asset('js/main.js') ?>"></script>

    <?php if ($theme->has('scripts')): ?>
        <?= $theme->raw('scripts') ?>
    <?php endif; ?>
</body>
</html>
