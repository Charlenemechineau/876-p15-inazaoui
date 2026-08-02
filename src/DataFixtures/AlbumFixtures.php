<?php

namespace App\DataFixtures;

use App\Entity\Album;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AlbumFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création du premier album.
        $firstAlbum = new Album();
        $firstAlbum->setName('Vacances');

        $manager->persist($firstAlbum);
        $this->addReference('album-vacances', $firstAlbum);

        // Création du second album.
        $secondAlbum = new Album();
        $secondAlbum->setName('Famille');

        $manager->persist($secondAlbum);
        $this->addReference('album-famille', $secondAlbum);

        $manager->flush();
    }
}