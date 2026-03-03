<?php 
    session_start();

    // Everyone that have a role (tech, adm...) can access this page
    if (!isset($_SESSION['login']) || !isset($_SESSION['role'])) {
        header("Location: login.php");
        exit();
    }

    include_once("../includes/functions.php");

    $notification = $_SESSION['notification'] ?? null;
    $notification_color = $_SESSION['notification_color'] ?? null;
    unset($_SESSION['notification']);
    unset($_SESSION['notification_color']);

    // --- Data and Pagination Logic ---
    $searchTerm = $_POST['search-item'] ?? '';
    // Fetch selected filters from POST (structured as filters[column][])
    $selectedFilters = $_POST['filters'] ?? ['Tout selectionner'];
    $pageNumber = (int)($_POST['page-number'] ?? 0);

    // Filter Logic: Build the SQL WHERE clause parts
    $filters = [];

    if (!empty($searchTerm)) {
        // Search by serial number & name using LIKE
        $filters['serial_number'] = "%" . $searchTerm . "%";
        $filters['name'] = "%" . $searchTerm . "%";
    }

    if (!in_array("Tout selectionner", $selectedFilters)) { //if "Tout selectionner" is not in the selected filters, build the filters array
        // Iterate through each column-specific filter group
        foreach ($selectedFilters as $col => $vals) {
            if (is_array($vals)) {
                // Add the group of values to the SQL filters array
                $filters[$col] = $vals;
            }
        }
    }

    // Handle navigation buttons
    if (isset($_POST["next-page"])) {
        $pageNumber += 11;
    } else if (isset($_POST["previous-page"])) {
        $pageNumber = max(0, $pageNumber - 11);
    } else if (isset($_POST["page-num"]) && !isset($_POST["inventory_search"])) { // Manual page entry
        $requestedPage = (int)$_POST["page-num"];
        $pageNumber = max(0, ($requestedPage - 1) * 11);
    } else if (isset($_POST["inventory_search"])) {
        $pageNumber = 0; // Reset pagination on new search
    }

    // Limits
    $start = $pageNumber;
    $end = $start + 11;
    $currentPage = floor($pageNumber / 11) + 1;
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Inventaire</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/inventory-table.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <header>
            <div class="nav-left-container">
                <div class="app-name-container">
                    <a href="index.php">KEEPIT</a>
                </div>
                <nav class="nav-buttons-container">
                    <a href="index.php">Dashboard</a>
                    <a href="#" class="nav-buttons-current">Inventaire</a>
                    <a href="#">Techniciens</a>
                    <a href="#">Informations</a>
                </nav>
            </div>
            <nav class="nav-right-container">
                <a href="account-management.php" class="profile-btn">Profil</a>
                <a href="../actions/logout_action.php" class="log-out">
                    <img src="../assets/log-out.png" alt="Déconnexion"/>
                </a>
            </nav>
        </header>
        <main>
            <div class="page-name">
                <img alt="Logo du site" src="../assets/logo.png" />
                <h1>Inventaire</h1>
            </div>
            <!-- Search, Filter and Pagination Form (Single Form) -->
            <form action="" method="POST" id="main-inventory-form">
                <section class="inventory-top-section">
                    <div class="form-header">
                        <div>
                            <input
                                type="search"
                                placeholder="Rechercher"
                                aria-label="Rechercher"
                                name="search-item"
                                value="<?php echo htmlspecialchars($searchTerm) ?>"
                            />
                            <div class="select-checkbox">
                                <input
                                    type="button"
                                    id="select-checkbox-button"
                                    value="Filtres"
                                />
                                <fieldset
                                    id="select-checkbox-list"
                                    style="display: none"
                                    legend=""
                                >
                                    <legend class="sr-only">
                                        Filtres
                                    </legend>
                                    <div>
                                        <label for="select-checkbox-all"
                                            >Tout selectionner</label
                                        >
                                        <input
                                            name="filters[]"
                                            type="checkbox"
                                            class="select-checkbox-checkbox"
                                            id="select-checkbox-all"
                                            value="Tout selectionner"
                                            <?php echo in_array("Tout selectionner", $selectedFilters) ? "checked" : "" ?>
                                        />
                                    </div>
                                    <?php 
                                        // Create the inputs elements for certains SQL tables
                                        echo generateInventoryFilters([
                                            "device_types" => ["columnName" => "name", "devices_col" => "device_type"], 
                                            "device_states" => ["columnName" => "state", "devices_col" => "state"]
                                        ]);
                                    ?>
                                </fieldset>
                            </div>
                            <button type="submit" name="inventory_search">
                                <img
                                    src="../assets/search.png"
                                    alt="Rechercher"
                                />
                            </button>
                        </div>
                        <div>
                            <select
                                name="inventory-action"
                                aria-label="Action"
                                id="action_select"
                            >
                                <option disabled selected hidden>Action</option>
                                <option value="./create-item.php">Ajouter</option>
                                <option value="./importCSV.php">
                                    Importer
                                </option>
                                <option value="#">Exporter</option>
                            </select>
                        </div>
                    </div>
                </section>
                <section class="page-content">
                    <div class="page-table-content">
                        <?php 
                            echo "<div class='table-preview'>" . importSQLTableBuilder("vw_inventory_search_table", $filters, $start, $end, ["serial_number", "state", "device_type"]) . "</div>";
                        ?>
                    </div>
                    <div class="nav-buttons">
                        <button type="submit" name="previous-page" <?php if ($start == 0) echo "disabled" ?>>
                            <img
                                src="../assets/arrow-big-left.png"
                                alt="Page précédente"
                            />
                        </button>
                        <label for="page-num-input" class="sr-only">Numéro de page</label>
                        <input type="number" name="page-num" class="page-num-input" value="<?php echo $currentPage ?>" id="page-num-input" aria-label="Numéro de la page actuelle">
                        <button type="submit" name="next-page">
                            <img
                                src="../assets/arrow-big-right.png"
                                alt="Page suivante"
                            />
                        </button>
                        <input type="hidden" name="page-number" value="<?php echo $pageNumber ?>"/>
                    </div>
                </section>
            </form>
            <!-- Notification container -->
            <div class="notifications-container" id="notificationsContainer"></div>
        </main>
    </body>
    <!-- Scripts -->
    <script>const notif = <?= json_encode($notification) ?>;const notif_color = <?= json_encode($notification_color) ?>;</script>
    <script src="../scripts/notification.js"></script>
    <script src="../scripts/inventory.js"></script>
</html>
