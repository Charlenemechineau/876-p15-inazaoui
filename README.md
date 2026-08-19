# Ina Zaoui

## Présentation du projet

Ce projet a été réalisé dans le cadre du parcours **Développeur d'application PHP/Symfony d'OpenClassrooms**.

L'application permet de présenter le travail de la photographe Ina Zaoui à travers son portfolio et de gérer les différents contenus du site depuis un espace d'administration.

Le projet existant a été repris afin de le faire évoluer. Le travail réalisé comprend notamment :

- la migration de l'application vers Symfony 6.4 ;
- la correction de différentes anomalies ;
- l'amélioration de la sécurité ;
- la mise en place de la gestion des invités ;
- l'ajout de tests unitaires et fonctionnels ;
- l'analyse et l'optimisation des performances ;
- la mise en place d'une intégration continue.

## Fonctionnalités principales

### Front Office

La partie publique de l'application permet notamment :

- de consulter la page d'accueil ;
- de consulter le portfolio ;
- de consulter les invités ;
- d'accéder aux médias associés aux invités ;
- de se connecter à l'application.

### Back Office

L'espace d'administration permet notamment :

- de gérer les invités ;
- d'ajouter et modifier les informations d'un invité ;
- de gérer les médias ;
- de gérer les albums ;
- d'associer les différents contenus de l'application.

L'accès au Back Office est réservé à l'administrateur.

## Environnement technique

Le projet utilise principalement :

- PHP 8.1 ou supérieur ;
- Symfony 6.4 ;
- Doctrine ORM ;
- Doctrine Migrations ;
- PostgreSQL ;
- Twig ;
- Bootstrap ;
- PHPUnit ;
- PHPStan ;
- Git / GitHub.

## Prérequis

Avant d'installer le projet, il est nécessaire de disposer de :

- PHP 8.1 ou supérieur ;
- Composer ;
- PostgreSQL ;
- Git ;
- Symfony CLI (optionnel).

## Installation

### 1. Cloner le repository

```bash
git clone https://github.com/Charlenemechineau/876-p15-inazaoui.git
cd 876-p15-inazaoui
```

### 2. Installer les dépendances

Installer les dépendances PHP avec Composer :

```bash
composer install
```

### 3. Configurer l'environnement

Créer un fichier `.env.local` à partir du fichier `.env`.

Sous Linux ou macOS :

```bash
cp .env .env.local
```

Sous Windows PowerShell :

```powershell
Copy-Item .env .env.local
```

Configurer ensuite la connexion à PostgreSQL dans la variable `DATABASE_URL` du fichier `.env.local`.

Exemple :

```dotenv
DATABASE_URL="postgresql://utilisateur:mot_de_passe@127.0.0.1:5432/ina_zaoui"
```

Les informations personnelles de connexion à la base de données ne doivent pas être ajoutées au repository Git.

## Initialisation de la base de données

### Créer la base

Une fois la connexion PostgreSQL configurée, créer la base de données :

```bash
php bin/console doctrine:database:create
```

### Appliquer les migrations

Les migrations présentes dans le projet peuvent être appliquées avec :

```bash
php bin/console doctrine:migrations:migrate
```

## Importer les données

Afin de faciliter l'installation et les tests de l'application, les dumps SQL anonymisés sont directement disponibles dans le dossier :

```text
database/
```

Il contient :

```text
database/
├── album.sql
├── media.sql
└── user.sql
```

Ces fichiers permettent de restaurer les données utilisées par l'application.

Ils peuvent être importés dans PostgreSQL à l'aide de `psql`.

Exemple :

```bash
psql -U <utilisateur> -d ina_zaoui -f database/user.sql
psql -U <utilisateur> -d ina_zaoui -f database/album.sql
psql -U <utilisateur> -d ina_zaoui -f database/media.sql
```

Remplacer `<utilisateur>` par le nom de votre utilisateur PostgreSQL.

## Médias

Les médias d'origine utilisés par l'application ont été fournis séparément dans les ressources du projet OpenClassrooms.

Ils doivent être placés dans le dossier :

```text
public/uploads/
```

Les médias ne sont pas inclus directement dans ce repository en raison de leur volume important.

Une étude d'optimisation des images a également été réalisée afin de réduire leur poids. Les images d'origine étant majoritairement en 1920 × 1080, des essais de redimensionnement et de recompression ont été effectués afin d'améliorer leur poids tout en conservant une qualité visuelle satisfaisante.

## Lancer l'application

Avec Symfony CLI :

```bash
symfony server:start
```

L'adresse du serveur local est ensuite indiquée dans le terminal.

Pour arrêter le serveur :

```bash
symfony server:stop
```

## Compte administrateur

Pour se connecter avec le compte administrateur d'Ina, utiliser les identifiants présents dans le jeu de données fourni avec le projet.

Une fois connecté, l'administrateur peut accéder aux fonctionnalités de gestion des invités, des médias et des albums.

## Fonctionnement général du code

L'application respecte l'organisation classique d'un projet Symfony.

### Contrôleurs

Les contrôleurs sont disponibles dans :

```text
src/Controller/
```

Ils reçoivent les requêtes HTTP, appellent les services ou repositories nécessaires et transmettent les données aux templates Twig.

Ils permettent notamment de gérer :

- les pages publiques ;
- l'authentification ;
- les invités ;
- les médias ;
- les albums ;
- les pages d'administration.

### Entités

Les entités Doctrine sont disponibles dans :

```text
src/Entity/
```

Elles représentent les principales données manipulées par l'application, notamment les utilisateurs, les médias et les albums.

Les relations entre les entités permettent d'associer les médias aux différents contenus de l'application.

