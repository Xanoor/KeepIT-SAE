-- We use values that we think are good but we aren't sure because we don't have any real data

USE keepit;

-- Insertions gérées par l'ADMIN WEB par la suite une fois que son interface sera prête
INSERT IGNORE INTO `locations` (location) VALUES ('Vélizy'), ('Rambouillet'), ('Versailles'), ('Toulouse');
INSERT IGNORE INTO `device_types` (name) VALUES ('Computer'), ('Monitor');
INSERT IGNORE INTO `device_states` (state, css_class) VALUES ('Déployé', 'table-item-DEPLOYED'), ('En stock', 'table-item-IN_INVENTORY'), ('Fin de vie', 'table-item-END_OF_LIFE');
INSERT IGNORE INTO `manufacturer` (name) VALUES ('Acer'), ('Dell'), ('HP'), ('Lenovo'), ('Asus'), ('Samsung'), ('LG'), ('BenQ'), ('AOC');
INSERT IGNORE INTO `operating_system` (name) VALUES ('Windows 11 Pro'), ('Windows 10 Pro'), ('Windows 10 Edu'), ('Windows 11 Edu');
INSERT IGNORE INTO `connector` (name) VALUES ('HDMI'), ('VGA'), ('DVI'), ('DisplayPort'), ('USB-C');