<?php

namespace App\Tests\Functional;

use App\Repository\AlbumRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    // Je vérifie que la page d'accueil
    // est accessible correctement.
    public function testHomePageIsAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    // Je vérifie que la page listant les invités
    // est accessible correctement.
    public function testGuestsPageIsAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/guests');

        $this->assertResponseIsSuccessful();
    }

    // Je vérifie que la page d'un invité existant
    // est accessible correctement.
    public function testGuestPageIsAccessible(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $guest = $userRepository->findOneBy([
            'admin' => false,
        ]);

        $this->assertNotNull($guest);

        $client->request(
            'GET',
            '/guest/' . $guest->getId()
        );

        $this->assertResponseIsSuccessful();
    }

    // Je vérifie qu'une erreur 404 est retournée
    // lorsqu'un invité n'existe pas.
    public function testGuestNotFound(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/guest/999999'
        );

        $this->assertResponseStatusCodeSame(404);
    }

    // Je vérifie que le portfolio général
    // est accessible correctement.
    public function testPortfolioPageIsAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/portfolio');

        $this->assertResponseIsSuccessful();
    }

    // Je vérifie que le portfolio peut être filtré
    // avec un album existant.
    public function testPortfolioWithAlbumIsAccessible(): void
    {
        $client = static::createClient();

        $albumRepository = static::getContainer()
            ->get(AlbumRepository::class);

        $album = $albumRepository->findOneBy([]);

        $this->assertNotNull($album);

        $client->request(
            'GET',
            '/portfolio/' . $album->getId()
        );

        $this->assertResponseIsSuccessful();
    }

    // Je vérifie que la page À propos
    // est accessible correctement.
    public function testAboutPageIsAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/about');

        $this->assertResponseIsSuccessful();
    }
}