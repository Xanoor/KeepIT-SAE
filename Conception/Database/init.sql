CREATE TABLE IF NOT EXISTS `users` (
    uid VARCHAR(9) NOT NULL PRIMARY KEY,           -- ex: uix123456
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,

    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS `device_types` (
    name VARCHAR(36) NOT NULL PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS `device_states` (
    state VARCHAR(36) NOT NULL PRIMARY KEY
);

INSERT INTO `device_states` (state) VALUES ('In inventory'), ('In repair'), ('Decommissioned');

CREATE TABLE IF NOT EXISTS `locations` (
    location VARCHAR(50) NOT NULL PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS `devices` (
    serial_number INT NOT NULL PRIMARY KEY,

    name VARCHAR(255) NOT NULL,
    device_type VARCHAR(36) NOT NULL,
    user_uid VARCHAR(9) NULL,
    mac_address VARCHAR(17) NULL,  -- Format XX:XX:XX:XX:XX:XX
    state VARCHAR(36) NULL DEFAULT 'In inventory',
    location VARCHAR(50) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (device_type) REFERENCES device_types(name) ON DELETE RESTRICT,
    FOREIGN KEY (user_uid) REFERENCES users(uid) ON DELETE SET NULL,
    FOREIGN KEY (state) REFERENCES device_states(state) ON DELETE RESTRICT,
    FOREIGN KEY (location) REFERENCES locations(location) ON DELETE RESTRICT
);