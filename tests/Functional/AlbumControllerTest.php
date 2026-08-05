<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;


final class AlbumControllerTest extends WebTestCase
{
    //  Dans ce test je vérifie que l'administratrice
    //  peut accéder à la liste des albums.
    public function testAdminCanAccessAlbumList(): void
    {
        //  Je crée un client pour simuler la navigation sur le site.
        $client = static::createClient();

        //  Je récupère le repository des utilisateurs
        $userRepository = static::getContainer()
            ->get(UserRepository::class);
        //  Je récupère l'administratrice dans la base de test.
        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        //  Je vérifie que l'administratrice existe bien.
        $this->assertNotNull($admin);

        //  Je connecte directement l'administratrice
        $client->loginUser($admin);
        //  J'ouvre la page de gestion des albums.
        $client->request('GET', '/admin/album');

        //  Je vérifie que la page des albums s'affiche correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'main h1',
            'Albums'
        );
    }

    public function testAdminCanCreateAlbum(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $this->assertNotNull($admin);

        $client->loginUser($admin);

        $client->request('GET', '/admin/album/add');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'main h1',
            'Albums'
        );

        // Je crée un nom d'album unique
        // pour pouvoir relancer le test plusieurs fois.
        $albumName = sprintf(
            'Album de test %s',
            uniqid()
        );

        // Je remplis et je soumets le formulaire.
        $client->submitForm('Ajouter', [
            'album[name]' => $albumName,
        ]);

        // Je vérifie que je suis redirigée vers la liste des albums.
        $this->assertResponseRedirects('/admin/album');

        $client->followRedirect();

        // Je vérifie que le nouvel album apparaît dans la liste.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'body',
            $albumName
        );
    }
    // Dans ce test, je vérifie que l'administratrice
    // peut modifier le nom d'un album.
    // Dans ce test, je vérifie que l'administratrice
// peut modifier le nom d'un album.
    public function testAdminCanUpdateAlbum(): void
    {
        $client = static::createClient();

        // Je récupère les repositories dont j'ai besoin.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $albumRepository = static::getContainer()
            ->get(AlbumRepository::class);

        // Je récupère l'administratrice dans la base de test.
        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $this->assertNotNull($admin);

        // Je connecte directement l'administratrice.
        $client->loginUser($admin);

        // Je crée d'abord un album uniquement pour ce test.
        $client->request('GET', '/admin/album/add');

        $albumName = sprintf(
            'Album à modifier %s',
            uniqid()
        );

        $client->submitForm('Ajouter', [
            'album[name]' => $albumName,
        ]);

        $this->assertResponseRedirects('/admin/album');

        $client->followRedirect();

        // Je récupère l'album que je viens de créer
        // afin de connaître son identifiant.
        $album = $albumRepository->findOneBy([
            'name' => $albumName,
        ]);

        $this->assertNotNull($album);

        // J'ouvre son formulaire de modification.
        $client->request(
            'GET',
            '/admin/album/update/' . $album->getId()
        );

        $this->assertResponseIsSuccessful();

        // Je prépare un nouveau nom unique.
        $newAlbumName = sprintf(
            'Album modifié %s',
            uniqid()
        );

        // Je modifie et je soumets le formulaire.
        $client->submitForm('Modifier', [
            'album[name]' => $newAlbumName,
        ]);

        $this->assertResponseRedirects('/admin/album');

        $client->followRedirect();

        // Je vérifie que le nouveau nom apparaît dans la liste.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'body',
            $newAlbumName
        );
    }

    // Dans ce test, je vérifie que l'administratrice
    // peut supprimer un album.
    public function testAdminCanDeleteAlbum(): void
    {
        $client = static::createClient();

        // Je récupère les repositories dont j'ai besoin.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $albumRepository = static::getContainer()
            ->get(AlbumRepository::class);

        // Je récupère l'administratrice dans la base de test.
        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $this->assertNotNull($admin);

        // Je connecte directement l'administratrice.
        $client->loginUser($admin);

        // Je commence par créer un album
        // qui servira uniquement pour ce test de suppression.
        $client->request('GET', '/admin/album/add');

        $albumName = sprintf(
            'Album à supprimer %s',
            uniqid()
        );

        $client->submitForm('Ajouter', [
            'album[name]' => $albumName,
        ]);

        $this->assertResponseRedirects('/admin/album');

        $client->followRedirect();

        // Je récupère l'album que je viens de créer
        // afin de connaître son identifiant.
        $album = $albumRepository->findOneBy([
            'name' => $albumName,
        ]);

        $this->assertNotNull($album);

        // J'appelle la route de suppression avec l'identifiant de l'album.
        $client->request(
            'GET',
            '/admin/album/delete/' . $album->getId()
        );

        // Je vérifie que Symfony me redirige
        // vers la liste des albums.
        $this->assertResponseRedirects('/admin/album');

        $client->followRedirect();

        // Je vérifie que l'album supprimé
        // n'apparaît plus dans la liste.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains(
            'body',
            $albumName
        );
    }
}