<?php
// About page - uses content from CMS or shows default
$pageContent = $content ?? '';
$pageTitle = $title ?? 'About Us';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8"><?= e($pageTitle) ?></h1>

        <div class="prose max-w-none">
            <?php if (!empty($pageContent)): ?>
                <?= $pageContent ?>
            <?php else: ?>
                <!-- Default content if page doesn't exist in CMS yet -->
                <p class="text-gray-500 italic">
                    This page content can be managed through the CMS admin panel.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>