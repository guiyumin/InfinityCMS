<?php
$currentStep = $step ?? '1';
$errors = $errors ?? [];
$old = $old ?? [];
?>

<div class="setup-progress">
    <div class="progress-step <?= $currentStep == '1' ? 'active' : ($currentStep > '1' ? 'completed' : '') ?>">
        <div class="progress-circle">1</div>
        <span class="progress-label">Basic Info</span>
    </div>
    <div class="progress-step <?= $currentStep == '2' ? 'active' : ($currentStep > '2' ? 'completed' : '') ?>">
        <div class="progress-circle">2</div>
        <span class="progress-label">Database & Admin</span>
    </div>
</div>

<?php if ($currentStep == '1'): ?>
    <!-- Step 1: Basic Configuration -->
    <form method="POST" action="<?= url('/setup/process') ?>" class="setup-content">
        <input type="hidden" name="step" value="1">

        <h2 style="margin-bottom: 1.5rem;">Basic Configuration</h2>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="app_name">
                Application Name
            </label>
            <input
                type="text"
                id="app_name"
                name="app_name"
                value="<?= e($old['app_name'] ?? 'My Infinity Website') ?>"
                class="<?= isset($errors['app_name']) ? 'input-error' : '' ?>"
                placeholder="My Awesome Website"
                required>
            <?php if (isset($errors['app_name'])): ?>
                <div class="error"><?= e($errors['app_name']) ?></div>
            <?php endif; ?>
            <div class="help-text">This will be displayed as your site name</div>
        </div>

        <div class="form-group">
            <label for="app_url">
                Application URL
            </label>
            <input
                type="url"
                id="app_url"
                name="app_url"
                value="<?= e($old['app_url'] ?? 'http://localhost') ?>"
                class="<?= isset($errors['app_url']) ? 'input-error' : '' ?>"
                placeholder="https://mywebsite.com"
                required>
            <?php if (isset($errors['app_url'])): ?>
                <div class="error"><?= e($errors['app_url']) ?></div>
            <?php endif; ?>
            <div class="help-text">The full URL where your site will be accessible</div>
        </div>

        <div class="form-group">
            <label for="timezone">
                Timezone
            </label>
            <select id="timezone" name="timezone">
                <?php
                $timezones = [
                    'UTC' => 'UTC',
                    'America/New_York' => 'America/New York (EST)',
                    'America/Chicago' => 'America/Chicago (CST)',
                    'America/Denver' => 'America/Denver (MST)',
                    'America/Los_Angeles' => 'America/Los Angeles (PST)',
                    'Europe/London' => 'Europe/London (GMT)',
                    'Europe/Paris' => 'Europe/Paris (CET)',
                    'Asia/Tokyo' => 'Asia/Tokyo (JST)',
                    'Asia/Shanghai' => 'Asia/Shanghai (CST)',
                    'Asia/Singapore' => 'Asia/Singapore (SGT)',
                    'Australia/Sydney' => 'Australia/Sydney (AEDT)',
                ];
                $selectedTimezone = $old['timezone'] ?? 'UTC';
                ?>
                <?php foreach ($timezones as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selectedTimezone == $value ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="help-text">Select your timezone for correct date/time display</div>
        </div>

        <div class="form-group">
            <label for="theme">
                Theme
            </label>
            <select id="theme" name="theme">
                <?php
                $availableThemes = ['infinity' => 'Infinity (Default)'];
                $selectedTheme = $old['theme'] ?? 'infinity';
                ?>
                <?php foreach ($availableThemes as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $selectedTheme == $value ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="help-text">Choose your site's visual theme</div>
        </div>

        <div class="setup-actions">
            <div></div>
            <button type="submit" class="btn btn-primary">
                Next: Database & Admin →
            </button>
        </div>
    </form>

<?php elseif ($currentStep == '2'): ?>
    <!-- Step 2: Database & Admin Account -->
    <form method="POST" action="<?= url('/setup/process') ?>" class="setup-content" x-data="{ dbDriver: '<?= $old['db_driver'] ?? 'sqlite' ?>' }">
        <input type="hidden" name="step" value="2">

        <h2 style="margin-bottom: 1.5rem;">Database & Admin Account</h2>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <!-- Database Type Selection -->
        <div class="form-group">
            <label>Choose Database Type</label>
            <div class="db-options">
                <label class="db-option" :class="{ 'selected': dbDriver === 'sqlite' }">
                    <input type="radio" name="db_driver" value="sqlite" x-model="dbDriver" checked>
                    <div class="db-option-title">SQLite</div>
                    <div class="db-option-desc">Simple, file-based database (recommended for beginners)</div>
                </label>

                <label class="db-option" :class="{ 'selected': dbDriver === 'mysql' }">
                    <input type="radio" name="db_driver" value="mysql" x-model="dbDriver">
                    <div class="db-option-title">MySQL</div>
                    <div class="db-option-desc">Traditional database server (for advanced users)</div>
                </label>
            </div>
        </div>

        <!-- MySQL Configuration (only shown if MySQL is selected) -->
        <div class="mysql-fields" :class="{ 'show': dbDriver === 'mysql' }">
            <h3 style="margin-bottom: 1rem;">MySQL Configuration</h3>

            <div class="form-group">
                <label for="db_host">Database Host</label>
                <input
                    type="text"
                    id="db_host"
                    name="db_host"
                    value="<?= e($old['db_host'] ?? 'localhost') ?>"
                    placeholder="localhost">
            </div>

            <div class="form-group">
                <label for="db_port">Database Port</label>
                <input
                    type="text"
                    id="db_port"
                    name="db_port"
                    value="<?= e($old['db_port'] ?? '3306') ?>"
                    placeholder="3306">
            </div>

            <div class="form-group">
                <label for="db_name">Database Name</label>
                <input
                    type="text"
                    id="db_name"
                    name="db_name"
                    value="<?= e($old['db_name'] ?? '') ?>"
                    placeholder="infinity_cms">
            </div>

            <div class="form-group">
                <label for="db_user">Database Username</label>
                <input
                    type="text"
                    id="db_user"
                    name="db_user"
                    value="<?= e($old['db_user'] ?? '') ?>"
                    placeholder="root">
            </div>

            <div class="form-group">
                <label for="db_pass">Database Password</label>
                <input
                    type="password"
                    id="db_pass"
                    name="db_pass"
                    placeholder="Enter database password">
            </div>
        </div>

        <hr style="margin: 2rem 0; border: none; border-top: 1px solid #e9ecef;">

        <!-- Admin Account -->
        <h3 style="margin-bottom: 1rem;">Create Admin Account</h3>

        <div class="form-group">
            <label for="admin_username">Username</label>
            <input
                type="text"
                id="admin_username"
                name="admin_username"
                value="<?= e($old['admin_username'] ?? '') ?>"
                class="<?= isset($errors['admin_username']) ? 'input-error' : '' ?>"
                placeholder="admin"
                required>
            <?php if (isset($errors['admin_username'])): ?>
                <div class="error"><?= e($errors['admin_username']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="admin_email">Email Address</label>
            <input
                type="email"
                id="admin_email"
                name="admin_email"
                value="<?= e($old['admin_email'] ?? '') ?>"
                class="<?= isset($errors['admin_email']) ? 'input-error' : '' ?>"
                placeholder="admin@example.com"
                required>
            <?php if (isset($errors['admin_email'])): ?>
                <div class="error"><?= e($errors['admin_email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="admin_password">Password</label>
            <input
                type="password"
                id="admin_password"
                name="admin_password"
                class="<?= isset($errors['admin_password']) ? 'input-error' : '' ?>"
                placeholder="At least 8 characters"
                required>
            <?php if (isset($errors['admin_password'])): ?>
                <div class="error"><?= e($errors['admin_password']) ?></div>
            <?php endif; ?>
            <div class="help-text">Use a strong password with at least 8 characters</div>
        </div>

        <div class="form-group">
            <label for="admin_password_confirm">Confirm Password</label>
            <input
                type="password"
                id="admin_password_confirm"
                name="admin_password_confirm"
                class="<?= isset($errors['admin_password_confirm']) ? 'input-error' : '' ?>"
                placeholder="Re-enter your password"
                required>
            <?php if (isset($errors['admin_password_confirm'])): ?>
                <div class="error"><?= e($errors['admin_password_confirm']) ?></div>
            <?php endif; ?>
        </div>

        <div class="alert alert-info">
            <strong>Almost done!</strong> After clicking "Complete Setup", we'll create your configuration file,
            set up the database, and publish your theme assets automatically.
        </div>

        <div class="setup-actions">
            <a href="<?= url('/setup?step=1') ?>" class="btn btn-secondary">
                ← Back
            </a>
            <button type="submit" class="btn btn-primary">
                Complete Setup
            </button>
        </div>
    </form>

<?php endif; ?>
