<?php
/**
 * Generic page template
 * Used for rendering CMS pages from the database
 */

// Page data is passed from the controller
$pageContent = $content ?? '';
$pageTitle = $title ?? 'Page';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8"><?= e($pageTitle) ?></h1>

        <div class="prose max-w-none">
            <?= $pageContent ?>
        </div>
    </div>
</div>