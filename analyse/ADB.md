# Introduction

## Objectif du document

Ce document est une **Analyse Des Besoins** (ADB). Il a pour but de clarifier et détaillé les exigences **fonctionnelles** et **non fonctionnelles** nécessaire à la réalisation d'une plateforme de **gestion d'un parc informatique**. Il est **basé sur le Cahier Des Charges** (CDC) se trouvant dans le même répertoire.

## Portée du système


| Inclus                     | Exclus                                                            |
| -------------------------- | ----------------------------------------------------------------- |
| Suivi de Moniteurs et d'UC | Suivi d'autres matériels (switchs, imprimantes, téléphones fixes) |

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
| Logs                  | Fichier texte enregistrant chronologiquement toutes les activités, événements, ou erreurs du système.                      |
| SGBD                  | Logiciel permettant de créer, gérer et interroger les bases de données où sont stockées toutes les informations du projet. |
| Inventaire            | Liste détaillée et mise à jour de tous les actifs du parc informatique géré par le système.                                |
| Actif                 | Elément géré par le système (ici soit des UC soit des moniteurs)                                                           |
| Rebut                 | Retirer définitivement un actif de l'inventaire du parc                                                                    |

# Besoins fonctionnelles

# Besoins non fonctionnelles
