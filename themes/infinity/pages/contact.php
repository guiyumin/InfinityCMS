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

<div class="container">
    <div class="contact-page">
        <div class="contact-header">
            <h1><?= e($pageTitle) ?></h1>

            <?php if (!empty($pageContent)): ?>
                <div class="page-content">
                    <?= $pageContent ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="contact-form">
            <form method="POST" action="<?= url('/contact') ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name" class="form-label">
                        Name <span class="required">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="<?= e($old['name'] ?? '') ?>"
                           required
                           class="form-input">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="<?= e($old['email'] ?? '') ?>"
                           required
                           class="form-input">
                </div>

                <div class="form-group">
                    <label for="subject" class="form-label">
                        Subject
                    </label>
                    <input type="text"
                           name="subject"
                           id="subject"
                           value="<?= e($old['subject'] ?? '') ?>"
                           class="form-input">
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">
                        Message <span class="required">*</span>
                    </label>
                    <textarea name="message"
                              id="message"
                              rows="6"
                              required
                              class="form-textarea"><?= e($old['message'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        Send Message
                    </button>
                </div>
            </form>
        </div>

        <div class="contact-info">
            <h2>Other Ways to Reach Us</h2>

            <div class="contact-info-list">
                <div class="contact-info-item">
                    <h3>Email</h3>
                    <p>info@example.com</p>
                </div>

                <div class="contact-info-item">
                    <h3>Phone</h3>
                    <p>+1 (555) 123-4567</p>
                </div>

                <div class="contact-info-item">
                    <h3>Address</h3>
                    <p>123 Main Street<br>
                       Anytown, ST 12345</p>
                </div>
            </div>
        </div>
    </div>
</div>