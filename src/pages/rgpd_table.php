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
        <title>RGPD</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/rgpd_table.css" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <?php include_once("../fragments/header.php"); ?>
        
        <div class="page-name">
            <img alt="Logo du site" src="../assets/logo.png" />
            <h1>RGPD</h1>
        </div>
        <main>
            <table>
                <thead>
                    <tr>
                        <th colspan="4">Identification du traitement</th>
                        <th rowspan="2">Finalité du traitement</th>
                        <th colspan="1">Données sensibles ?</th>
                    </tr>
                    <tr>
                        <th>Nom du traitement</th>
                        <th>N° / RÉF</th>
                        <th>Date de création de la fiche</th>
                        <th>Dernière mise à jour de la fiche</th>
                        <th>Oui/non</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gestion des comptes</td>
                        <td>001</td>
                        <td>08/03/2026</td>
                        <td>15/04/2026</td>
                        <td>Identification et accès au site web</td>
                        <td>Non</td>
                    </tr>
                </tbody>
            </table>

            <br>
            <hr>
            <br>

            <table>
                <thead>
                    <tr>
                        <th colspan="8">Modèle de fiche de registre</th>
                        <th>ref-001</th>
                    </tr>
                    <tr>
                        <td colspan="9">Cet onglet est la fiche opérationnelle concernant la gestion des comptes</td>
                    </tr>
                    <tr>
                        <th colspan="9">Description du traitement</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Nom du traitement</th>
                        <td colspan="8">Gestion des comptes</td>
                    </tr>
                    <tr>
                        <th>N° / RÉF</th>
                        <td colspan="8">ref-001</td>
                    </tr>
                    <tr>
                        <th>Date de création du traitement</th>
                        <td colspan="8">08/03/2026</td>
                    </tr>
                    <tr>
                        <th>Mise à jour du traitement</th>
                        <td colspan="8">15/04/2026</td>
                    </tr>
                    <tr>
                        <td colspan="9">
                            KeepIT agit en qualité d'éditeur logiciel et n'intervient pas dans la mise en oeuvre des traitements de données à caractère personnel. En conséquence, leclient, utilisateur autonome du logiciel, est le seul responsable du traitement au sens de la réglementation en matière de protection des données.
                        </td>
                    </tr>

                    <tr>
                        <th>Acteurs</th>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Code Postal</th>
                        <th>Ville</th>
                        <th>Pays</th>
                        <th>Téléphone</th>
                        <th colspan="2">Adresse mél</th>
                    </tr>
                    <tr>
                        <td>Responsable du traitement</td>
                        <td>Les clients</td>
                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td colspan="2">-</td>
                    </tr>
                    <tr>
                        <td>Délégué à la protection des données</td>
                        <td>Les clients</td>
                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td colspan="2">-</td>
                    </tr>
                    <tr>
                        <td>Société du DPO</td>
                        <td>Les clients</td>
                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td colspan="2">-</td>
                    </tr>
                    <tr>
                        <td>Représentant</td>
                        <td>Les clients</td>
                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td colspan="2">-</td>
                    </tr>
                    <tr>
                        <td>Responsable(s) conjoint(s)</td>
                        <td>Les clients</td>
                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td colspan="2">-</td>
                    </tr>

                    <tr>
                        <th colspan="9">Finalité(s) du traitement effectué</th>
                    </tr>
                    <tr>
                        <td>Finalité principale</td>
                        <td colspan="8">Identification et accès au site web</td>
                    </tr>
                    <tr>
                        <td>Sous-finalité 1</td>
                        <td colspan="8">Identification des mise à jour des informations relatives aux attributs du matériel renseigné dans le système</td>
                    </tr>
                    <tr>
                        <td>Sous-finalité 2</td>
                        <td colspan="8">Renseignement des technicien en activité récente</td>
                    </tr>

                    <tr>
                        <th colspan="3">Catégories de données personnelles concernées</th>
                        <th colspan="3">Description</th>
                        <th colspan="3">Durée de conservation</th>
                    </tr>
                    <tr>
                        <td colspan="3">État civil, identité...</td>
                        <td colspan="3">Nom, Prénom, login, mot de passe, dernière connexion </td>
                        <td colspan="3">1 mois après départ</td>
                    </tr>
                    <tr>
                        <td colspan="3">Vie personnelle</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Informations d'ordre économique et financier </td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Données de localisation</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Numéro de Sécurité Sociale</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>

                    <tr>
                        <th colspan="3">Données sensibles</th>
                        <th colspan="3">Description</th>
                        <th colspan="3">Durée de conservation</th>
                    </tr>
                    <tr>
                        <td colspan="3">Données révélant l'origine raciale ou ethnique</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Données révélant les opinions politiques</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Données révélant l'appartenance syndicale</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Données génétiques</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Données biométriques aux fins d'identifier une personne physique de manière unique</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Données concernant la vie sexuelle ou l'orientation sexuelle</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>
                    <tr>
                        <td colspan="3">Données relatives à des condamnations pénales ou  infractions</td>
                        <td colspan="3">-</td>
                        <td colspan="3">-</td>
                    </tr>

                    <tr>
                        <th colspan="3">Catégories de personnes concernées</th>
                        <th colspan="3">Description</th>
                        <th colspan="3">Précisions</th>
                    </tr>
                    <tr>
                        <td colspan="3">Catégorie 1</td>
                        <td colspan="3">Services internes</td>
                        <td colspan="3">Utilisateurs du site</td>
                    </tr>

                    <tr>
                        <th colspan="3">Destinataires</th>
                        <th colspan="3">Type de destinataire</th>
                        <th colspan="3">Précisions</th>
                    </tr>
                    <tr>
                        <td colspan="3">Destinataire 1</td>
                        <td colspan="3">Service interne</td>
                        <td colspan="3">Site web (KeepIT) afin de générer des profils utilisateurs</td>
                    </tr>

                    <tr>
                        <th colspan="3">Mesures de sécurité</th>
                        <th colspan="3">Type</th>
                        <th colspan="3">Précisions</th>
                    </tr>
                    <tr>
                        <td colspan="3">Mesure 1</td>
                        <td colspan="3">Mesures de protection des logiciels</td>
                        <td colspan="3">Vérification d'identité, réseau interne uniquement</td>
                    </tr>
                    <tr>
                        <td colspan="3">Mesure 2</td>
                        <td colspan="3">Sauvegarde des données</td>
                        <td colspan="3">Backup de la base de donnée (mise en place entre 04/2026 et 06/2026)</td>
                    </tr>
                    <tr>
                        <td colspan="3">Mesure 3</td>
                        <td colspan="3">Contrôle d'accès des utilisateurs</td>
                        <td colspan="3">Informations uniquement consultable par les utilisateurs après identification;modification par le propriétaire et l'administrateur web</td>
                    </tr>
                    <tr>
                        <td colspan="3">Mesure 4</td>
                        <td colspan="3">Chiffrement des données</td>
                        <td colspan="3">Hashage des mots de passe et accès SSH au serveur</td>
                    </tr>

                    <tr>
                        <th colspan="2">Transferts hors UE</th>
                        <th colspan="2">Destinataire</th>
                        <th colspan="2">Pays</th>
                        <th colspan="2">Garanties</th>
                        <th>Documentation</th>
                    </tr>
                    <tr>
                        <td colspan="2">Organisme 1</td>
                        <td colspan="2">-</td>
                        <td colspan="2">-</td>
                        <td colspan="2">-</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </main>
        <div class="notifications-container" id="notificationsContainer"></div>
    </body>
    <script>
        const notif = <?= json_encode($notification) ?>;
        const notif_color = <?= json_encode($notification_color) ?>;
    </script>
    <script src="../scripts/notification.js"></script>
</html>