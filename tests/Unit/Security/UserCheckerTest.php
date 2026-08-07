<?php

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserCheckerTest extends TestCase
{
    // Dans ce test, je vérifie qu'un utilisateur bloqué
    // ne peut pas se connecter.
    public function testBlockedUserThrowsException(): void
    {
        $user = new User();
        $user->setBlocked(true);

        $userChecker = new UserChecker();

        $this->expectException(
            CustomUserMessageAccountStatusException::class
        );

        $this->expectExceptionMessage(
            'Votre compte est bloqué. Vous ne pouvez plus vous connecter.'
        );

        $userChecker->checkPreAuth($user);
    }

    // Dans ce test, je vérifie qu'un utilisateur actif
    // ne déclenche aucune exception.
    public function testActiveUserDoesNotThrowException(): void
    {
        $user = new User();
        $user->setBlocked(false);

        $userChecker = new UserChecker();

        $userChecker->checkPreAuth($user);

        $this->addToAssertionCount(1);
    }

    // Dans ce test, je vérifie que la méthode checkPostAuth()
    // peut être appelée sans provoquer d'erreur.
    public function testCheckPostAuthDoesNothing(): void
    {
        $user = new User();

        $userChecker = new UserChecker();

        $userChecker->checkPostAuth($user);

        $this->addToAssertionCount(1);
    }
}