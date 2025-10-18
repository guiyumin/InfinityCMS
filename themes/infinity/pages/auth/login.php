<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1><?= $theme->config('app.name') ?></h1>
            <p>Sign in to your account</p>
        </div>

        <?php if ($theme->has('_flash') && flash('error')): ?>
        <div class="alert alert-error">
            <?= $theme->e(flash('error')) ?>
        </div>
        <?php endif; ?>

        <?php if ($theme->has('_flash') && flash('success')): ?>
        <div class="alert alert-success">
            <?= $theme->e(flash('success')) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= $theme->url('/login') ?>" class="login-form">
            <?= $theme->csrfField() ?>

            <div class="form-group">
                <label for="username">Username or Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= $theme->e(old('username')) ?>"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your username or email"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Sign In
            </button>
        </form>

        <div class="login-footer">
            <p class="help-text">
                <strong>Default Credentials:</strong><br>
                Username: <code>admin</code><br>
                Password: <code>admin123</code>
            </p>
            <p class="help-text">
                <small>Change these credentials immediately after first login!</small>
            </p>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
}

.login-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    padding: 3rem;
    width: 100%;
    max-width: 420px;
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.login-header h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    color: #333;
}

.login-header p {
    margin: 0;
    color: #666;
    font-size: 0.95rem;
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.alert-error {
    background: #fee;
    color: #c00;
    border: 1px solid #fcc;
}

.alert-success {
    background: #efe;
    color: #060;
    border: 1px solid #cfc;
}

.login-form {
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 600;
    font-size: 0.9rem;
}

.form-group input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-block {
    width: 100%;
    display: block;
}

.login-footer {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e0e0e0;
}

.help-text {
    margin: 0.5rem 0;
    font-size: 0.85rem;
    color: #666;
}

.help-text code {
    background: #f5f5f5;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    color: #667eea;
}

.help-text small {
    color: #999;
}
</style>
