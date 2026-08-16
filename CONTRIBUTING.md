# Guide de contribution

Merci de contribuer à ce projet.

Ce document présente les bonnes pratiques et les conventions à suivre afin de garder un code propre, cohérent et facile à maintenir.

Pour l'installation et la configuration du projet, consultez le [README.md](README.md).

## 1. Bonnes pratiques Git

Chaque nouvelle fonctionnalité ou correction doit être réalisée dans une branche dédiée.

Il ne faut pas développer directement sur la branche `main`.

### Créer une branche

Avant de commencer une modification, vérifier que la branche `main` est à jour :

```bash
git switch main
git pull
```

Créer ensuite une nouvelle branche :

```bash
git switch -c feat/nom-de-la-feature
```

Le nom de la branche doit permettre de comprendre rapidement son objectif.

Exemples :

```text
feat/gestion-invites
feat/tests
feat/performance
fix/correction-anomalies
```

### Commits

Les commits doivent être clairs et correspondre à une modification précise.

Les principaux préfixes utilisés dans le projet sont :

- `feat:` ajout d'une fonctionnalité
- `fix:` correction d'une anomalie
- `test:` ajout ou modification de tests
- `perf:` amélioration des performances
- `docs:` ajout ou modification de documentation

Exemple :

```bash
git add .
git commit -m "feat: ajout de la gestion des invités"
```

Avant chaque commit, vérifier les fichiers modifiés :

```bash
git status
```

## 2. Conventions de code

Le projet suit les standards de codage PSR-12.

Quelques règles sont à respecter afin de conserver un code lisible et cohérent :

- utiliser des noms de classes, méthodes et variables explicites ;
- respecter l'organisation et l'architecture existantes du projet ;
- utiliser une indentation de 4 espaces ;
- éviter les méthodes inutilement longues ;
- éviter la duplication de code lorsque celui-ci peut être réutilisé ;
- ajouter des commentaires uniquement lorsqu'ils apportent une information utile à la compréhension du code ;
- supprimer le code mort ou devenu inutile avant d'effectuer un commit.

Les nouvelles fonctionnalités doivent être intégrées en respectant le fonctionnement déjà présent dans l'application.

## 3. Tests

Avant de proposer une modification, il est important de vérifier que les tests automatisés passent toujours.

### Lancer l'ensemble des tests

```bash
php bin/phpunit
```

### Lancer uniquement les tests fonctionnels

```bash
php bin/phpunit tests/Functional
```

### Lancer uniquement les tests unitaires

```bash
php bin/phpunit tests/Unit
```

Lorsqu'une nouvelle fonctionnalité est ajoutée ou qu'un comportement important est modifié, des tests doivent être ajoutés ou adaptés lorsque cela est nécessaire.

Avant de créer une Pull Request, l'ensemble de la suite de tests doit être exécuté afin de vérifier qu'aucune régression n'a été introduite.

## 4. Pull Request

Une fois le développement terminé et testé, la branche peut être proposée pour intégration dans `main` à l'aide d'une Pull Request.

Avant de créer une Pull Request :

- vérifier que le projet fonctionne correctement en local ;
- vérifier que les tests unitaires et fonctionnels passent ;
- vérifier les fichiers modifiés avec `git status` ;
- vérifier que la branche contient uniquement les modifications liées à la fonctionnalité ou au correctif concerné ;
- rédiger une description claire des modifications réalisées.

Une Pull Request doit rester centrée sur une fonctionnalité ou une correction afin de faciliter sa compréhension et sa relecture.

En cas de remarques pendant la revue de code, les corrections doivent être effectuées sur la même branche puis ajoutées à la Pull Request.

## 5. Organisation du projet

Lors d'une contribution, il est important de respecter l'organisation existante du projet.

Les principaux dossiers sont :

```text
config/       Configuration de Symfony et des bundles
docs/         Rapports et documentation complémentaire
migrations/   Migrations de la base de données
public/       Point d'entrée de l'application et ressources publiques
src/          Code source de l'application
templates/    Templates Twig
tests/        Tests unitaires et fonctionnels
```

Les nouveaux fichiers doivent être ajoutés dans le dossier correspondant à leur rôle.

## 6. Documentation

Lorsqu'une modification change le fonctionnement, l'installation ou l'utilisation de l'application, la documentation doit également être mise à jour.

Les principaux documents sont :

- `README.md` : présentation, installation et utilisation du projet ;
- `CONTRIBUTING.md` : règles et bonnes pratiques pour contribuer ;
- `docs/` : rapports et documentation complémentaire.

## 7. Avant de proposer une contribution

Avant de terminer une contribution, vérifier les points suivants :

- le projet fonctionne correctement en local ;
- le code respecte l'organisation existante ;
- aucun fichier ou code inutile n'a été ajouté ;
- les tests unitaires et fonctionnels passent ;
- les nouvelles fonctionnalités sont testées lorsque cela est nécessaire ;
- aucune information sensible n'est présente dans les fichiers suivis par Git ;
- la documentation a été mise à jour si le fonctionnement du projet a changé ;
- les commits sont clairs et compréhensibles.

## 8. Cadre du projet

Ce projet a été réalisé dans le cadre de mon parcours  **Développeur d'Applications PHP/Symfony d'OpenClassrooms**.