<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryTest extends KernelTestCase
{
    // Dans ce test, je vérifie que le repository
    // permet de mettre à jour le mot de passe d'un utilisateur.
    public function testUpgradePassword(): void
    {
        self::bootKernel();

        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $user = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $this->assertNotNull($user);

        // Je garde l'ancien mot de passe
        // pour remettre la base dans son état initial après le test.
        $oldPassword = $user->getPassword();

        $this->assertNotNull($oldPassword);

        $newHashedPassword = 'nouveau-mot-de-passe-hashe';

        $userRepository->upgradePassword(
            $user,
            $newHashedPassword
        );

        $this->assertSame(
            $newHashedPassword,
            $user->getPassword()
        );

        // Je remets le mot de passe d'origine
        // pour ne pas perturber les autres tests.
        $userRepository->upgradePassword(
            $user,
            $oldPassword
        );
    }
}