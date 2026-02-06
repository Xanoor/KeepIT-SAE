<?php
function computerDetailsFragment($items, $state_html, $os_html, $manufacturer_html, $location_html, $new_item) {
    $header_html = "";
    $submit_value = "Enregistrer";
    $serial_field_html = "";

    if (!$new_item) {
        $header_html = "
            <div class=\"inventory-item-name\">
                <h2>" . htmlspecialchars($items["name"] ?? "") . "</h2>
                <h3>SN: " . htmlspecialchars($items["serial_number"] ?? "") . "</h3>
            </div>";
    } else {
        $submit_value = "Créer";
        $serial_field_html = "
            <div class=\"inventory-item\">
                <label for=\"item-input-sn\">Numéro de série</label>
                <input
                    id=\"item-input-sn\"
                    type=\"text\"
                    value=\"" . htmlspecialchars($items["serial_number"] ?? "") . "\"
                    name=\"item-serial_number\"
                    placeholder=\"Numéro de série\"
                    required
                />
            </div>";
    }

    return "
        $header_html
        <input type=\"hidden\" name=\"item-device_type\" value=\"COMPUTER\">
        <div class=\"inventory-item-inline\">
            <div class=\"inventory-item-data\">
                Informations générales
            </div>
            <input
                type=\"submit\"
                class=\"inventory-item-data\"
                value=\"$submit_value\"
                name=\"submit\"
            />
        </div>
        <div class=\"inventory-item-category\">
            <div class=\"inventory-item-col\">
                $serial_field_html
                <div class=\"inventory-item\">
                    <label for=\"item-input-name\">Nom</label>
                    <input
                        id=\"item-input-name\"
                        type=\"text\"
                        value=\"" . htmlspecialchars($items["name"] ?? "") . "\"
                        name=\"item-name\"
                        placeholder=\"Nom/modèle de l'appareil\"
                        required
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-category\">Catégorie</label
                    >
                    <input
                        id=\"item-input-category\"
                        type=\"text\"
                        value=\"" . htmlspecialchars($items["type_name"] ?? "") . "\"
                        name=\"item-type_name\"
                        placeholder=\"Catégorie de l'appareil\"
                        required
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-status\"
                        >Statut</label
                    >
                    <select id=\"item-input-status\" name=\"item-state\" required>
                        $state_html
                    </select>
                </div>
            </div>
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-loc\"
                        >Lieu de détention</label
                    >
                    <select
                        id=\"item-input-loc\"
                        name=\"item-location\"
                        required
                    >
                        $location_html
                    </select>
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-building\"
                        >Bâtiment</label
                    >
                    <input
                        id=\"item-input-building\"
                        type=\"text\"
                        value=\"" . htmlspecialchars($items["building"] ?? "") . "\"
                        name=\"item-building\"
                        placeholder=\"Nom du bâtiment\"
                        required
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-room\"
                        >Salle de détention</label
                    >
                    <input
                        id=\"item-input-room\"
                        type=\"text\"
                        value=\"" . htmlspecialchars($items["room"] ?? "") . "\"
                        name=\"item-room\"
                        placeholder=\"Nom de la salle\"
                        required
                    />
                </div>
            </div>
        </div>
        <div class=\"inventory-item-data\">Spécificités</div>
        <div class=\"inventory-item-category\">
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-cpu\"
                        >Processeur (CPU)</label
                    >
                    <input
                        id=\"item-input-cpu\"
                        type=\"text\"
                        value=\"" . htmlspecialchars($items["cpu"] ?? "") . "\"
                        name=\"item-cpu\"
                        placeholder=\"Nom du processeur\"
                        required
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-os\"
                        >Système d'exploitation (OS)</label
                    >
                    <select id=\"item-input-os\" name=\"item-os_name\" required>
                        $os_html
                    </select>
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-manufacturer\"
                        >Fabricant</label
                    >
                    <select id=\"item-input-manufacturer\" name=\"item-manufacturer_name\" required>
                        $manufacturer_html
                    </select>
                </div>
            </div>
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-ram\">RAM (Mo)</label>
                    <input
                        type=\"number\"
                        value=\"" . htmlspecialchars($items["ram_mb"] ?? "") . "\"
                        id=\"item-input-ram\"
                        name=\"item-ram_mb\"
                        placeholder=\"Mémoire vive (Mega octets)\"
                        required
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-disk\"
                        >Taille du stockage (Go)</label
                    >
                    <input
                        type=\"number\"
                        value=\"" . htmlspecialchars($items["disk_gb"] ?? "") . "\"
                        id=\"item-input-disk\"
                        name=\"item-disk_gb\"
                        placeholder=\"Taille du stockage (Go)\"
                        required
                    />
                </div>
            </div>
        </div>
        <div class=\"inventory-item-data\">
            Autres informations
        </div>
        <div class=\"inventory-item-category\">
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-domain\"
                        >Domaine</label
                    >
                    <input
                        type=\"text\"
                        id=\"item-input-domain\"
                        value=\"" . htmlspecialchars($items["domain"] ?? "") . "\"
                        name=\"item-domain\"
                        placeholder=\"Domaine informatique\"
                        required
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-mac\"
                        >Adresse physique (MAC)</label
                    >
                    <input
                        type=\"text\"
                        id=\"item-input-mac\"
                        value=\"" . htmlspecialchars($items["mac_address"] ?? "") . "\"
                        name=\"item-mac_address\"
                        placeholder=\"Adresse physique (MAC)\"
                        required
                    />
                </div>
            </div>
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-purchase_date\"
                        >Date d'achat</label
                    >
                    <input
                        type=\"text\"
                        id=\"item-input-purchase_date\"
                        value=\"" . htmlspecialchars($items["purchase_date"] ?? "") . "\"
                        name=\"item-purchase_date\"
                        placeholder=\"2024-07-30\"
                        required
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-warranty_end\"
                        >Date de fin de garantie</label
                    >
                    <input
                        type=\"text\"
                        id=\"item-input-warranty_end\"
                        value=\"" . htmlspecialchars($items["warranty_end"] ?? "") . "\"
                        name=\"item-warranty_end\"
                        placeholder=\"2024-07-30\"
                        required
                    />
                </div>
            </div>
        </div>
    ";
}
?>
