# Rapport de tests

## Objectif

Les tests automatisés permettent de vérifier le bon fonctionnement de l'application après sa migration et les différentes corrections apportées au projet.

Le projet contient des tests fonctionnels et des tests unitaires.

## Les tests fonctionnels vérifient :
- l'authentification de l'administratrice ;
- la gestion des invités ;
- la gestion des albums ;
- la gestion des médias ;
- les différentes pages du Front Office ;
- le comportement du repository utilisateur.

## Les tests unitaires vérifient :
- l'entité Album ;
- l'entité Media ;
- l'entité User ;
- le UserChecker.

## Résultat des tests automatisés

- Tests exécutés : 50
- Assertions : 150
- Tests réussis : 50
- Échecs : 0

Résultat :

OK (50 tests, 150 assertions)

## Couverture de code

Rapport généré avec PHPUnit 9.6.19 et Xdebug 3.3.2.

- Classes : 52,63 % (10/19)
- Méthodes : 79,71 % (55/69)
- Lignes : 70,43 % (262/372)

La couverture de code des lignes atteint donc le seuil minimum de 70 % demandé.

## Commandes utilisées

Exécution des tests :

php bin/phpunit

Génération du rapport de couverture :

php bin/phpunit --coverage-text