<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? config('app.name') ?></title>

    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?= theme_asset('css/style.css') ?>">

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <?php if (isset($head)): ?>
        <?= $head ?>
    <?php endif; ?>
</head>
<body>
    <?php app('view')->partial('header', get_defined_vars()); ?>

    <main class="main-content">
        <?= $content ?>
    </main>

    <?php app('view')->partial('footer', get_defined_vars()); ?>

    <!-- Theme JS -->
    <script src="<?= theme_asset('js/main.js') ?>"></script>

    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>
</body>
</html>
