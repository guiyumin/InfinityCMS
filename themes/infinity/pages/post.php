<article class="post-single">
    <div class="container">
        <header class="post-header">
            <h1><?= e($post['title'] ?? 'Untitled') ?></h1>

            <div class="post-meta">
                <span class="author">By <?= e($post['author'] ?? 'Unknown') ?></span>
                <span class="date"><?= date('F j, Y', strtotime($post['created_at'] ?? 'now')) ?></span>
            </div>
        </header>

        <?php if (!empty($post['featured_image'])): ?>
            <div class="post-featured-image">
                <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>">
            </div>
        <?php endif; ?>

        <div class="post-content prose">
            <?= $post['content_html'] ?? $post['content'] ?? '' ?>
        </div>

        <footer class="post-footer">
            <?php if (!empty($post['tags'])): ?>
                <div class="post-tags">
                    <?php foreach ($post['tags'] as $tag): ?>
                        <a href="<?= url('/tag/' . $tag) ?>" class="tag"><?= e($tag) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </footer>

        <!-- Comments section (Alpine.js example) -->
        <div class="comments-section" x-data="{ showCommentForm: false }">
            <h3>Comments</h3>

            <button @click="showCommentForm = !showCommentForm" class="btn">
                <span x-text="showCommentForm ? 'Cancel' : 'Add Comment'"></span>
            </button>

            <div x-show="showCommentForm" x-cloak>
                <form
                    hx-post="<?= url('/api/comments') ?>"
                    hx-target="#comments-list"
                    hx-swap="afterbegin">
                    <?= csrf_field() ?>
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?? '' ?>">

                    <textarea name="content" placeholder="Your comment..." required></textarea>
                    <button type="submit" class="btn btn-primary">Submit Comment</button>
                </form>
            </div>

            <div id="comments-list"
                 hx-get="<?= url('/api/comments?post_id=' . ($post['id'] ?? '')) ?>"
                 hx-trigger="load">
                Loading comments...
            </div>
        </div>
    </div>
</article>
