<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $admin->get('title', 'Admin Dashboard') ?> - <?= config('app.name') ?></title>

    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?= $admin->asset('css/admin.css') ?>">

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <?php if ($admin->has('head')): ?>
        <?= $admin->get('head', '', false) ?>
    <?php endif; ?>
</head>
<body class="admin-body">
    <?php $admin->partial('header'); ?>

    <main class="admin-main">
        <?php if ($admin->has('hasPendingMigrations') && $admin->get('hasPendingMigrations', false, false)): ?>
        <div class="migration-alert">
            <div class="alert-content">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <div class="alert-message">
                    <strong>Database Update Required</strong>
                    <span>You have <?= $admin->get('pendingMigrationsCount', 0, false) ?> pending database migration<?= $admin->get('pendingMigrationsCount', 1, false) > 1 ? 's' : '' ?> that need to be run.</span>
                </div>
            </div>
            <a href="<?= $admin->url('/admin/migrations') ?>" class="alert-button">
                Run Migrations
            </a>
        </div>
        <?php endif; ?>

        <?= $admin->get('content', '', false) ?>
    </main>

    <?php $admin->partial('footer'); ?>

    <!-- Admin JS -->
    <script src="<?= $admin->asset('js/admin.js') ?>"></script>

    <?php if ($admin->has('scripts')): ?>
        <?= $admin->get('scripts', '', false) ?>
    <?php endif; ?>
</body>
</html>
