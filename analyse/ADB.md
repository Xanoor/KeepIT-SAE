# Introduction

## Objectif du document

Ce document est une **Analyse Des Besoins** (ADB). Il a pour but de clarifier et détaillé les exigences **fonctionnelles** et **non fonctionnelles** nécessaire à la réalisation d'une plateforme web de **gestion d'un parc informatique**. Il est **basé sur le Cahier Des Charges** (CDC) se trouvant dans le même répertoire.

## Portée du système

| Inclus                                        | Exclus                                                              |
| --------------------------------------------- | ------------------------------------------------------------------- |
| Suivi de Moniteurs et d'UC                    | Suivi d'autres matériels (switchs, imprimantes, téléphones fixes)   |
| Ajouts, suppressions, modifications d'actifs  | Gestion financière du prix des actifs (amortissement, prix d'achat) |
| Traçabilité des actions (logs/journalisation) | Système de ticketing                                                |
| Gestion des comptes par l'administrateur web  |                                                                     |

## Intervenants

- Clients :

 1. Les professeurs

- Utilisateurs :

 1. Administrateur système : accède aux journaux d'activités (logs)
 2. Administrateur Web : Gère les techniciens et crée des informations
 3. Technicien : Ajoute, modifie et supprime des machines de l'inventaire
 4. Visiteur : Visualise une partie de l'inventaire

# Glossaire des termes

| Terme                 | Définition                                                                                                                 |
| --------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| UC (Unités Centrales) | élément principal qui contient tous les composants essentiels de l'ordinateur                                              |
| Moniteur              | Périphérique servant d'affichage à l'ordinateur (écran)                                                                    |
| Utilisateur           | Toute personne accédant au site                                                                                            |
| Visiteur              | Utilisateur qui ne s'est pas authentifié                                                                                   |
| CSV                   | Format de fichier texte qui stocke les données sous forme de tableau                                                       |
| Log                   | Fichier texte enregistrant chronologiquement toutes les activités, événements, ou erreurs du système.                      |
| SGBD                  | Logiciel permettant de créer, gérer et interroger les bases de données où sont stockées toutes les informations du projet. |
| Inventaire            | Liste détaillée et mise à jour de tous les actifs du parc informatique géré par le système.                                |
| Actif                 | Elément géré par le système (ici soit des UC soit des moniteurs)                                                           |
| Rebut                 | Retirer définitivement un actif de l'inventaire du parc et le placer dans une liste spécifique                             |

# Besoins fonctionnelles

## Exigences concernant la connexion à la plateforme

| Exigences                                                                                                 | Utilisateurs concernés                                  |
| --------------------------------------------------------------------------------------------------------- | ------------------------------------------------------- |
| Le système doit permettre l'authentification des utilisateurs                                             | Techniciens, Administrateur Web, Administrateur Système |
| Le visiteur doit pouvoir consulter l'inventaire (en partie)                                               | Visiteur                                                |
| L'administrateur Web doit avoir une interface le permettant de créer et de supprimer un compte Technicien | Administrateur Web                                      |
| L'administrateur Web doit pouvoir définir le mot de passe d'un compte Technicien                          | Administrateur Web                                      |

## Exigences concernant la gestion du matériel

| Exigences                                                                                         | Utilisateurs concernés |
| ------------------------------------------------------------------------------------------------- | ---------------------- |
| Le technicien doit pouvoir ajouter, supprimer ou modifier un actif                                | Technicien             |
| La suppression d'un actif doit l'ajouter au rebut                                                 | Technicien             |
| L'administrateur Web doit pouvoir créer de nouvelles informations (utilisables par le technicien) | Administrateur Web     |
| Il doit être possible de bloquer temporairement la liste du rebut                                 | Administrateur Web     |

## Exigences générales

| Exigences                                                                   | Utilisateurs concernés         |
| --------------------------------------------------------------------------- | ------------------------------ |
| Possibilité d'exporter/importer les données existantes dans un fichier .csv | Administrateur Web, Technicien |
| Le système doit afficher l'inventaire sous forme de liste                   | Technicien                     |

# Besoins non fonctionnelles

| Exigences                                                                  | Catégorie       |
| -------------------------------------------------------------------------- | --------------- |
| Utilisation d'une base MySQL et de fichiers csv                            | Base de données |
| Utilisation de HTML, de CSS, de PHP                                        | Langage         |
| Le site doit être accessible depuis un serveur apache sur un Raspberry Pi4 | Support         |
| Le projet est versionné et consultable sur GitHub                          | Documentation   |
