<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8">Posts</h1>

    <?php if (empty($posts)): ?>
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">No posts yet. Check back soon!</p>
        </div>
    <?php else: ?>
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($posts as $post): ?>
                <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <?php if (!empty($post['featured_image'])): ?>
                        <img src="<?= $post['featured_image'] ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-48 object-cover">
                    <?php endif; ?>

                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-2">
                            <a href="<?= url('/post/' . $post['slug']) ?>" class="text-gray-900 hover:text-blue-600 transition-colors">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h2>

                        <div class="text-gray-600 text-sm mb-4">
                            <span><?= htmlspecialchars($post['author']) ?></span>
                            <span class="mx-2">•</span>
                            <time datetime="<?= $post['created_at'] ?>"><?= date('M j, Y', strtotime($post['created_at'])) ?></time>
                        </div>

                        <div class="prose prose-sm text-gray-700 mb-4">
                            <?php if (isset($post['excerpt_html'])): ?>
                                <?= $post['excerpt_html'] ?>
                            <?php elseif (!empty($post['excerpt'])): ?>
                                <p><?= htmlspecialchars($post['excerpt']) ?></p>
                            <?php else: ?>
                                <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                            <?php endif; ?>
                        </div>

                        <a href="<?= url('/post/' . $post['slug']) ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                            Read More
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.prose {
    max-width: 65ch;
}

.prose p {
    margin: 0.5rem 0;
}

.prose h1, .prose h2, .prose h3 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.prose strong {
    font-weight: 600;
}

.prose em {
    font-style: italic;
}

.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose code {
    background: #f3f4f6;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.prose pre {
    background: #1f2937;
    color: #f3f4f6;
    padding: 1rem;
    border-radius: 0.375rem;
    overflow-x: auto;
    margin: 1rem 0;
}

.prose pre code {
    background: transparent;
    padding: 0;
}

.prose ul, .prose ol {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
}

.prose li {
    margin: 0.25rem 0;
}

.prose blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1rem;
    margin: 1rem 0;
    color: #6b7280;
}

.prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    margin: 1rem 0;
}

.prose hr {
    border: 0;
    border-top: 1px solid #e5e7eb;
    margin: 1.5rem 0;
}
</style>