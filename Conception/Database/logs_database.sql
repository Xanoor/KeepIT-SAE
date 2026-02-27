-- Table logs
USE keepit;

DROP TABLE IF EXISTS device_logs;

CREATE TABLE IF NOT EXISTS device_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    log_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    login VARCHAR(36),
    serial_number VARCHAR(25) NOT NULL,
    table_name VARCHAR(25) NOT NULL,
    action_did VARCHAR(10) NOT NULL,
    field_updated VARCHAR(20) NOT NULL,
    old_val VARCHAR(50),
    new_val VARCHAR(50),
    INDEX idx_device_logs_date (log_date),
    INDEX idx_device_logs_serial (serial_number)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'LOGS TABLE ABOUT DEVICES, COMPUTERS AND MONITORS';


DELIMITER //

-- ==============================
-- Trigger pour la table devices
CREATE OR REPLACE TRIGGER devices_after_insert
AFTER INSERT ON devices
FOR EACH ROW
BEGIN
    INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated)
    VALUES (NOW(), @current_user, NEW.serial_number, 'devices', 'INSERT', NEW.device_type);
END
//

CREATE OR REPLACE TRIGGER devices_after_update
AFTER UPDATE ON devices
FOR EACH ROW
BEGIN
    IF NOT (OLD.model <=> NEW.model) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'devices', 'UPDATE', 'model', OLD.model, NEW.model);
    END IF;
    IF NOT (OLD.state <=> NEW.state) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'devices', 'UPDATE', 'state', OLD.state, NEW.state);
    END IF;
END
//

CREATE OR REPLACE TRIGGER devices_after_delete
    AFTER DELETE ON devices
    FOR EACH ROW
BEGIN
    INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated)
    VALUES (NOW(), @current_user, OLD.serial_number, 'devices', 'DELETE', OLD.device_type);
END
//


-- ==============================
-- Trigger pour la table computer
CREATE OR REPLACE TRIGGER computer_after_update
AFTER UPDATE ON computer
FOR EACH ROW
BEGIN
    IF NOT (OLD.name <=> NEW.name) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'name', CAST(OLD.name AS CHAR), CAST(NEW.name AS CHAR));
    END IF;

    IF NOT (OLD.location <=> NEW.location) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'location', CAST(OLD.location AS CHAR), CAST(NEW.location AS CHAR));
    END IF;

    IF NOT (OLD.building <=> NEW.building) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'building', CAST(OLD.building AS CHAR), CAST(NEW.building AS CHAR));
    END IF;

    IF NOT (OLD.room <=> NEW.room) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'room', CAST(OLD.room AS CHAR), CAST(NEW.room AS CHAR));
    END IF;

    IF NOT (OLD.cpu <=> NEW.cpu) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'cpu', CAST(OLD.cpu AS CHAR), CAST(NEW.cpu AS CHAR));
    END IF;

    IF NOT (OLD.ram_mb <=> NEW.ram_mb) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'ram_mb', CAST(OLD.ram_mb AS CHAR), CAST(NEW.ram_mb AS CHAR));
    END IF;

    IF NOT (OLD.disk_gb <=> NEW.disk_gb) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'disk_gb', CAST(OLD.disk_gb AS CHAR), CAST(NEW.disk_gb AS CHAR));
    END IF;
    IF NOT (OLD.domain <=> NEW.domain) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'domain', CAST(OLD.domain AS CHAR), CAST(NEW.domain AS CHAR));
    END IF;

    IF NOT (OLD.mac_address <=> NEW.mac_address) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'mac_address', CAST(OLD.mac_address AS CHAR), CAST(NEW.mac_address AS CHAR));
    END IF;

    IF NOT (OLD.purchase_date <=> NEW.purchase_date) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'purchase_date', CAST(OLD.purchase_date AS CHAR), CAST(NEW.purchase_date AS CHAR));
    END IF;

    IF NOT (OLD.warranty_end <=> NEW.warranty_end) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'warranty_end', CAST(OLD.warranty_end AS CHAR), CAST(NEW.warranty_end AS CHAR));
    END IF;

    IF NOT (OLD.manufacturer_name <=> NEW.manufacturer_name) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'manufacturer_name', CAST(OLD.manufacturer_name AS CHAR), CAST(NEW.manufacturer_name AS CHAR));
    END IF;

    IF NOT (OLD.os_name <=> NEW.os_name) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'os_name', CAST(OLD.os_name AS CHAR), CAST(NEW.os_name AS CHAR));
    END IF;

    IF NOT (OLD.type_name <=> NEW.type_name) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'computer', 'UPDATE', 'type_name', CAST(OLD.type_name AS CHAR), CAST(NEW.type_name AS CHAR));
    END IF;

