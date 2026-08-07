<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class MediaTest extends TestCase
{
    // Dans ce test, je vérifie que je peux
    // enregistrer puis récupérer le titre d'un média.
    public function testMediaTitle(): void
    {
        // Je crée un nouveau média.
        $media = new Media();

        // Je lui donne un titre.
        $media->setTitle('Horizon');

        // Je vérifie que le titre récupéré
        // correspond bien au titre enregistré.
        $this->assertSame(
            'Horizon',
            $media->getTitle()
        );
    }

    // Dans ce test, je vérifie que je peux
    // enregistrer puis récupérer le chemin d'un média.
    public function testMediaPath(): void
    {
        // Je crée un nouveau média.
        $media = new Media();

        // Je lui donne un chemin.
        $media->setPath('/images/horizon.jpg');

        // Je vérifie que le chemin récupéré
        // correspond bien au chemin enregistré.
        $this->assertSame(
            '/images/horizon.jpg',
            $media->getPath()
        );
    }

    // Dans ce test, je vérifie que je peux
    // associer un utilisateur à un média.
    public function testMediaUser(): void
    {
        // Je crée un nouveau média
        // et un utilisateur.
        $media = new Media();
        $user = new User();

        // J'associe l'utilisateur au média.
        $media->setUser($user);

        // Je vérifie que l'utilisateur récupéré
        // correspond bien à celui qui a été associé.
        $this->assertSame(
            $user,
            $media->getUser()
        );
    }

    // Dans ce test, je vérifie que je peux
    // associer un album à un média.
    public function testMediaAlbum(): void
    {
        // Je crée un nouveau média
        // et un album.
        $media = new Media();
        $album = new Album();

        // J'associe l'album au média.
        $media->setAlbum($album);

        // Je vérifie que l'album récupéré
        // correspond bien à celui qui a été associé.
        $this->assertSame(
            $album,
            $media->getAlbum()
        );
    }

    // Dans ce test, je vérifie que je peux
    // associer un fichier uploadé à un média.
    public function testMediaFile(): void
    {
        // Je crée un nouveau média.
        $media = new Media();

        // Je crée un faux fichier uploadé.
        $file = $this->createMock(UploadedFile::class);

        // J'associe le fichier au média.
        $media->setFile($file);

        // Je vérifie que le fichier récupéré
        // correspond bien à celui qui a été associé.
        $this->assertSame(
            $file,
            $media->getFile()
        );
    }
}