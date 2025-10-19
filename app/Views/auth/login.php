<div class="login-container">
    <div class="login-header">
        <h1><?= config('app.name') ?></h1>
        <p>Sign in to your account</p>
    </div>

    <?php if (flash('error')): ?>
    <div class="alert alert-error">
        <?= e(flash('error')) ?>
    </div>
    <?php endif; ?>

    <?php if (flash('success')): ?>
    <div class="alert alert-success">
        <?= e(flash('success')) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('/login') ?>" class="login-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="username">Username or Email</label>
            <input
                type="text"
                id="username"
                name="username"
                value="<?= e(old('username')) ?>"
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

    </div>
</div>
