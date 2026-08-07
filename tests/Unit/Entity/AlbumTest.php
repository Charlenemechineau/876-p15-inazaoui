<?php


namespace App\Tests\Unit\Entity;

use App\Entity\Album;
use PHPUnit\Framework\TestCase;

final class AlbumTest extends TestCase
{
    // Dans ce test, je vérifie que je peux
    // enregistrer puis récupérer le nom d'un album.
    public function testAlbumName(): void
    {
        // Je crée un nouvel album.
        $album = new Album();

        // Je lui donne un nom.
        $album->setName('Vacances');

        // Je vérifie que le nom récupéré
        // correspond bien au nom enregistré.
        $this->assertSame(
            'Vacances',
            $album->getName()
        );
    }

    // Dans ce test, je vérifie que je peux
    // modifier le nom d'un album.
    public function testAlbumNameCanBeModified(): void
    {
        $album = new Album();

        // Je donne un premier nom à l'album.
        $album->setName('Vacances');

        // Je remplace ensuite ce nom.
        $album->setName('Famille');

        // Je vérifie que le nouveau nom est bien enregistré.
        $this->assertSame(
            'Famille',
            $album->getName()
        );
    }
}

