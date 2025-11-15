CREATE TABLE IF NOT EXISTS `users` (
    uid VARCHAR(255) NOT NULL PRIMARY KEY,           -- ex: uix123456
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,

    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME NULL
);
