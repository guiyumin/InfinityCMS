<?php
/**
 * Create comments table migration
 */

function up($db) {
    $db->execute("
        CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_id INTEGER NOT NULL,
            author_name VARCHAR(100),
            author_email VARCHAR(100),
            content TEXT,
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

function down($db) {
    $db->execute("DROP TABLE IF EXISTS comments");
}
