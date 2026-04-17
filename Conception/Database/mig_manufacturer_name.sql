
SELECT COUNT(*) FROM monitor, computer WHERE monitor.serial_number = computer.serial_number;
-- DOIT ETRE à 0 !

ALTER TABLE devices ADD manufacturer_name varchar(50) NOT NULL AFTER model;

START TRANSACTION;

UPDATE devices d
JOIN computer c ON d.serial_number = c.serial_number
SET d.manufacturer_name = c.manufacturer_name;

UPDATE devices d
JOIN monitor m ON d.serial_number = m.serial_number
SET d.manufacturer_name = m.manufacturer_name;

COMMIT;

ALTER TABLE monitor DROP CONSTRAINT fk_monitor_manufacturer_name;
ALTER TABLE computer DROP CONSTRAINT fk_computer_manufacturer_name;

ALTER TABLE monitor DROP INDEX idx_monitor_manufacturer_name;
ALTER TABLE computer DROP INDEX idx_computer_manufacturer_name;

ALTER TABLE monitor DROP COLUMN manufacturer_name;
ALTER TABLE computer DROP COLUMN manufacturer_name;

CREATE INDEX idx_devices_manufacturer_name ON devices(manufacturer_name);
ALTER TABLE devices ADD CONSTRAINT fk_computer_manufacturer_name
        FOREIGN KEY (manufacturer_name) REFERENCES manufacturer(name)
            ON UPDATE CASCADE
            ON DELETE RESTRICT;

UPDATE device_logs SET table_name ='devices' WHERE field_updated = 'manufacturer_name' AND action_did = 'UPDATE';