END
//

-- ==============================
-- Trigger pour la table monitor
CREATE OR REPLACE TRIGGER monitor_after_update
AFTER UPDATE ON monitor
FOR EACH ROW
BEGIN

    IF NOT (OLD.size_inch <=> NEW.size_inch) THEN
            INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
            VALUES (NOW(), @current_user, OLD.serial_number, 'monitor', 'UPDATE', 'size_inch', CAST(OLD.size_inch AS CHAR), CAST(NEW.size_inch AS CHAR));
    END IF;

    IF NOT (OLD.resolution <=> NEW.resolution) THEN
        INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
        VALUES (NOW(), @current_user, OLD.serial_number, 'monitor', 'UPDATE', 'resolution', CAST(OLD.resolution AS CHAR), CAST(NEW.resolution AS CHAR));
    END IF;

        IF NOT (OLD.manufacturer_name <=> NEW.manufacturer_name) THEN
            INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
            VALUES (NOW(), @current_user, OLD.serial_number, 'monitor', 'UPDATE', 'manufacturer_name', CAST(OLD.manufacturer_name AS CHAR), CAST(NEW.manufacturer_name AS CHAR));
    END IF;

        IF NOT (OLD.connector_name <=> NEW.connector_name) THEN
            INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
            VALUES (NOW(), @current_user, OLD.serial_number, 'monitor', 'UPDATE', 'connector_name', CAST(OLD.connector_name AS CHAR), CAST(NEW.connector_name AS CHAR));
    END IF;

        IF NOT (OLD.attached_to_computer <=> NEW.attached_to_computer) THEN
            INSERT INTO device_logs (log_date, login, serial_number, table_name, action_did, field_updated, old_val, new_val)
            VALUES (NOW(), @current_user, OLD.serial_number, 'monitor', 'UPDATE', 'attached_to_computer', CAST(OLD.attached_to_computer AS CHAR), CAST(NEW.attached_to_computer AS CHAR));
    END IF;

END
//


CREATE TABLE IF NOT EXISTS constant_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    log_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    table_name VARCHAR(25) NOT NULL,
    action_did VARCHAR(10) NOT NULL,
    val VARCHAR(50) NOT NULL,
    INDEX idx_constant_logs_date (log_date),
    INDEX idx_constant_logs_table (table_name)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT = 'LOGS TABLE ABOUT FEATURES OF DEVICES'
//


-- ==============================
-- Trigger pour la table locations
CREATE OR REPLACE TRIGGER locations_after_insert
AFTER INSERT ON locations
FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
        VALUES (NOW(), 'locations', 'INSERT', CAST(NEW.location AS CHAR));

end //

CREATE OR REPLACE TRIGGER locations_after_delete
AFTER DELETE ON locations
FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
    VALUES (NOW(), 'locations', 'DELETE', CAST(OLD.location AS CHAR));

end //

-- ==============================
-- Trigger pour la table operating_system
CREATE OR REPLACE TRIGGER operating_system_after_insert
    AFTER INSERT ON operating_system
    FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
    VALUES (NOW(), 'operating_system', 'INSERT', CAST(NEW.name AS CHAR));

end //

CREATE OR REPLACE TRIGGER operating_system_after_delete
    AFTER DELETE ON operating_system
    FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
    VALUES (NOW(), 'operating_system', 'DELETE', CAST(OLD.name AS CHAR));

end //

-- ==============================
-- Trigger pour la table manufacturer
CREATE OR REPLACE TRIGGER manufacturer_after_insert
    AFTER INSERT ON manufacturer
    FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
    VALUES (NOW(), 'manufacturer', 'INSERT', CAST(NEW.name AS CHAR));

end //

CREATE OR REPLACE TRIGGER manufacturer_after_delete
    AFTER DELETE ON manufacturer
    FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
    VALUES (NOW(), 'manufacturer', 'DELETE', CAST(OLD.name AS CHAR));

end //

-- ==============================
-- Trigger pour la table connector
CREATE OR REPLACE TRIGGER connector_after_insert
    AFTER INSERT ON connector
    FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
    VALUES (NOW(), 'connector', 'INSERT', CAST(NEW.name AS CHAR));

end //

CREATE OR REPLACE TRIGGER connector_after_delete
    AFTER DELETE ON connector
    FOR EACH ROW
BEGIN

    INSERT INTO constant_logs (log_date, table_name, action_did, val)
    VALUES (NOW(), 'connector', 'DELETE', CAST(OLD.name AS CHAR));

end //

DELIMITER ;