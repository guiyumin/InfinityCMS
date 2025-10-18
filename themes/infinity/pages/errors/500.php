<div class="error-container">
    <div class="error-content">
        <div class="error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">Internal Server Error</h1>
        <p class="error-message">
            Something went wrong on our end. We're working to fix it.
        </p>

        <?php if ($theme->has('error_details') && config('app.debug')): ?>
        <details class="error-details">
            <summary>Technical Details (Debug Mode)</summary>
            <pre><?= $theme->e($theme->get('error_details', '', false)) ?></pre>
        </details>
        <?php endif; ?>

        <div class="error-actions">
            <a href="<?= $theme->url('/') ?>" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Go Home
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Try Again
            </a>
        </div>
    </div>
</div>

<style>
body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.error-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    padding: 2rem;
}

.error-content {
    text-align: center;
    color: white;
    max-width: 600px;
}

.error-icon {
    margin-bottom: 1rem;
}

.error-icon svg {
    filter: drop-shadow(4px 4px 0 rgba(0, 0, 0, 0.2));
}

.error-code {
    font-size: 6rem;
    font-weight: 900;
    line-height: 1;
    margin-bottom: 1rem;
    text-shadow: 4px 4px 0 rgba(0, 0, 0, 0.2);
}

.error-title {
    font-size: 2.5rem;
    margin: 0 0 1rem 0;
    font-weight: 700;
}

.error-message {
    font-size: 1.25rem;
    margin: 0 0 2rem 0;
    opacity: 0.9;
}

.error-details {
    background: rgba(0, 0, 0, 0.2);
    padding: 1rem;
    border-radius: 8px;
    margin: 2rem 0;
    text-align: left;
}

.error-details summary {
    cursor: pointer;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.error-details pre {
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
    font-size: 0.85rem;
    opacity: 0.9;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: white;
    color: #f5576c;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid white;
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}
</style>