### Repositories

Les repositories se trouvent dans :

```text
src/Repository/
```

Ils permettent de centraliser les requêtes vers la base de données réalisées avec Doctrine.

Certaines requêtes ont été adaptées afin d'améliorer les performances de l'application, notamment lors de la récupération des invités et de leurs médias.

### Formulaires

Les formulaires Symfony sont regroupés dans :

```text
src/Form/
```

Ils permettent de gérer la saisie et la validation des données lors de la création ou de la modification des différents contenus.

### Templates

Les vues de l'application sont réalisées avec Twig et sont disponibles dans :

```text
templates/
```

Elles sont séparées selon les différentes parties et fonctionnalités de l'application.

### Sécurité

L'authentification et les autorisations sont gérées avec le composant Security de Symfony.

L'accès aux fonctionnalités d'administration est limité aux utilisateurs disposant des droits nécessaires.

Des vérifications supplémentaires ont également été mises en place afin de sécuriser les différentes actions accessibles depuis le Back Office.

### Gestion des médias

Les médias sont associés aux différentes données de l'application et leurs fichiers sont stockés dans :

```text
public/uploads/
```

Les informations permettant de retrouver ces fichiers sont enregistrées en base de données.

## Tests

Le projet contient des tests unitaires et fonctionnels réalisés avec PHPUnit.

Pour lancer l'ensemble des tests :

```bash
php bin/phpunit
```

### Tests unitaires

Les tests unitaires permettent notamment de vérifier le comportement des entités et de certains composants de l'application.

Ils sont disponibles dans :

```text
tests/Unit/
```

### Tests fonctionnels

Les tests fonctionnels permettent de vérifier le comportement de plusieurs fonctionnalités de l'application en simulant des requêtes HTTP.

Ils sont disponibles dans :

```text
tests/Functional/
```

Ils couvrent notamment plusieurs fonctionnalités liées à :

- l'authentification ;
- la gestion des invités ;
- la gestion des médias ;
- la gestion des albums.

### Couverture de code

Un rapport de couverture de code HTML a été généré avec PHPUnit et Xdebug.

La suite de tests contient actuellement :

- 53 tests ;
- 167 assertions ;
- 100 % des tests passent.

La couverture obtenue est de :

- 70,79 % des lignes ;
- 78,87 % des méthodes et fonctions ;
- 47,37 % des classes.

Le rapport HTML complet est disponible dans :

```text
coverage/
```

Pour consulter le rapport, ouvrir le fichier :

```text
coverage/index.html
```

Pour régénérer la couverture avec Xdebug sous Linux ou macOS :

```bash
XDEBUG_MODE=coverage php bin/phpunit --coverage-html coverage
```

Sous Windows PowerShell :

```powershell
$env:XDEBUG_MODE="coverage"
php bin/phpunit --coverage-html coverage
```

## Performances

Les performances de l'application ont été analysées à l'aide de deux outils :

- le Profiler Symfony ;
- Lighthouse.

L'analyse a porté notamment sur :

- le temps de chargement des pages ;
- le nombre de requêtes SQL ;
- le temps consacré aux requêtes SQL ;
- la consommation mémoire ;
- les indicateurs de performance remontés par Lighthouse.

Cette analyse a permis d'identifier un problème de type **N+1** sur la page des invités.

La récupération des invités et de leurs médias a été optimisée avec une requête adaptée dans le repository.

Le nombre de requêtes SQL observé sur cette page est ainsi passé de :

```text
103 requêtes → 1 requête
```

Le temps SQL mesuré est également passé de :

```text
160,68 ms → 14,67 ms
```

Le rapport complet est disponible dans :

```text
docs/Rapport_de_performance.pdf
```

## Documentation des tests

Une documentation complémentaire concernant les tests réalisés sur l'application est disponible dans :

```text
docs/rapport-tests.md
```

Elle présente les tests mis en place et les différentes fonctionnalités vérifiées.

## Contribution

Les règles à respecter pour contribuer au projet sont détaillées dans le fichier :

```text
CONTRIBUTING.md
```

Ce document présente notamment :

- l'installation de l'environnement de développement ;
- la création des branches ;
- les conventions de nommage ;
- les conventions de commits ;
- les bonnes pratiques de développement ;
- l'exécution des tests ;
- le fonctionnement des Pull Requests.

## Intégration continue

Une intégration continue est mise en place avec GitHub Actions afin de vérifier automatiquement la qualité du projet lors des évolutions du code.

Elle permet notamment d'exécuter automatiquement les vérifications définies pour le projet.

## Structure principale du projet

```text
876-p15-inazaoui/
├── config/             Configuration Symfony
├── coverage/           Rapport HTML de couverture PHPUnit
├── database/           Dumps SQL anonymisés
├── docs/               Documentation et rapports
├── migrations/         Migrations Doctrine
├── public/             Fichiers publics et point d'entrée
├── src/
│   ├── Controller/     Contrôleurs
│   ├── Entity/         Entités Doctrine
│   ├── Form/           Formulaires Symfony
│   └── Repository/     Repositories Doctrine
├── templates/          Templates Twig
├── tests/              Tests unitaires et fonctionnels
├── CONTRIBUTING.md     Guide de contribution
├── README.md           Documentation principale
└── composer.json       Dépendances PHP
```

## Documentation complémentaire

Les documents réalisés dans le cadre du projet sont regroupés dans le dossier :

```text
docs/
```

Ce dossier contient notamment :

- le rapport des tests ;
- le rapport de performance.

## Cadre du projet

Ce projet a été réalisé dans le cadre du parcours **Développeur d'application PHP/Symfony d'OpenClassrooms**.