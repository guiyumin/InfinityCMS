<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-column">
                <h3><?= config('app.name') ?></h3>
                <p>A modern CMS built with PHP, HTMX, and Alpine.js</p>
            </div>

            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?= url('/') ?>">Home</a></li>
                    <li><a href="<?= url('/about') ?>">About</a></li>
                    <li><a href="<?= url('/posts') ?>">Posts</a></li>
                    <li><a href="<?= url('/contact') ?>">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Follow Us</h4>
                <div class="social-links">
                    <!-- Add social media links here -->
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= config('app.name') ?>. All rights reserved.</p>
        </div>
    </div>
</footer>
