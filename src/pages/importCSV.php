<?php 
    include_once("../fragments/functions.php"); 
    session_start();
    $notification = $_SESSION['notification'] ?? null;
    $notification_color = $_SESSION['notification_color'] ?? null;
    $import_errors = $_SESSION['import_errors'] ?? null;
    unset($_SESSION['notification']);
    unset($_SESSION['notification_color']);
    unset($_SESSION['import_errors']);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Importer un fichier CSV</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/importCSV.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <header>
            <div class="nav-left-container">
                <div class="app-name-container">
                    <a href="index.php">KEEPIT</a>
                </div>
                <nav class="nav-buttons-container">
                    <a href="#">Dashboard</a>
                    <a href="./inventory.php" class="nav-buttons-current">Inventaire</a>
                    <a href="#">Techniciens</a>
                    <a href="#">Informations</a>
                </nav>
            </div>
            <nav class="nav-right-container">
                <a href="#" class="profile-btn">Profil</a>
                <a href="#" class="log-out"></a>
            </nav>
        </header>
        <main>
            <div class="page-name">
                <img src="../assets/logo.png" />
                <h1>Inventaire</h1>
            </div>
            <section>
                <form action="" id="csvForm" method="POST" enctype="multipart/form-data">
                    <div class="form-header">
                        <div>
                            <input type="button" value="Retour" name="back" />
                            <select name="device_type">
                                <option disabled selected hidden>
                                    Choisir appareil
                                </option>
                                <option value="CENTRAL_UNIT" <?php if (isset($_POST['device_type']) && $_POST['device_type'] == "CENTRAL_UNIT") echo'selected'; ?>>Unité centrale</option>
                                <option value="SCREEN" <?php if (isset($_POST['device_type'])  && $_POST['device_type'] == "SCREEN") echo'selected'; ?>>Écran</option>
                            </select>
                        </div>
                        <div>
                            <input
                                type="submit"
                                value="Charger"
                                name="load_csv"
                                id="load_csv"
                                disabled
                            />
                            <input
                                type="submit"
                                value="Enregistrer"
                                name="import_csv"
                                id="import_csv"
                                <?php if (!isset($_POST["load_csv"])) echo "disabled"; ?>
                            />
                        </div>
                    </div>
                    <div class="form-content">
                        <?php 
                            if (isset($_FILES['file'], $_POST["load_csv"]) && $_FILES['file']['error'] === 0) {

                                // Verify ext
                                $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                                if (strtolower($fileType) !== "csv") {
                                    $_SESSION['notification'] = "Seuls les fichiers CSV sont autorisés !";
                                    header("Location: importCSV.php");
                                    exit();
                                }

                                // Move csv to a tmp folder
                                $uploadDir = "../uploads/";
                                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                                $filePath = $uploadDir . basename($_FILES['file']['name']);
                                if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                                    $_SESSION['notification'] = "Erreur pendant l'importation !";
                                    header("Location: importCSV.php");
                                    exit();
                                }

                                $file = fopen($filePath, "r");
                                echo "<div class='table-preview'><input type='hidden' name='file_path_csv' value='".$filePath."'/>".importTableBuilder($file, 15)."</div></div><p>Ce tableau présente un <b>extrait</b> des données.</p>";

                            } else {
                                echo "<div class='file-upload-wrapper'>
                                    <input
                                        type='file'
                                        name='file'
                                        accept='.csv'
                                        id='file-upload'
                                        class='file-upload-input'
                                    />
                                    <label for='file-upload' class='file-upload-label'>
                                        <div class='file-upload-design'>
                                            <img
                                                src='../assets/upload.png'
                                                class='file-upload-icon'
                                            />
                                            <span class='file-upload-text'
                                                >Cliquez et choisissez votre fichier
                                                CSV</span
                                            >
                                        </div>
                                    </label>
                                </div>
                            </div>";
                            }
                        ?>
                </form>
            </section>
            <!-- Errors container -->
            <?php if ($import_errors): ?>
                <section class="error-container" id="error-container">
                    <div class="error-container-header">
                        <p>Rapport d'erreurs :</p>
                        <img id="closeErrors" src="../assets/message-circle-x.png" alt="Fermer">
                    </div>
                    <ul>
                        <?php foreach ($import_errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
            <!-- Notification container -->
            <div class="notifications-container" id="notificationsContainer"></div>
        </main>
        <!-- Scripts -->
        <script>const notif = <?= json_encode($notification) ?>;const notif_color = <?= json_encode($notification_color) ?>;</script>
        <script src="../scripts/importCSV.js"></script>
        <script src="../scripts/notification.js"></script>
    </body>
</html>
