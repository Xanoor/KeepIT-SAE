<!doctype html>
<html lang="fr">
    <head>
        <title>Page de configuration</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link
            rel="stylesheet"
            type="text/css"
            href="../styles/inventory-item.css"
        />
    </head>
    <body>
        <header>
            <div class="nav-left-container">
                <div class="app-name-container">
                    <a href="login.php">KEEPIT</a>
                </div>
                <nav class="nav-buttons-container">
                    <a href="#">Dashboard</a>
                    <a href="inventory.php" class="nav-buttons-current"
                        >Inventaire</a
                    >
                    <a href="#">Techniciens</a>
                    <a href="#">Informations</a>
                </nav>
            </div>
            <nav class="nav-right-container">
                <a href="#" class="profile-btn">Profil</a>
                <a href="#" class="log-out"
                    ><img src="../assets/log-out.png" alt="Profil"
                /></a>
            </nav>
        </header>
        <main class="inventory-item-main">
            <div>
                <div class="page-name">
                    <img alt="Logo du site" src="../assets/logo.png" />
                    <h1>Inventaire</h1>
                </div>
                <div class="create-item-header">
                    <select
                        name="device_type"
                        id="create-item-select"
                        aria-label="Type d'appareil"
                    >
                        <option disabled selected hidden>
                            Selectionner le type
                        </option>
                        <option value="COMPUTER">Ordinateur</option>
                        <option value="MONITOR">Écran</option>
                    </select>
                </div>

                <!-- /!\ This part is an example for the static page /!\ -->
                <section class="inventory-item-main-section">
                    <form action="" method="POST" id="create-item-form">
                       
                    </form>
                </section>
            </div>
        </main>
    </body>
    <script src="../scripts/callItemForm.js"></script>
</html>
