<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $theme->get('title', 'Setup - Infinity CMS') ?></title>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .setup-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 700px;
            overflow: hidden;
        }

        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .setup-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .setup-header p {
            opacity: 0.9;
        }

        .setup-progress {
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 2rem 2rem 1rem;
            background: #f8f9fa;
        }

        .progress-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .progress-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s;
        }

        .progress-step.active .progress-circle {
            background: #667eea;
            color: white;
        }

        .progress-step.completed .progress-circle {
            background: #28a745;
            color: white;
        }

        .progress-label {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .progress-step.active .progress-label {
            color: #667eea;
            font-weight: 600;
        }

        .setup-content {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .label-optional {
            font-weight: normal;
            color: #6c757d;
            font-size: 0.875rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="url"],
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
        }

        .error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .input-error {
            border-color: #dc3545;
        }

        .help-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .db-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .db-option {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .db-option:hover {
            border-color: #667eea;
        }

        .db-option.selected {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .db-option input[type="radio"] {
            display: none;
        }

        .db-option-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .db-option-desc {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .mysql-fields {
            display: none;
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .mysql-fields.show {
            display: block;
        }

        .setup-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .setup-footer {
            text-align: center;
            padding: 1.5rem;
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>

    <?php if ($theme->has('head')): ?>
        <?= $theme->raw('head') ?>
    <?php endif; ?>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1>Infinity CMS Setup</h1>
            <p>Let's get your website up and running</p>
        </div>

        <?= $theme->raw('content') ?>

        <div class="setup-footer">
            Infinity CMS &copy; <?= date('Y') ?>
        </div>
    </div>

    <?php if ($theme->has('scripts')): ?>
        <?= $theme->raw('scripts') ?>
    <?php endif; ?>
</body>
</html>
