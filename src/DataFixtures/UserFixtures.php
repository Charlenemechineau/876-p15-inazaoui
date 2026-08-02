<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // Création de l'administratrice Ina Zaoui.
        $admin = new User();

        $admin->setName('Ina Zaoui');
        $admin->setEmail('ina@zaoui.com');
        $admin->setAdmin(true);
        $admin->setBlocked(false);
        $admin->setRoles(['ROLE_ADMIN']);

        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                'password'
            )
        );
            $manager->persist($admin);
            $this->addReference('user_admin', $admin);

        // Création d'un invité actif.
        $activeGuest = new User();

        $activeGuest->setName('Louis de Funes');
        $activeGuest->setEmail('louis@funes.com');
        $activeGuest->setAdmin(false);
        $activeGuest->setBlocked(false);
        $activeGuest->setRoles(['ROLE_USER']);

        $activeGuest->setPassword(
            $this->passwordHasher->hashPassword(
                $activeGuest,
                'password'
            )
        );
            $manager->persist($activeGuest);
            $this->addReference('active-guest', $activeGuest);

        // Création d'un invité bloqué.
        $blockedGuest = new User();

        $blockedGuest->setName('Jean Dujardin');
        $blockedGuest->setEmail('jean@dujardin.com');
        $blockedGuest->setAdmin(false);
        $blockedGuest->setBlocked(true);
        $blockedGuest->setRoles(['ROLE_USER']);

        $blockedGuest->setPassword(
            $this->passwordHasher->hashPassword(
                $blockedGuest,
                'password'
            )
        );
            $manager->persist($blockedGuest);
            $this->addReference('blocked-guest', $blockedGuest);

            $manager->flush();
        
        
    }
    
}