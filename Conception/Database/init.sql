-- DROP DATABASE IF EXISTS keepit;
-- CREATE DATABASE keepit;

USE keepit;

CREATE TABLE IF NOT EXISTS `users` (
    login VARCHAR(36) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    role ENUM('System Administrator', 'Web Administrator', 'Technician') NOT NULL DEFAULT 'Technician',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME NULL,
    INDEX idx_users_last_login_at (last_login_at)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'Table of user"s account';

CREATE TABLE IF NOT EXISTS `device_types` (
    name VARCHAR(25) PRIMARY KEY
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'List of device_types <=> tables who refer a device';

CREATE TABLE IF NOT EXISTS `device_states` (
    state VARCHAR(36) PRIMARY KEY,
    css_class VARCHAR(50) NOT NULL DEFAULT "table-item-DEFAULT"
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'Table of states that a device can be associated with css style';

CREATE TABLE IF NOT EXISTS `locations` (
    location VARCHAR(50) PRIMARY KEY
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'List of countries where is the company located';

CREATE TABLE IF NOT EXISTS operating_system (
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (name)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'List of operating system that can be assigned to a computer';

CREATE TABLE IF NOT EXISTS manufacturer (
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (name)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'List of all manufacturer of devices (computer, monitor, ...)';

CREATE TABLE IF NOT EXISTS connector (
    name VARCHAR(25) NOT NULL,
    PRIMARY KEY (name)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'List of connector type to link a screen and a monitor';

CREATE TABLE IF NOT EXISTS devices (
    serial_number VARCHAR(25),
    model VARCHAR(25),
    manufacturer_name VARCHAR(50) NOT NULL,
    device_type VARCHAR(25) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    state VARCHAR(36) NOT NULL DEFAULT 'En stock',
    PRIMARY KEY (serial_number),
    INDEX idx_devices_manufacturer_name (manufacturer_name),
    INDEX idx_devices_device_type (device_type),
    INDEX idx_devices_state (state),
    CONSTRAINT fk_computer_manufacturer_name
        FOREIGN KEY (manufacturer_name) REFERENCES manufacturer(name)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,
    CONSTRAINT fk_devices_device_type
        FOREIGN KEY (device_type) REFERENCES device_types(name)
            ON UPDATE RESTRICT
            ON DELETE RESTRICT,
    CONSTRAINT fk_devices_state
        FOREIGN KEY (state) REFERENCES device_states(state)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'table for recording a computer element, also is the commun base to define device';

CREATE TABLE IF NOT EXISTS computer (
    serial_number VARCHAR(25),
    name VARCHAR(25) UNIQUE NOT NULL,
    location VARCHAR(50) NOT NULL,
    building VARCHAR(50),
    room VARCHAR(25),
    cpu VARCHAR(25) NOT NULL,
    ram_mb INT NOT NULL CHECK(ram_mb >= 0),
    disk_gb INT NOT NULL CHECK(disk_gb >= 0),
    domain VARCHAR(50),
    mac_address VARCHAR(17) UNIQUE NOT NULL CHECK (mac_address REGEXP '^[0-9A-Fa-f]{2}(:[0-9A-Fa-f]{2}){5}$'),
    purchase_date DATE NOT NULL,
    warranty_end DATE CHECK (warranty_end >= purchase_date),
    os_name VARCHAR(50),
    type_name VARCHAR(25) NOT NULL, -- type of computer ex: laptop, desktop, mini-pc...
    PRIMARY KEY (serial_number),
    -- Quickly identify computers on a site + rename index of FK, UNIQUE
    INDEX idx_computer_name (name),
    INDEX idx_computer_location (location),
    INDEX idx_computer_mac_address (mac_address),
    INDEX idx_computer_os_name (os_name),
    CONSTRAINT fk_computer_serial_number
        FOREIGN KEY (serial_number) REFERENCES devices(serial_number)
            ON UPDATE RESTRICT
            ON DELETE CASCADE, -- When an element is deleted from the devices table, it is also deleted from the computer table.
    CONSTRAINT fk_computer_location
        FOREIGN KEY (location) REFERENCES locations(location)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,
    CONSTRAINT fk_computer_os_name
        FOREIGN KEY (os_name) REFERENCES operating_system(name)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'Computer information table';

CREATE TABLE IF NOT EXISTS monitor (
    serial_number VARCHAR(25),
    size_inch INT NOT NULL CHECK(size_inch > 0),
    resolution VARCHAR(25) NOT NULL,
    connector_name VARCHAR(25),
    attached_to_computer VARCHAR(25),
    PRIMARY KEY (serial_number),
    -- Quickly identify the screen(s) attached to a computer
    INDEX idx_monitor_connector_name (connector_name),
    INDEX idx_monitor_attached_to_computer (attached_to_computer),
    CONSTRAINT fk_monitor_serial_number
        FOREIGN KEY (serial_number) REFERENCES devices(serial_number)
            ON UPDATE RESTRICT
            ON DELETE CASCADE, -- When an element is deleted from the devices table, it is also deleted from the monitor table.
    CONSTRAINT fk_monitor_connector_name
        FOREIGN KEY (connector_name) REFERENCES connector(name)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,
    CONSTRAINT fk_monitor_attached_to_computer
        FOREIGN KEY (attached_to_computer) REFERENCES computer(name)
            ON UPDATE CASCADE
            ON DELETE SET NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'Monitor information table';

INSERT INTO `users` (login, password_hash, role) VALUES ('sysadmin', '$2y$10$H5DAxsFD0gSoVyYXPwLm3uXpRc.wLF5GIgtqjAQMnr0xFuDHasOmy', 'System Administrator');
INSERT INTO `users` (login, password_hash, role) VALUES ('adminweb', '$2y$10$zsJ2yxGntxq9tBad09LDJeBpvVQVDF5BWtqXezXwOjOyysdfYU6Gq', 'Web Administrator');
INSERT INTO `users` (login, password_hash, role) VALUES ('tech1', '$2y$10$3o1JyIoZLzxXDDL21O5FrObUzxSZB0wHX3P7MlZ3PLnjy0qUXdG.C', 'Technician');

-- DETAILLE DES ENGINES : https://dev.mysql.com/doc/refman/8.0/en/storage-engines.html