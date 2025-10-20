<?php
/**
 * Create settings table migration
 * Stores admin custom settings and configurations
 */

return [
    'up' => function($db) {
        $db->execute("
            CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                description VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_setting_key (setting_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Insert default settings
        $db->execute("
            INSERT INTO settings (setting_key, setting_value, description) VALUES
            ('posts_per_page', '10', 'Number of posts to display per page'),
            ('allow_comments', '1', 'Allow comments on posts (1 = yes, 0 = no)'),
            ('maintenance_mode', '0', 'Enable maintenance mode (1 = yes, 0 = no)')
        ");
    },

    'down' => function($db) {
        $db->execute("DROP TABLE IF EXISTS settings");
    }
];
