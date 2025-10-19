<?php
// Get old input and errors from session
$old = $_SESSION['_old'] ?? [];
$errors = $_SESSION['contact_errors'] ?? [];
$success = flash('success');

// Clear session data
unset($_SESSION['_old']);
unset($_SESSION['contact_errors']);

// Page content from CMS (if exists)
$pageContent = $content ?? '';
$pageTitle = $title ?? 'Contact Us';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8"><?= e($pageTitle) ?></h1>

        <?php if (!empty($pageContent)): ?>
            <div class="prose max-w-none mb-8">
                <?= $pageContent ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/contact') ?>" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       value="<?= e($old['name'] ?? '') ?>"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       id="email"
                       value="<?= e($old['email'] ?? '') ?>"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                    Subject
                </label>
                <input type="text"
                       name="subject"
                       id="subject"
                       value="<?= e($old['subject'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea name="message"
                          id="message"
                          rows="6"
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($old['message'] ?? '') ?></textarea>
            </div>

            <div>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Send Message
                </button>
            </div>
        </form>

        <div class="mt-12 pt-8 border-t border-gray-200">
            <h2 class="text-2xl font-semibold mb-4">Other Ways to Reach Us</h2>

            <div class="space-y-4 text-gray-600">
                <div>
                    <h3 class="font-medium text-gray-900">Email</h3>
                    <p>info@example.com</p>
                </div>

                <div>
                    <h3 class="font-medium text-gray-900">Phone</h3>
                    <p>+1 (555) 123-4567</p>
                </div>

                <div>
                    <h3 class="font-medium text-gray-900">Address</h3>
                    <p>123 Main Street<br>
                       Anytown, ST 12345</p>
                </div>
            </div>
        </div>
    </div>
</div>