-- Table logs
CREATE TABLE IF NOT EXISTS device_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    serial_number VARCHAR(25) NOT NULL,
    table_name VARCHAR(25) NOT NULL,
    action_did VARCHAR(10) NOT NULL,
    fields_updated INT NOT NULL,
    INDEX idx_serial (serial_number),
    INDEX idx_date (log_date)
);

-- Trigger pour la table devices
DELIMITER //

CREATE TRIGGER devices_after_insert
AFTER INSERT ON devices
FOR EACH ROW
BEGIN
    INSERT INTO device_logs (log_date, serial_number, table_name, action_did, fields_updated)
    VALUES (NOW(), NEW.serial_number, 'devices', 'INSERT', 9);
END//

CREATE TRIGGER devices_after_update
AFTER UPDATE ON devices
FOR EACH ROW
BEGIN
    DECLARE nb_changes INT DEFAULT 0;
    
    IF OLD.name != NEW.name THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.location != NEW.location THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.building != NEW.building THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.room != NEW.room THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.device_type != NEW.device_type THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.state != NEW.state THEN SET nb_changes = nb_changes + 1; END IF;
    
    INSERT INTO device_logs (log_date, serial_number, table_name, action_did, fields_updated)
    VALUES (NOW(), NEW.serial_number, 'devices', 'UPDATE', nb_changes);
END//

-- Trigger pour la table computer
CREATE TRIGGER computer_after_insert
AFTER INSERT ON computer
FOR EACH ROW
BEGIN
    INSERT INTO device_logs (log_date, serial_number, table_name, action_did, fields_updated)
    VALUES (NOW(), NEW.serial_number, 'computer', 'INSERT', 11);
END//

CREATE TRIGGER computer_after_update
AFTER UPDATE ON computer
FOR EACH ROW
BEGIN
    DECLARE nb_changes INT DEFAULT 0;
    
    IF OLD.model != NEW.model THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.cpu != NEW.cpu THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.ram_mb != NEW.ram_mb THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.disk_gb != NEW.disk_gb THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.domain != NEW.domain THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.mac_address != NEW.mac_address THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.purchase_date != NEW.purchase_date THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.warranty_end != NEW.warranty_end THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.manufacturer_name != NEW.manufacturer_name THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.os_name != NEW.os_name THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.type_name != NEW.type_name THEN SET nb_changes = nb_changes + 1; END IF;
    
    INSERT INTO device_logs (log_date, serial_number, table_name, action_did, fields_updated)
    VALUES (NOW(), NEW.serial_number, 'computer', 'UPDATE', nb_changes);
END//

-- Trigger pour la table monitor
CREATE TRIGGER monitor_after_insert
AFTER INSERT ON monitor
FOR EACH ROW
BEGIN
    INSERT INTO device_logs (log_date, serial_number, table_name, action_did, fields_updated)
    VALUES (NOW(), NEW.serial_number, 'monitor', 'INSERT', 7);
END//

CREATE TRIGGER monitor_after_update
AFTER UPDATE ON monitor
FOR EACH ROW
BEGIN
    DECLARE nb_changes INT DEFAULT 0;
    
    IF OLD.model != NEW.model THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.size_inch != NEW.size_inch THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.resolution != NEW.resolution THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.manufacturer_name != NEW.manufacturer_name THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.connector_name != NEW.connector_name THEN SET nb_changes = nb_changes + 1; END IF;
    IF OLD.attached_to_serial != NEW.attached_to_serial THEN SET nb_changes = nb_changes + 1; END IF;
    
    INSERT INTO device_logs (log_date, serial_number, table_name, action_did, fields_updated)
    VALUES (NOW(), NEW.serial_number, 'monitor', 'UPDATE', nb_changes);
END//

DELIMITER ;


-- ici juste le compte des champs modif/ajouté => a def le réel besoin
-- a faire -> Logs pour les autre tables