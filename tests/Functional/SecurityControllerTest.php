<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityControllerTest extends WebTestCase
{
    // Dans ce test, je vérifie que la page de connexion
    // est bien accessible à un utilisateur.
    public function testLoginPageIsAccessible(): void
    {
        // Je crée un client pour simuler un utilisateur.
        $client = static::createClient();

        // J'ouvre la page de connexion.
        $client->request('GET', '/login');

        // Je vérifie que la page s'affiche correctement.
        $this->assertResponseIsSuccessful();

        // Je vérifie que le titre "Connexion" est présent.
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    // Dans ce test, je vérifie qu'une administratrice
    // peut se connecter à l'application.
    public function testAdminCanLogin(): void
    {
        // Je crée un client pour simuler un utilisateur.
        $client = static::createClient();

        // J'ouvre la page de connexion.
        $client->request('GET', '/login');

        // Je remplis le formulaire avec le compte administrateur.
        $client->submitForm('Connexion', [
            '_username' => 'ina@zaoui.com',
            '_password' => 'password',
        ]);

        // Je vérifie que je suis redirigée
        // vers la page des médias.
        $this->assertResponseRedirects('/admin/media');

        // Je suis automatiquement la redirection.
        $client->followRedirect();

        // Je vérifie que la page est bien affichée.
        $this->assertResponseIsSuccessful();

        // Je vérifie que je suis bien sur la page des médias.
        $this->assertSelectorTextContains('main h1', 'Medias');
    }

    // Dans ce test, je vérifie qu'un utilisateur bloqué
    // ne peut pas se connecter.
    public function testBlockedUserCannotLogin(): void
    {
        // Je crée un client pour simuler un utilisateur.
        $client = static::createClient();

        // J'ouvre la page de connexion.
        $client->request('GET', '/login');

        // J'essaie de me connecter avec un compte bloqué.
        $client->submitForm('Connexion', [
            '_username' => 'jean@dujardin.com',
            '_password' => 'password',
        ]);

        // Je vérifie que je suis renvoyée
        // vers la page de connexion.
        $this->assertResponseRedirects('/login');

        // Je suis la redirection.
        $client->followRedirect();

        // Je vérifie qu'un message d'erreur est affiché.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    // Dans ce test, je vérifie qu'un invité actif
    // peut se connecter mais qu'il n'a pas les droits
    // d'un administrateur.
    public function testActiveGuestCanLogin(): void
    {
        // Je crée un client pour simuler un utilisateur.
        $client = static::createClient();

        // J'ouvre la page de connexion.
        $client->request('GET', '/login');

        // Je me connecte avec un compte invité.
        $client->submitForm('Connexion', [
            '_username' => 'louis@funes.com',
            '_password' => 'password',
        ]);

        // Je vérifie que je suis redirigée
        // vers la page des médias.
        $this->assertResponseRedirects('/admin/media');

        // Je suis la redirection.
        $client->followRedirect();

        // Je vérifie que la page est bien affichée.
        $this->assertResponseIsSuccessful();

        // Je vérifie que je suis bien sur la page des médias.
        $this->assertSelectorTextContains('main h1', 'Medias');

        // Je vérifie que les menus réservés
        // aux administrateurs ne sont pas visibles.
        $this->assertSelectorNotExists('a[href="/admin/guest"]');
        $this->assertSelectorNotExists('a[href="/admin/album"]');
    }
    // Dans ce test, je vérifie qu'un utilisateur connecté
    // peut se déconnecter correctement de l'application.
    public function testUserCanLogout(): void
    {
        $client = static::createClient();

        // J'ouvre la page de connexion.
        $client->request('GET', '/login');

        // Je me connecte avec un invité actif.
        $client->submitForm('Connexion', [
            '_username' => 'louis@funes.com',
            '_password' => 'password',
        ]);

        // Je vérifie que la connexion a fonctionné.
        $this->assertResponseRedirects('/admin/media');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        // Je demande la déconnexion.
        $client->request('GET', '/logout');

        // Je vérifie que Symfony effectue bien une redirection
        // après la déconnexion.
        $this->assertResponseRedirects();

        $client->followRedirect();

        // Je tente ensuite d'accéder à une page protégée.
        $client->request('GET', '/admin/media');

        // L'utilisateur étant déconnecté,
        // il doit être renvoyé vers la page de connexion.
        $this->assertResponseRedirects('/login');
    }

    // Dans ce test, je vérifie qu'un invité
    // ne peut pas accéder à la gestion des invités.
    public function testActiveGuestCannotAccessGuestManagement(): void
    {
        // Je crée un client pour simuler un utilisateur.
        $client = static::createClient();

        // Je me connecte avec un compte invité.
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            '_username' => 'louis@funes.com',
            '_password' => 'password',
        ]);

        // Je suis la redirection après la connexion.
        $client->followRedirect();

        // J'essaie d'accéder directement
        // à une page réservée aux administrateurs.
        $client->request('GET', '/admin/guest');

        // Je vérifie que l'accès est refusé.
        $this->assertResponseStatusCodeSame(403);
    }

    // Dans ce test, je vérifie qu'un invité
    // ne peut pas accéder à la gestion des albums.
    public function testActiveGuestCannotAccessAlbumManagement(): void
    {
        // Je crée un client pour simuler un utilisateur.
        $client = static::createClient();

        // Je me connecte avec un compte invité.
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            '_username' => 'louis@funes.com',
            '_password' => 'password',
        ]);

        // Je suis la redirection après la connexion.
        $client->followRedirect();

        // J'essaie d'accéder directement
        // à une page réservée aux administrateurs.
        $client->request('GET', '/admin/album');

        // Je vérifie que l'accès est refusé.
        $this->assertResponseStatusCodeSame(403);
    }
}