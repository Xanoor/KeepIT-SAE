-- We use values that we think are good but we aren't sure because we don't have any real data


-- Inserts per country
INSERT IGNORE INTO `locations` (location) VALUES ('Rambouillet'), ('Versailles'), ('Toulouse');
INSERT IGNORE INTO `locations` (location) VALUES ('Berlin'), ('Frankfurt');

INSERT IGNORE INTO `device_types` (name) VALUES ('Computer'), ('Monitor');
INSERT IGNORE INTO `device_states` (state, css_class) VALUES ('Déployé', 'table-item-DEPLOYED'), ('En stock', 'table-item-IN_INVENTORY'), ('Fin de vie', 'table-item-END_OF_LIFE');
INSERT IGNORE INTO `manufacturer` (name) VALUES ('Acer'), ('Dell'), ('HP'), ('Lenovo'), ('Asus'), ('Samsung'), ('LG'), ('BenQ'), ('AOC');
INSERT IGNORE INTO `operating_system` (name) VALUES ('Windows 10'), ('Windows 11'), ('Ubuntu 22.04'), ('Debian 12');
INSERT IGNORE INTO `connector` (name) VALUES ('HDMI'), ('VGA'), ('DVI'), ('DisplayPort'), ('USB-C');