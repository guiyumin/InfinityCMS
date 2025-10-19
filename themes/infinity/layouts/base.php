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
