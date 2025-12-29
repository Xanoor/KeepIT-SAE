CREATE TABLE IF NOT EXISTS `users` (
    login VARCHAR(36) NOT NULL PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    role ENUM('System Administrator', 'Web Administrator', 'Technician') NOT NULL DEFAULT 'Technician',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS `device_types` (
    name ENUM('Computer', 'Monitor') NOT NULL PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS `device_states` (
    state VARCHAR(36) NOT NULL PRIMARY KEY,
    css_class VARCHAR(50) NOT NULL DEFAULT ""
);

INSERT IGNORE INTO `device_states` (state, css_class) VALUES ('Déployé', 'table-item-DEPLOYED'), ('En stock', 'table-item-IN_INVENTORY'), ('Fin de vie', 'table-item-END_OF_LIFE');

CREATE TABLE IF NOT EXISTS `locations` (
    location VARCHAR(50) NOT NULL PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS operating_system (
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (name)
);

CREATE TABLE IF NOT EXISTS manufacturer (
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (name)
);

CREATE TABLE IF NOT EXISTS connector (
    name VARCHAR(25) NOT NULL,
    PRIMARY KEY (name)
);

CREATE TABLE IF NOT EXISTS devices (
    serial_number VARCHAR(25) NOT NULL,
    device_type ENUM('Computer', 'Monitor'), 
    model VARCHAR(25),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    state VARCHAR(36) NULL DEFAULT 'En stock',
    PRIMARY KEY (serial_number),
    FOREIGN KEY (device_type) REFERENCES device_types(name) ON DELETE RESTRICT, 
    FOREIGN KEY (state) REFERENCES device_states(state) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS computer (
    serial_number VARCHAR(25) NOT NULL,
    name VARCHAR(25),
    location VARCHAR(50),
    building VARCHAR(50),
    room VARCHAR(25),
    cpu VARCHAR(25),
    ram_mb INT CHECK(ram_mb > 0),
    disk_gb INT CHECK(disk_gb > 0),
    domain VARCHAR(50),
    mac_address VARCHAR(17) CHECK (mac_address REGEXP '^[0-9A-Fa-f]{2}(:[0-9A-Fa-f]{2}){5}$'),
    purchase_date DATE,
    warranty_end DATE,
    manufacturer_name VARCHAR(50),
    os_name VARCHAR(50),
    type_name VARCHAR(25),
    PRIMARY KEY (serial_number),
    FOREIGN KEY (serial_number) REFERENCES devices(serial_number) ON DELETE CASCADE, -- When an element is deleted from the devices table, it is also deleted from the computer table.
    FOREIGN KEY (manufacturer_name) REFERENCES manufacturer(name) ON DELETE RESTRICT,
    FOREIGN KEY (os_name) REFERENCES operating_system(name) ON DELETE RESTRICT,
    FOREIGN KEY (location) REFERENCES locations(location) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS monitor (
    serial_number VARCHAR(25) NOT NULL,
    size_inch INT CHECK(size_inch > 0),
    resolution VARCHAR(25),
    manufacturer_name VARCHAR(50),
    connector_name VARCHAR(25),
    attached_to_serial VARCHAR(25),
    PRIMARY KEY (serial_number),
    FOREIGN KEY (serial_number) REFERENCES devices(serial_number) ON DELETE CASCADE, -- When an element is deleted from the devices table, it is also deleted from the monitor table.
    FOREIGN KEY (manufacturer_name) REFERENCES manufacturer(name) ON DELETE RESTRICT,
    FOREIGN KEY (connector_name) REFERENCES connector(name) ON DELETE RESTRICT,
    FOREIGN KEY (attached_to_serial) REFERENCES computer(serial_number) ON DELETE SET NULL
);

INSERT IGNORE INTO `users` (login, password_hash, role) VALUES ('sysadmin', '$2y$10$H5DAxsFD0gSoVyYXPwLm3uXpRc.wLF5GIgtqjAQMnr0xFuDHasOmy', 'System Administrator');
INSERT IGNORE INTO `users` (login, password_hash, role) VALUES ('adminweb', '$2y$10$zsJ2yxGntxq9tBad09LDJeBpvVQVDF5BWtqXezXwOjOyysdfYU6Gq', 'Web Administrator');
INSERT IGNORE INTO `users` (login, password_hash, role) VALUES ('tech1', '$2y$10$3o1JyIoZLzxXDDL21O5FrObUzxSZB0wHX3P7MlZ3PLnjy0qUXdG.C', 'Technician');