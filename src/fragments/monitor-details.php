<?php
function monitorDetailsFragment($items, $state_html, $manufacturer_html, $connector_html, $attached_to_html, $new_item) {
    $header_html = "";
    $submit_value = "Enregistrer";
    $serial_field_html = "";

    if (!$new_item) {
        $header_html = "
            <div class=\"inventory-item-name\">
                <h2>" . htmlspecialchars($items["manufacturer_name"] ?? "") . " " . htmlspecialchars($items["model"] ?? "") . "</h2>
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

    $admin_html = "";
    if (($_SESSION['role'] == "System Administrator" || $_SESSION['role'] == "Web Administrator") && !$new_item) {
           $admin_html .= "<input
                type=\"submit\"
                class=\"inventory-item-data\"
                value=\"Supprimer\"
                name=\"submit-delete\"
                id=\"submit-delete\"
            />";
    }

    return "
        $header_html
        <input type=\"hidden\" name=\"item-serial_number\" value=\"".htmlspecialchars($items["serial_number"] ?? "")."\">
        <input type=\"hidden\" name=\"item-device_type\" value=\"MONITOR\">
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
            $admin_html
        </div>
        <div class=\"inventory-item-category\">
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-model\">Modèle</label>
                    <input
                        id=\"item-input-model\"
                        type=\"text\"
                        value=\"" . htmlspecialchars($items["model"] ?? "") . "\"
                        name=\"item-model\"
                        placeholder=\"Nom/modèle de l'appareil\"
                        required
                    />
                </div>
                $serial_field_html
            </div>
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-status\"
                        >Statut</label
                    >
                    <select
                        id=\"item-input-status\"
                        name=\"item-state\"
                        required
                    >
                        $state_html
                    </select>
                </div>
            </div>
        </div>
        <div class=\"inventory-item-data\">Spécificités</div>
        <div class=\"inventory-item-category\">
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-size\"
                        >Taille d'écran (inch)</label
                    >
                    <input
                        id=\"item-input-size\"
                        type=\"number\"
                        value=\"" . htmlspecialchars($items["size_inch"] ?? "") . "\"
                        name=\"item-size\"
                        placeholder=\"24\"
                        required
                        min=\"0\"
                    />
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-resolution\"
                        >Résolution de l'écran</label
                    >
                    <input
                        id=\"item-input-resolution\"
                        type=\"text\"
                        value=\"" . htmlspecialchars($items["resolution"] ?? "") . "\"
                        name=\"item-resolution\"
                        placeholder=\"1920x1080\"
                        required
                    />
                </div>
            </div>
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-connector\"
                        >Connecteur</label
                    >
                    <select
                        id=\"item-input-connector\"
                        name=\"item-connector_name\"
                        required
                    >
                        <option value=\"null\">Aucun</option>
                        $connector_html
                    </select>
                </div>
                <div class=\"inventory-item\">
                    <label for=\"item-input-manufacturer\"
                        >Fabricant</label
                    >
                    <select
                        id=\"item-input-manufacturer\"
                        name=\"item-manufacturer_name\"
                        required
                    >
                        $manufacturer_html
                    </select>
                </div>
            </div>
        </div>
        <div class=\"inventory-item-data\">
            Autres informations
        </div>
        <div class=\"inventory-item-category\">
            <div class=\"inventory-item-col\">
                <div class=\"inventory-item\">
                    <label for=\"item-input-attached_to_computer\"
                        >Relier a un appareil (serial number)</label
                    >
                    <select
                        id=\"item-input-attached_to_computer\"
                        name=\"item-attached_to_computer\"
                    >
                        <option selected value=\"null\">Selectionner (optionnel)</option>
                        $attached_to_html
                    </select>
                </div>
        </div>
    ";
}
?>
