# Ordre exécution des fichiers SQL

| Ordre | Nom du fichier      | Détaille                                    |
| ----- | ------------------- | ------------------------------------------- |
| 1     | `init.sql`          | Mise en place de la base de données         |
| 2     | `inserts.sql`       | Données statique                            |
| 3     | `logs_database.sql` | Ensemble des structures de gestion des logs |
| 4     | `Views.sql`         | Les vues                                    |
| 5     | `action.sql`        | Les procédures, fonctions, jobs, ...        |

Appliquer `mig_manufacturer_name.sql` si la base a été initialisé avec le `init_old.sql`
Exécuter de nouveau les triggers des logs (pas le fichier entier sinon les tables des logs seront recréées) et le fichier des vues.

---
## INIT.SQL

### Description générale
`init.sql` construit le schéma principal de la base de données `keepit`. Il crée les tables de base qui servent tout le site web, ainsi que les comptes utiliseurs obligatoires.

### Structures importantes

- `devices`
  - table principale des équipements.
  - sert de base commune aux tables `computer` et `monitor`.

- `computer`
  - étend `devices` pour les ordinateurs.
  - `ON DELETE CASCADE` sur `devices` : lorsqu’un équipement est supprimé, la ligne `computer` associée l’est aussi.

- `monitor`
  - étend `devices` pour les écrans.
  - si l’ordinateur attaché est supprimé, `attached_to_computer` devient NULL.

### Fonction dans le site web
Cette base structure l’inventaire :
- un seul objet `devices` sert d’entité générique,
- les tables `computer` et `monitor` ajoutent des données spécifiques,
- les tables de référence fournissent les listes utilisées par les formulaires et la validation.

---

## INSERTS.SQL

### Description générale
`inserts.sql` pré-remplit les tables de références et constantes. Ce fichier apporte les premières valeurs utilisables immédiatement par l’application.

---

## LOGS_DATABASE.SQL

### Description générale
`logs_database.sql` crée le système de traçabilité. Il ajoute des tables de log et des triggers pour enregistrer chaque modification importante de la base.

### Structures importantes
- `device_logs`
  - utilisé pour afficher l’historique des changements et pour la surveillance.

- Triggers sur `devices`
  - `BEFORE DELETE` : cas particulier pour les ordinateurs ; avant suppression, le trigger journalise la perte de lien des moniteurs attachés à l’ordinateur supprimé.

- Triggers sur `computer`
  - chaque champ modifié devient une entrée de log distincte.

- Triggers sur `monitor`
  - `AFTER UPDATE` : log des changements de taille, résolution, connecteur, ordinateur attaché.

- `constant_logs`
  - journalise les modifications des tables de constantes.

### Fonction dans le site web
- fournit un historique détaillé des changements,
- permet de suivre qui a modifié quoi grâce à la variable session `@current_user`,

---

## VIEWS.SQL

### Description générale
`Views.sql` définit des vues SQL pour l’export, le tableau de bord, la recherche et les statistiques. Ce sont des requêtes prédéfinies qui simplifient l’accès aux données agrégées. Permet de solidifier les échanges avec le site. La struture de la base peut changer, la forme des donnée retourné est la même ce qui n'impacte pas le coté PHP.

### Structures importantes
- `vw_monitor_by_computer`
  - relie chaque moniteur à l’ordinateur auquel il est attaché.

- `vw_action_on_device_by_month`
  - compte les actions par mois dans `device_logs`.

- `vw_month_activity`
  - calcule l’activité mensuelle par champ modifié.
  - fournit classement, quartiles, évolution en valeur et en pourcentage.

- Vues dashboard
  - `vw_dashboard_five_devices_last_update` : les 5 appareils les plus récemment modifiés.
  - `vw_dashboard_five_users_last_connection` : les 5 derniers utilisateurs connectés.

- `vw_inventory_search_table` : table de recherche pour l’inventaire.
- `vw_export_inventaire` : export complet de l’inventaire.

### Fonction dans le site web
Ces vues servent :
- aux écrans de tableau de bord,
- aux exports CSV,
- à l’interface de recherche/inventaire.

---

## ACTION.SQL

### Description générale
`action.sql` contient les mécanismes automatiques de maintenance. Il crée des procédures stockées et un événement planifié pour gérer la suppression des logs anciens et des tests.

### Structures importantes
- `clean_inf_month_logs()`
  - procédure qui supprime les logs de plus d’un mois dans `device_logs`, `constant_logs`, `users_logs`.
  - permet de limiter la taille du journal et de répondre au normes sur la durée de stockage des données (RGPD par exemple).

- `clean_logs_daily`
  - événement MySQL planifié quotidient à 2h du matin.
  - appelle la procédure de nettoyage.

- `add_test_logs()`
  - procédure de test pour insérer une entrée de log factice.

### Fonction dans le site web
- assure la maintenance automatique du système de logs,

---