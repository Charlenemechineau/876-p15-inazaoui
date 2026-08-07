<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    // Dans ce test, je vérifie que je peux
    // enregistrer puis récupérer l'adresse e-mail d'un utilisateur.
    public function testUserEmail(): void
    {
        $user = new User();

        $user->setEmail('utilisateur@test.fr');

        $this->assertSame(
            'utilisateur@test.fr',
            $user->getEmail()
        );
    }

    // Dans ce test, je vérifie que je peux
    // enregistrer puis récupérer le nom d'un utilisateur.
    public function testUserName(): void
    {
        $user = new User();

        $user->setName('Louis de Funes');

        $this->assertSame(
            'Louis de Funes',
            $user->getName()
        );
    }

    // Dans ce test, je vérifie que je peux
    // enregistrer puis récupérer la description d'un utilisateur.
    public function testUserDescription(): void
    {
        $user = new User();

        $user->setDescription(
            'Utilisateur créé pour le test.'
        );

        $this->assertSame(
            'Utilisateur créé pour le test.',
            $user->getDescription()
        );
    }

    // Dans ce test, je vérifie que je peux
    // enregistrer puis récupérer le mot de passe d'un utilisateur.
    public function testUserPassword(): void
    {
        $user = new User();

        $user->setPassword('mot-de-passe-hashe');

        $this->assertSame(
            'mot-de-passe-hashe',
            $user->getPassword()
        );
    }

    // Dans ce test, je vérifie que je peux
    // définir un utilisateur comme administrateur.
    public function testUserIsAdmin(): void
    {
        $user = new User();

        $user->setAdmin(true);

        $this->assertTrue(
            $user->isAdmin()
        );
    }

    // Dans ce test, je vérifie qu'un nouvel utilisateur
    // n'est pas administrateur par défaut.
    public function testUserIsNotAdminByDefault(): void
    {
        $user = new User();

        $this->assertFalse(
            $user->isAdmin()
        );
    }

    // Dans ce test, je vérifie que je peux
    // bloquer un utilisateur.
    public function testUserIsBlocked(): void
    {
        $user = new User();

        $user->setBlocked(true);

        $this->assertTrue(
            $user->isBlocked()
        );
    }

    // Dans ce test, je vérifie qu'un nouvel utilisateur
    // n'est pas bloqué par défaut.
    public function testUserIsNotBlockedByDefault(): void
    {
        $user = new User();

        $this->assertFalse(
            $user->isBlocked()
        );
    }

    // Dans ce test, je vérifie qu'une administratrice
    // possède automatiquement le rôle ROLE_ADMIN.
    public function testAdminRoles(): void
    {
        $user = new User();

        $user->setAdmin(true);
        $user->setRoles([]);

        $this->assertContains(
            'ROLE_ADMIN',
            $user->getRoles()
        );
    }

    // Dans ce test, je vérifie qu'un invité
    // possède automatiquement le rôle ROLE_USER.
    public function testGuestRoles(): void
    {
        $user = new User();

        $user->setAdmin(false);
        $user->setRoles([]);

        $this->assertContains(
            'ROLE_USER',
            $user->getRoles()
        );
    }

    // Dans ce test, je vérifie que les rôles enregistrés
    // sont bien récupérés avec le rôle principal de l'utilisateur.
    public function testAdditionalRoles(): void
    {
        $user = new User();

        $user->setAdmin(false);
        $user->setRoles([
            'ROLE_PHOTOGRAPHER',
        ]);

        $this->assertContains(
            'ROLE_PHOTOGRAPHER',
            $user->getRoles()
        );

        $this->assertContains(
            'ROLE_USER',
            $user->getRoles()
        );
    }

    // Dans ce test, je vérifie que l'identifiant
    // de connexion correspond à l'adresse e-mail.
    public function testUserIdentifier(): void
    {
        $user = new User();

        $user->setEmail('utilisateur@test.fr');

        $this->assertSame(
            'utilisateur@test.fr',
            $user->getUserIdentifier()
        );
    }

    // Dans ce test, je vérifie qu'une erreur est déclenchée
    // si l'identifiant est demandé sans adresse e-mail.
    public function testUserIdentifierCannotBeEmpty(): void
    {
        $user = new User();

        $this->expectException(
            \LogicException::class
        );

        $this->expectExceptionMessage(
            'L’adresse e-mail de l’utilisateur ne peut pas être vide.'
        );

        $user->getUserIdentifier();
    }

    // Dans ce test, je vérifie que la méthode
    // eraseCredentials() peut être appelée sans erreur.
    public function testEraseCredentials(): void
    {
        $user = new User();

        $user->eraseCredentials();

        // La méthode est vide, j'ajoute donc manuellement
        // une assertion pour confirmer qu'elle a été exécutée.
        $this->addToAssertionCount(1);
    }
}