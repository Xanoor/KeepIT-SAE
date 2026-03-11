<?php 
    session_start();
    if (!isset($_SESSION['login']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'Web Administrator') {
        header("Location: login.php");
        exit();
    }
    $notification = $_SESSION['notification'] ?? null;
    $notification_color = $_SESSION['notification_color'] ?? null;
    unset($_SESSION['notification']);
    unset($_SESSION['notification_color']);
    include_once("../includes/functions.php");
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Techniciens</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/inventory-table.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
        <link rel="stylesheet" type="text/css" href="../styles/technician.css" />
    </head>
    <body>
        <header>
            <div class="nav-left-container">
                <div class="app-name-container">
                    <a href="index.php">KEEPIT</a>
                </div>
                <nav class="nav-buttons-container">
                    <a href="index.php">Dashboard</a>
                    <a href="inventory.php">Inventaire</a>
                    <a href="#" class="nav-buttons-current">Techniciens</a>
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
                <h1>Techniciens</h1>
            </div>

            <section class="inventory-top-section">
                <div class="form-header">
                    <div>
                        <button type="button" class="bar-textfield">Vue d'ensemble</button>
                    </div>
                    <div>
                        <a href="create-technician.php"><button type="button" class="bar-textfield">Ajouter</button></a>
                    </div>
                </div>
            </section>

            <section class="page-content">
                <div class="page-table-content">
                    <div class="table-preview">
                        <table id="tech-data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NOM</th>
                                    <th>PRÉNOM</th>
                                    <th>CRÉATION DU COMPTE</th>
                                    <th>DERNIÈRE CONNEXION</th>
                                    <th>TEMPS DE CONNEXION</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $users = loadUsersFromDB('Technician');
                                    $id_tech = 0;
                                    
                                    while ($user = mysqli_fetch_assoc($users)) {
                                        $id_tech++;
                                        ?>
                                        <tr 
                                            class="tech-row <?php echo $isActive; ?>" 
                                            onclick="fillEditTechForm(this)" 
                                            data-id="<?php echo $id_tech; ?>"
                                            data-nom="<?php echo htmlspecialchars($user['last_name']); ?>"
                                            data-prenom="<?php echo htmlspecialchars($user['first_name']); ?>"
                                            data-login="<?php echo htmlspecialchars($user['login']); ?>"
                                        >
                                        <?php
                                            echo "<td>". $id_tech . "</td>";
                                            echo "<td>". htmlspecialchars($user['last_name']) ."</td>";
                                            echo "<td>". htmlspecialchars($user['first_name']) ."</td>";
                                            echo "<td>". timeAgoFr($user['created_at']) ."</td>";
                                            echo "<td>". timeAgoFr($user['last_login_at']) ."</td>";
                                            echo "<td> ? </td>";
                                            echo "<td></td>";
                                        ?>
                                        </tr>
                                        <?php
                                    }
                                ?>
                                </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="edit-tech-footer">
                <h2 class="edit-tech-title">
                    Veuillez sélectionner un technicien.
                </h2>
                
                <form class="edit-form-section" method="POST" action="../actions/update-technician.php">
                    <div class="form-group">
                        <label for="name">Prénom</label>
                        <input type="text" name="name" id="name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="surname">Nom</label>
                        <input type="text" name="surname" id="surname" readonly>
                    </div>
                    <div class="form-group">
                        <label for="login">Login</label>
                        <input type="text" name="login" id="login" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="****************" required>
                            <span class="eye-icon"></span>
                        </div>
                    </div>
                    
                    <div class="form-actions-row">
                        <input type="submit" class="btn-save-edit" value="Enregistrer" />
                    </div>   
                </form>
            </section>
            <div class="notifications-container" id="notificationsContainer"></div>
        </main>
    </body>
    <script src="../scripts/showPassword.js"></script>
    <script>
        const notif = <?= json_encode($notification) ?>;
        const notif_color = <?= json_encode($notification_color) ?>;
    </script>
    <script src="../scripts/notification.js"></script>
    <script src="../scripts/technician.js"></script>
</html>