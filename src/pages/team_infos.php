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
$import_errors = $_SESSION['import_errors'] ?? null;
unset($_SESSION['notification']);
unset($_SESSION['notification_color']);
unset($_SESSION['import_errors']);
?>

<!doctype html>
<html lang="fr">
    <head>
        <title>L'équipe KEEPIT</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/team_infos.css" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <header>
            <div class="nav-left-container">
                <div class="app-name-container">
                    <a href="login.php">KEEPIT</a>
                </div>
                <nav class="nav-buttons-container">
                    <a href="#">Dashboard</a>
                    <a href="#">Inventaire</a
                    >
                    <a href="#">Techniciens</a>
                    <a href="#" class="nav-buttons-current" >Informations</a>
                </nav>
            </div>
            <nav class="nav-right-container">
                <a href="#" class="profile-btn">Profil</a>
                <a href="../actions/logout_action.php" class="log-out">
                    <img src="../assets/log-out.png" alt="Déconnexion"/>
                </a>
            </nav>
        </header>
        <main>
        <div class="page-name">
            <img alt="Logo du site" src="../assets/logo.png" />
            <h1>Présentation de l'équipe</h1>
        </div>
        <section class="team-section">
            <div class="team-container">
                <button class="member-card js-scroll-trigger" data-to="detail-nourane">
                    <div class="card-outline">
                        <img src="../assets/nourane.png" alt="Photo Nourâne" class="member-img">
                    </div>
                </button>
                <button class="member-card js-scroll-trigger" data-to="detail-nicolas">
                    <div class="card-outline">
                        <img src="../assets/nicolas.png" alt="Photo Nicolas" class="member-img">
                    </div>
                </button>

                <button class="member-card js-scroll-trigger" data-to="detail-theo">
                    <div class="card-outline">
                        <img src="../assets/theo.png" alt="Photo Théo" class="member-img">
                    </div>
                </button>

                <button class="member-card js-scroll-trigger" data-to="detail-gabriel">
                    <div class="card-outline">
                        <img src="../assets/gabriel.png" alt="Photo Gabriel" class="member-img">
                    </div>
                </button>

                <button class="member-card js-scroll-trigger" data-to="detail-daniel">
                    <div class="card-outline">
                        <img src="../assets/daniel.png" alt="Photo Daniel" class="member-img">
                    </div>
                </button>

                </div>

            <div class="details-wrapper">

                <div id="detail-nourane" class="detail-card">
                    <div class="detail-content">
                        <div class="detail-text">
                            <h2>ATHOUMANI</h2>
                            <h3>Nourâne</h3>
                            <p>Responsable des statistiques, <br>
                                Développeur,<br>
                                Rédacteur de livrables.</p>
                        </div>
                        <div class="detail-image-container">
                            <img src="../assets/nourane.png" alt="Nourâne" class="detail-photo">
                        </div>
                    </div>
                </div>

                <div id="detail-nicolas" class="detail-card">
                    <div class="detail-content">
                        <div class="detail-text">
                            <h2>RACOT</h2>
                            <h3>Nicolas</h3>
                            <p>Responsable infrastructure, <br>Développeur.</p>
                        </div>
                        <div class="detail-image-container">
                            <img src="../assets/nicolas.png" alt="Nicolas" class="detail-photo">
                        </div>
                    </div>
                </div>


                <div id="detail-theo" class="detail-card">
                    <div class="detail-content">
                        <div class="detail-text">
                            <h2>PEYRONNET</h2>
                            <h3>Théo</h3>
                            <p>Chef de projet, <br>
                            Responsable des développements,<br>
                            Co-designer</p>
                        </div>
                        <div class="detail-image-container">
                            <img src="../assets/theo.png" alt="Théo" class="detail-photo">
                        </div>
                    </div>
                </div>


                <div id="detail-gabriel" class="detail-card">
                    <div class="detail-content">
                        <div class="detail-text">
                            <h2>CHIFFLET</h2>
                            <h3>Gabriel</h3>
                            <p>Responsable des bases de données, <br>
                            Développeur, <br>
                            Rédacteur des spécifications et RGPD.</p>
                        </div>
                        <div class="detail-image-container">
                            <img src="../assets/gabriel.png" alt="Gabriel" class="detail-photo">
                        </div>
                    </div>
                </div>


                <div id="detail-daniel" class="detail-card">
                    <div class="detail-content">
                        <div class="detail-text">
                            <h2>RODRIGUES AMORIM</h2>
                            <h3>Daniel</h3>
                            <p>Responsable DA Graphique,<br>Développeur,<br>Rédacteur de livrables.</p>
                        </div>
                        <div class="detail-image-container">
                            <img src="../assets/daniel.png" alt="Daniel" class="detail-photo">
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
        <!-- Notification container -->
        <div class="notifications-container" id="notificationsContainer"></div>
    </body>
    <footer>
        <p>Utilisation d'une IA pour la modification des photos.<br> Utilisation de Gemini Pro 3.1</p>
        <img src="../assets/picto_ia.png" alt="Pictogramme utilisation partagée de l'IA" class="ia-logo">
    </footer>
    <script>
        const notif = <?= json_encode($notification) ?>;
        const notif_color = <?= json_encode($notification_color) ?>;
    </script>
    <script src="../scripts/notification.js"></script>
    <script src="../scripts/callItemForm.js"></script>
    <script>
        document.querySelectorAll('.js-scroll-trigger').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.getAttribute('data-to'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>   
</html>
