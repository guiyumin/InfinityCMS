<?php
/**
 * Create comments table migration
 */

return [
    'up' => function($db) {
        $db->execute("
            CREATE TABLE IF NOT EXISTS comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                author_name VARCHAR(100),
                author_email VARCHAR(100),
                content TEXT,
                status VARCHAR(20) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_post_id (post_id),
                INDEX idx_status (status),
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },

    'down' => function($db) {
        $db->execute("DROP TABLE IF EXISTS comments");
    }
];
