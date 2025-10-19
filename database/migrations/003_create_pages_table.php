<?php
/**
 * Create pages table migration
 */

return [
    'up' => function($db) {
        $db->execute("
            CREATE TABLE IF NOT EXISTS pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content TEXT,
                template VARCHAR(50) DEFAULT 'default',
                status VARCHAR(20) DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },

    'down' => function($db) {
        $db->execute("DROP TABLE IF EXISTS pages");
    }
];
