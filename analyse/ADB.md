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
| Matériel              | Comprend les Unités Centrales et les Ecrans                                                                                |
|                       |                                                                                                                            |

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

# Cas d’utilisation

## Cas d’utilisation métier

### Enregistrer une machine (Formulaire)

**Nom :** Enregistrer une machine par formulaire

**Niveau :** Utilisateur

**Description :** Le technicien entre une nouvelle machine dans le système en renseignant les informations dans une formulaire.

**Acteur principal :** Technicien

#### Scénario Nominal

1. Dans l'inventaire, choisir l'action "ajouté" pour arrivé à la page de formulaire.

2. Saisir les différents champs du formulaires.

3. Cliquer sur le bouton validé.

4. Le système enregistre les informations.

5. Un message de confirmation est affiché.

#### Extensions

- 4.1) Les informations sont incorrectes, exemple : numéro de série déjà existant (doublon dans la base)

- 4.2) Le système affiche un message stipulant que les informations sont erronées

- 4.3) L'utilisateur peut retenter : *Retour Etape 2*



- 4.1) Un champ est manquant (vide)

- 4.2) Le système affiche un message stipulant qu'une information est manquante

- 4.3) Le système garde les informations dans le formulaire et indique le champ à renseigner : *Retour Etape 2*

### Enregistrer des machines (CSV)

**Nom :** Enregistrement de machines à partir d'un fichier CSV

**Niveau :** Utilisateur

**Description :** Le technicien importe un fichier CSV des informations de plusieurs machines pour les enregistrer

**Acteur principal :** Technicien

#### Scénario Nominal

1. Dans l'inventaire, choisir l'action "importé" pour arrivé à la page de formulaire.

2. Cliquer sur le bouton central pour ouvrir l'explorateur de fichier.

3. L'utilisateur sélectionne le fichier.

4. Le fichier est chargé dans le système.

5. Un message de confirmation indique le nombre d'appareil enregistré.

#### Extensions

- 3.1) Le fichier sélectionné est invalide, ce n'est pas un csv (fichier pdf par exemple).

- 3.2) Affichage d'un message d'erreur adéquate.

- 3.3) *Retour étape 2*



- 4.1) La structure du fichier est erroné.

- 4.2) Un message d'erreur est affiché.

- 4.3) *Retour à 2*



- 4.1) Des informations sont erronées (doublons, vide).

- 4.2) Les appareils (lignes d'informations) correctes sont bien ajouté.

- 4.3) Un message d'erreur indique les ligne ignoré du faite de données non-valide.

### Se connecter

**Nom :** S'identifier

**Niveau :** Sous-fonction

**Description :** S'identifier pour ne plus être considérer comme simple visiteur et avoir accès à aux fonctionnalité de son profil

**Acteur principal :** Technicien/Administrateur web/Administrateur système

#### Scénario Nominal

1. Accéder à la page de connexion par l'icone en haut à droite.

2. Saisir le login et mot de passe.

3. Cliquer sur le bouton "Se connecter".

4. Le système vérifie les informations.

5. L'utilisateur est rediriger vers la page de son profil.

#### Extensions

- 4.1) Les informations sont incorrectes.

- 4.2) Le système affiche un message stipulant que les informations sont erronées.

- 4.3) L'utilisateur peut retenter : *Retour Etape 2*

- 4.1) Connexion avec la base de données échoue.

- 4.2) Le système affiche un message stipulant qu'une erreur est survenue.

- 4.3) Le système propose de poursuivre en tant que visiteur.

### Modifier une information matérielle

**Nom :** Modification d'une information matérielle

**Niveau :** Utilisateur

**Description :** Le technicien modifie une information de l'élément matériel

**Acteur principal :** Technicien

#### Scénario Nominal

- Le technicien clique dans la barre de navigation sur inventaire.

- Le technicien clique sur le bouton "action" le l'appareil choisi.

- Il modifie une/plusieurs des informations sur la page de l'appareil.

- Il clique sur le bouton "Enregistrer".

- Le système enregistre la modification.

- L'utilisateur est redirigé vers la page de l'inventaire.

#### Extensions

### Exporter une liste (CSV)

**Nom :**

**Niveau :**

**Description :**

**Acteur principal :**

#### Scénario Nominal

#### Extensions

### Gérer les techniciens

**Nom :**

**Niveau :**

**Description :**

**Acteur principal :**

#### Scénario Nominal

#### Extensions

### Créer une information matérielle

**Nom :**

**Niveau :**

**Description :**

**Acteur principal :**

#### Scénario Nominal

#### Extensions

# 
