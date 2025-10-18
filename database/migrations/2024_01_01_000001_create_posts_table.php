<?php
/**
 * Create posts table migration
 */

function up($db) {
    $db->execute("
        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            content TEXT,
            excerpt TEXT,
            featured_image VARCHAR(255),
            author VARCHAR(100),
            status VARCHAR(20) DEFAULT 'draft',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

function down($db) {
    $db->execute("DROP TABLE IF EXISTS posts");
}
