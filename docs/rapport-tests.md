# Rapport de tests

## Objectif

Les tests automatisés permettent de vérifier le bon fonctionnement de l'application après sa migration et les différentes corrections apportées au projet.

Le projet contient des tests fonctionnels et des tests unitaires.

## Les tests fonctionnels vérifient

- l'authentification de l'administratrice ;
- la gestion des invités ;
- la gestion des albums ;
- la gestion des médias ;
- les différentes pages du Front Office ;
- le comportement du repository utilisateur ;
- la gestion des cas d'erreur, notamment la pagination des médias et la création d'un invité avec une adresse e-mail déjà utilisée.

## Les tests unitaires vérifient

- l'entité Album ;
- l'entité Media ;
- l'entité User ;
- le UserChecker.

## Résultat des tests automatisés

La suite complète de tests a été exécutée avec PHPUnit 9.6.19.

- Tests exécutés : 52
- Assertions : 161
- Tests réussis : 52
- Échecs : 0

Résultat :

```text
OK (52 tests, 161 assertions)
```

## Couverture de code

Le rapport de couverture a été généré avec PHPUnit et Xdebug.

Les résultats obtenus sont :

- Classes : 47,37 % (9/19)
- Méthodes et fonctions : 78,87 % (56/71)
- Lignes : 70,79 % (269/380)

La couverture des lignes atteint donc 70,79 %.

Le rapport HTML complet est disponible dans :

```text
coverage/
```

La page principale du rapport est :

```text
coverage/index.html
```

## Commandes utilisées

### Exécution des tests

```bash
php bin/phpunit
```

### Génération du rapport de couverture HTML

Sous Linux ou macOS :

```bash
XDEBUG_MODE=coverage php bin/phpunit --coverage-html coverage
```

Sous Windows PowerShell :

```powershell
$env:XDEBUG_MODE="coverage"
php bin/phpunit --coverage-html coverage
```