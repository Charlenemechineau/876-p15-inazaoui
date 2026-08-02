<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupération des utilisateurs créés dans UserFixtures.
        $admin = $this->getReference('user_admin', User::class);
        $activeGuest = $this->getReference('active-guest', User::class);

        // Récupération des albums créés dans AlbumFixtures.
        $vacationAlbum = $this->getReference(
            'album-vacances',
            Album::class
        );

        $familyAlbum = $this->getReference(
            'album-famille',
            Album::class
        );

        // Création d’un média appartenant à l’invité actif.
        $vacationMedia = new Media();
        $vacationMedia->setTitle('Photo de vacances');
        $vacationMedia->setPath('uploads/test-vacances.jpg');
        $vacationMedia->setUser($activeGuest);
        $vacationMedia->setAlbum($vacationAlbum);

        $manager->persist($vacationMedia);
        $this->addReference('media-vacances', $vacationMedia);

        // Création d’un média ajouté par l’administratrice.
        $familyMedia = new Media();
        $familyMedia->setTitle('Photo de famille');
        $familyMedia->setPath('uploads/test-famille.jpg');
        $familyMedia->setUser($admin);
        $familyMedia->setAlbum($familyAlbum);

        $manager->persist($familyMedia);
        $this->addReference('media-famille', $familyMedia);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AlbumFixtures::class,
        ];
    }
}