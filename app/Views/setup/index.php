<?php
// Ensure step is a string for proper comparison
// Default to '1' if not set or invalid
$currentStep = isset($step) ? (string)$step : '1';
if (!in_array($currentStep, ['1', '2', '3'])) {
    $currentStep = '1';
}
$errors = $errors ?? [];
$old = $old ?? [];
?>

<div class="setup-progress">
    <div class="progress-step <?= $currentStep === '1' ? 'active' : ($currentStep > '1' ? 'completed' : '') ?>">
        <div class="progress-circle">1</div>
        <span class="progress-label">Database Setup</span>
    </div>
    <div class="progress-step <?= $currentStep === '2' ? 'active' : ($currentStep > '2' ? 'completed' : '') ?>">
        <div class="progress-circle">2</div>
        <span class="progress-label">Admin Account</span>
    </div>
    <div class="progress-step <?= $currentStep === '3' ? 'active' : ($currentStep > '3' ? 'completed' : '') ?>">
        <div class="progress-circle">3</div>
        <span class="progress-label">Site Settings</span>
    </div>
</div>

<?php if ($currentStep === '1'): ?>
    <!-- Step 1: Database Setup -->
    <form method="POST" action="<?= url('/setup/process') ?>" class="setup-content">
        <input type="hidden" name="step" value="1">
        <input type="hidden" name="db_driver" value="mysql">

        <h2 style="margin-bottom: 1.5rem;">Database Configuration</h2>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['migration_errors']) && !$_SESSION['migration_errors']['success']): ?>
            <div class="alert alert-danger">
                <h4>Database Migration Details</h4>
                <p><strong><?= $_SESSION['migration_errors']['successCount'] ?> succeeded, <?= $_SESSION['migration_errors']['failureCount'] ?> failed</strong></p>
                <div class="migration-details">
                    <?php foreach ($_SESSION['migration_errors']['results'] as $result): ?>
                        <p class="<?= strpos($result, '✗') !== false ? 'text-danger' : 'text-success' ?>">
                            <?= e($result) ?>
                        </p>
                    <?php endforeach; ?>
                </div>
                <p class="alert-help">Please fix these issues:</p>
                <ul class="alert-help">
                    <li>Check database user permissions (CREATE TABLE required)</li>
                    <li>Verify database exists and is accessible</li>
                    <li>Ensure no conflicting tables exist</li>
                    <li>Check MySQL/MariaDB version compatibility</li>
                </ul>
            </div>
        <?php endif; ?>

        <!-- MySQL Configuration -->
        <h3 style="margin-bottom: 1rem;">MySQL Database Configuration</h3>

        <div class="form-group">
            <label for="db_host">Database Host</label>
            <input
                type="text"
                id="db_host"
                name="db_host"
                value="<?= e($old['db_host'] ?? 'localhost') ?>"
                class="<?= isset($errors['db_host']) ? 'input-error' : '' ?>"
                placeholder="localhost"
                required>
            <?php if (isset($errors['db_host'])): ?>
                <div class="error"><?= e($errors['db_host']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="db_port">Database Port</label>
            <input
                type="text"
                id="db_port"
                name="db_port"
                value="<?= e($old['db_port'] ?? '3306') ?>"
                class="<?= isset($errors['db_port']) ? 'input-error' : '' ?>"
                placeholder="3306">
            <?php if (isset($errors['db_port'])): ?>
                <div class="error"><?= e($errors['db_port']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="db_name">Database Name</label>
            <input
                type="text"
                id="db_name"
                name="db_name"
                value="<?= e($old['db_name'] ?? '') ?>"
                class="<?= isset($errors['db_name']) ? 'input-error' : '' ?>"
                placeholder="infinity_cms"
                required>
            <?php if (isset($errors['db_name'])): ?>
                <div class="error"><?= e($errors['db_name']) ?></div>
            <?php endif; ?>
            <div class="help-text">The database must already exist on your MySQL server</div>
        </div>

        <div class="form-group">
            <label for="db_user">Database Username</label>
            <input
                type="text"
                id="db_user"
                name="db_user"
                value="<?= e($old['db_user'] ?? '') ?>"
                class="<?= isset($errors['db_user']) ? 'input-error' : '' ?>"
                placeholder="Enter database username"
                required>
            <?php if (isset($errors['db_user'])): ?>
                <div class="error"><?= e($errors['db_user']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="db_pass">Database Password</label>
            <input
                type="password"
                id="db_pass"
                name="db_pass"
                class="<?= isset($errors['db_pass']) ? 'input-error' : '' ?>"
                placeholder="Enter database password">
            <?php if (isset($errors['db_pass'])): ?>
                <div class="error"><?= e($errors['db_pass']) ?></div>
            <?php endif; ?>
            <div class="help-text">Leave blank if your database has no password</div>
        </div>

        <div class="setup-actions">
            <div></div>
            <button type="submit" class="btn btn-primary">
                Test Connection & Run Migrations →
            </button>
        </div>
    </form>

<?php elseif ($currentStep === '2'): ?>
    <!-- Step 2: Admin Account -->
    <form method="POST" action="<?= url('/setup/process') ?>" class="setup-content">
        <input type="hidden" name="step" value="2">

        <h2 style="margin-bottom: 1.5rem;">Admin Account Setup</h2>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <p style="margin-bottom: 1.5rem;">Choose how you want to set up the administrator account for your CMS.</p>

        <div class="form-group" x-data="{ adminOption: '<?= e($old['admin_option'] ?? 'custom') ?>' }">
            <div style="margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 0.5rem;">
                    <input
                        type="radio"
                        name="admin_option"
                        value="custom"
                        x-model="adminOption"
                        style="margin-right: 0.5rem;">
                    <span>Create a new custom admin account</span>
                </label>

                <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 0.5rem;">
                    <input
                        type="radio"
                        name="admin_option"
                        value="default"
                        x-model="adminOption"
                        style="margin-right: 0.5rem;">
                    <span>Use default admin account (username: admin, password: admin123)</span>
                </label>

                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input
                        type="radio"
                        name="admin_option"
                        value="skip"
                        x-model="adminOption"
                        style="margin-right: 0.5rem;">
                    <span>Skip - I already have an admin account in the database</span>
                </label>
            </div>

            <div x-show="adminOption === 'default'" x-transition style="margin-top: 1rem;">
                <div class="alert alert-info">
                    <strong>Important:</strong> You should change these default credentials immediately after logging in for security reasons.
                </div>
            </div>

            <div x-show="adminOption === 'skip'" x-transition style="margin-top: 1rem;">
                <div class="alert alert-info">
                    Choose this option if you're reconfiguring the application and your database already contains admin users from a previous installation.
                </div>
            </div>

            <div x-show="adminOption === 'custom'" x-transition style="margin-top: 1.5rem;">
                <div class="form-group">
                    <label for="admin_username">Username</label>
                    <input
                        type="text"
                        id="admin_username"
                        name="admin_username"
                        value="<?= e($old['admin_username'] ?? '') ?>"
                        class="<?= isset($errors['admin_username']) ? 'input-error' : '' ?>"
                        placeholder="admin"
                        x-bind:required="adminOption === 'custom'">
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
                        x-bind:required="adminOption === 'custom'">
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
                        x-bind:required="adminOption === 'custom'">
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
                        x-bind:required="adminOption === 'custom'">
                    <?php if (isset($errors['admin_password_confirm'])): ?>
                        <div class="error"><?= e($errors['admin_password_confirm']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="setup-actions">
            <a href="<?= url('/setup?step=1') ?>" class="btn btn-secondary">
                ← Back
            </a>
            <button type="submit" class="btn btn-primary">
                Next: Site Settings →
            </button>
        </div>
    </form>

<?php elseif ($currentStep === '3'): ?>
    <!-- Step 3: Site Settings -->
    <form method="POST" action="<?= url('/setup/process') ?>" class="setup-content">
        <input type="hidden" name="step" value="3">

        <h2 style="margin-bottom: 1.5rem;">Site Settings</h2>

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

        <div class="alert alert-info">
            <strong>Final step!</strong> After clicking "Complete Setup", we'll finalize your configuration and you'll be ready to start using your CMS.
        </div>

        <div class="setup-actions">
            <a href="<?= url('/setup?step=2') ?>" class="btn btn-secondary">
                ← Back
            </a>
            <button type="submit" class="btn btn-primary">
                Complete Setup ✓
            </button>
        </div>
    </form>

<?php endif; ?>
