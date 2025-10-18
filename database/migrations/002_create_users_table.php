<?php
/**
 * Create users table migration
 */

function up($db) {
    $db->execute("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Insert default admin user (password: admin123)
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $db->execute("
        INSERT INTO users (username, email, password, role)
        VALUES ('admin', 'admin@example.com', '{$password}', 'admin')
    ");
}

function down($db) {
    $db->execute("DROP TABLE IF EXISTS users");
}
