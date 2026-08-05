<?php


namespace App\Tests\Functional;

use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class MediaControllerTest extends WebTestCase
{
    // Dans ce test, je vérifie que l'administratrice
    // peut accéder à la liste des médias.
    public function testAdminCanAccessMediaList(): void
    {
        // Je crée un client pour simuler la navigation sur le site.
        $client = static::createClient();

        // Je récupère l'administratrice dans la base de test.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        // Je vérifie que l'administratrice existe bien.
        $this->assertNotNull($admin);

        // Je connecte directement l'administratrice.
        $client->loginUser($admin);

        // J'ouvre la page de gestion des médias.
        $client->request('GET', '/admin/media');

        // Je vérifie que la page s'affiche correctement.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'main h1',
            'Medias'
        );
    }

    // Dans ce test, je vérifie que l'administratrice
    // peut ajouter un nouveau média.
    public function testAdminCanCreateMedia(): void
    {
        $client = static::createClient();

        // Je récupère les repositories dont j'ai besoin.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $albumRepository = static::getContainer()
            ->get(AlbumRepository::class);

        // Je récupère l'administratrice et un album.
        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $album = $albumRepository->findOneBy([]);

        $this->assertNotNull($admin);
        $this->assertNotNull($album);

        // Je connecte directement l'administratrice.
        $client->loginUser($admin);

        // J'ouvre le formulaire d'ajout d'un média.
        $client->request('GET', '/admin/media/add');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'main h1',
            'Medias'
        );

        // Je crée un titre unique pour pouvoir relancer le test.
        $mediaTitle = sprintf(
            'Média de test %s',
            uniqid()
        );

        // Je crée une petite image temporaire
        // qui sera utilisée dans le formulaire.
        $uploadedFile = $this->createTestImage();

        // Je remplis et je soumets le formulaire.
        $client->submitForm('Ajouter', [
            'media[user]' => (string)$admin->getId(),
            'media[album]' => (string)$album->getId(),
            'media[title]' => $mediaTitle,
            'media[file]' => $uploadedFile,
        ]);

        // Je vérifie que je suis redirigée vers la liste.
        $this->assertResponseRedirects('/admin/media');

        $client->followRedirect();

        // Je vérifie que le nouveau média apparaît.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'body',
            $mediaTitle
        );
    }

    // Dans ce test, je vérifie que l'administratrice
    // peut supprimer un média.
    public function testAdminCanDeleteMedia(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $albumRepository = static::getContainer()
            ->get(AlbumRepository::class);

        $mediaRepository = static::getContainer()
            ->get(MediaRepository::class);

        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $album = $albumRepository->findOneBy([]);

        $this->assertNotNull($admin);
        $this->assertNotNull($album);

        $client->loginUser($admin);

        // Je crée d'abord un média uniquement destiné
        // à être supprimé pendant ce test.
        $client->request('GET', '/admin/media/add');

        $mediaTitle = sprintf(
            'Média à supprimer %s',
            uniqid()
        );

        $client->submitForm('Ajouter', [
            'media[user]' => (string)$admin->getId(),
            'media[album]' => (string)$album->getId(),
            'media[title]' => $mediaTitle,
            'media[file]' => $this->createTestImage(),
        ]);

        $this->assertResponseRedirects('/admin/media');

        $client->followRedirect();

        // Je récupère le média créé pour connaître son identifiant.
        $media = $mediaRepository->findOneBy([
            'title' => $mediaTitle,
        ]);

        $this->assertNotNull($media);

        // J'appelle la route de suppression.
        $client->request(
            'GET',
            '/admin/media/delete/' . $media->getId()
        );

        $this->assertResponseRedirects('/admin/media');

        $client->followRedirect();

        // Je vérifie que le média n'apparaît plus dans la liste.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains(
            'body',
            $mediaTitle
        );
    }

    // Dans ce test, je vérifie qu'un invité
    // ne peut pas supprimer le média d'un autre utilisateur.
    public function testGuestCannotDeleteAnotherUserMedia(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $mediaRepository = static::getContainer()
            ->get(MediaRepository::class);

        $guest = $userRepository->findOneBy([
            'email' => 'louis@funes.com',
        ]);

        // Je récupère un média qui n'appartient pas à Louis.
        $media = $mediaRepository->findOneBy([
            'user' => $userRepository->findOneBy([
                'email' => 'ina@zaoui.com',
            ]),
        ]);

        $this->assertNotNull($guest);
        $this->assertNotNull($media);

        // Je connecte l'invité actif.
        $client->loginUser($guest);

        // Il tente de supprimer le média d'un autre utilisateur.
        $client->request(
            'GET',
            '/admin/media/delete/' . $media->getId()
        );

        // Je vérifie que Symfony refuse l'accès.
        $this->assertResponseStatusCodeSame(403);
    }

    // Cette méthode me permet de créer une petite image temporaire
    // pour tester le formulaire d'upload sans utiliser une vraie photo.
    private function createTestImage(): UploadedFile
    {
        $temporaryPath = tempnam(
            sys_get_temp_dir(),
            'media_test_'
        );

        if ($temporaryPath === false) {
            throw new \RuntimeException(
                'Impossible de créer le fichier temporaire.'
            );
        }

        // Il s'agit d'une image PNG d'un pixel.
        $imageContent = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z4xkAAAAASUVORK5CYII=',
            true
        );

        if ($imageContent === false) {
            throw new \RuntimeException(
                'Impossible de générer l’image de test.'
            );
        }

        file_put_contents(
            $temporaryPath,
            $imageContent
        );

        return new UploadedFile(
            $temporaryPath,
            'image-test.png',
            'image/png',
            null,
            true
        );
    }
}