<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GuestControllerTest extends WebTestCase
{
    // Dans ce test je vérifie que l'administratrice
    // peut accéder à la liste des invités.
    public function testAdminCanAccessGuestList(): void
    {
        // Je crée un client pour simuler la navigation sur le site.
        $client = static::createClient();

        // Je récupère le repository des utilisateurs
        // pour aller chercher l'administratrice dans la base de test.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        // Je vérifie que l'administratrice existe bien.
        $this->assertNotNull($admin);

        // Je connecte directement l'administratrice
        // sans passer par le formulaire de connexion.
        $client->loginUser($admin);

        // J'ouvre la page de gestion des invités.
        $client->request('GET', '/admin/guest');

        // Je vérifie que la page s'affiche correctement
        // et que je suis bien sur la bonne page.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'main h1',
            'Gestion des invités'
        );
    }

    // Dans ce test je vérifie que l'administratrice
    // peut créer un nouvel invité.
    public function testAdminCanCreateGuest(): void
    {
        $client = static::createClient();

        // Je récupère l'administratrice dans la base de test.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $this->assertNotNull($admin);

        // Je connecte directement l'administratrice.
        $client->loginUser($admin);

        // J'ouvre le formulaire d'ajout d'un invité.
        $client->request('GET', '/admin/guest/new');

        // Je vérifie que le formulaire s'affiche correctement.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'main h1',
            'Ajouter un invité'
        );

        // Je crée une adresse e-mail unique
        // pour pouvoir relancer le test plusieurs fois.
        $email = sprintf(
            'nouvel-invite-%s@test.fr',
            uniqid()
        );

        // Je remplis et je soumets le formulaire
        // comme le ferait l'administratrice sur le site.
        $client->submitForm('Ajouter', [
            'guest[name]' => 'Nouvel invité',
            'guest[email]' => $email,
            'guest[password]' => 'password',
            'guest[description]' => 'Invité créé pendant un test fonctionnel.',
        ]);

        // Je vérifie que Symfony me redirige
        // vers la liste des invités.
        $this->assertResponseRedirects('/admin/guest');

        // Je suis la redirection.
        $client->followRedirect();

        // Je vérifie que la page s'affiche bien
        // et que le nouvel invité apparaît dans la liste.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'body',
            'Nouvel invité'
        );
    }

    // Dans ce test, je vérifie qu'il n'est pas possible
    // de créer un invité avec une adresse e-mail déjà utilisée.
    public function testAdminCannotCreateGuestWithExistingEmail(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $existingGuest = $userRepository->findOneBy([
            'email' => 'louis@funes.com',
        ]);

        $this->assertNotNull($admin);
        $this->assertNotNull($existingGuest);

        $client->loginUser($admin);

        $client->request('GET', '/admin/guest/new');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Ajouter', [
            'guest[name]' => 'Invité doublon',
            'guest[email]' => $existingGuest->getEmail(),
            'guest[password]' => 'password',
            'guest[description]' => 'Test avec une adresse déjà existante.',
        ]);

        // Je vérifie que Symfony refuse correctement
        // les données du formulaire sans déclencher d'erreur serveur.
        $this->assertResponseStatusCodeSame(422);

        // Je vérifie que le message de validation est affiché.
        $this->assertSelectorTextContains(
            'body',
            'Cette adresse e-mail est déjà utilisée.'
        );

        // Je vérifie qu'aucun nouvel utilisateur n'a été créé.
        $users = $userRepository->findBy([
            'email' => $existingGuest->getEmail(),
        ]);

        $this->assertCount(1, $users);
    }

    // Je vérifie que l'administratrice
    // peut bloquer puis débloquer un invité.
    public function testAdminCanBlockAndUnblockGuest(): void
    {
        $client = static::createClient();

        // Je récupère le repository des utilisateurs.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        // Je récupère l'administratrice et l'invité actif
        // dont je vais modifier le statut.
        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $guest = $userRepository->findOneBy([
            'email' => 'louis@funes.com',
        ]);

        // Je vérifie que les deux utilisateurs existent bien.
        $this->assertNotNull($admin);
        $this->assertNotNull($guest);

        // Je connecte l'administratrice.
        $client->loginUser($admin);

        // J'ouvre la liste des invités.
        $crawler = $client->request('GET', '/admin/guest');

        // Je récupère le formulaire qui correspond
        // au bouton Bloquer de Louis.
        $form = $crawler
            ->filter(
                'form[action="/admin/guest/'
                . $guest->getId()
                . '/toggle-block"]'
            )
            ->form();

        // Je soumets le formulaire pour bloquer l'invité.
        $client->submit($form);

        // Je vérifie que je suis redirigée
        // vers la liste des invités.
        $this->assertResponseRedirects('/admin/guest');

        // Je suis la redirection après le blocage.
        $crawler = $client->followRedirect();

        // Je vérifie que le bouton affiche maintenant "Débloquer".
        $this->assertSelectorTextContains(
            'form[action="/admin/guest/'
            . $guest->getId()
            . '/toggle-block"] button',
            'Débloquer'
        );

        // Je récupère à nouveau le formulaire
        // afin de remettre Louis dans son état initial.
        $form = $crawler
            ->filter(
                'form[action="/admin/guest/'
                . $guest->getId()
                . '/toggle-block"]'
            )
            ->form();

        // Je soumets une deuxième fois le formulaire
        // pour débloquer l'invité.
        $client->submit($form);

        $this->assertResponseRedirects('/admin/guest');

        $client->followRedirect();

        // Je vérifie que le bouton affiche de nouveau "Bloquer".
        $this->assertSelectorTextContains(
            'form[action="/admin/guest/'
            . $guest->getId()
            . '/toggle-block"] button',
            'Bloquer'
        );
    }

    // Dans ce test, je vérifie que l'administratrice
    // peut supprimer un invité.
    public function testAdminCanDeleteGuest(): void
    {
        $client = static::createClient();

        // Je récupère l'administratrice dans la base de test.
        $userRepository = static::getContainer()
            ->get(UserRepository::class);

        $admin = $userRepository->findOneBy([
            'email' => 'ina@zaoui.com',
        ]);

        $this->assertNotNull($admin);

        // Je connecte directement l'administratrice.
        $client->loginUser($admin);

        // Je commence par créer un invité
        // qui servira uniquement pour ce test de suppression.
        $client->request('GET', '/admin/guest/new');

        $email = sprintf(
            'invite-suppression-%s@test.fr',
            uniqid()
        );

        $client->submitForm('Ajouter', [
            'guest[name]' => 'Invité à supprimer',
            'guest[email]' => $email,
            'guest[password]' => 'password',
            'guest[description]' => 'Compte créé pour tester la suppression.',
        ]);

        $this->assertResponseRedirects('/admin/guest');

        $client->followRedirect();

        // Je récupère l'invité que je viens de créer
        // afin de connaître son identifiant.
        $guest = $userRepository->findOneBy([
            'email' => $email,
        ]);

        $this->assertNotNull($guest);

        // J'ouvre la liste des invités.
        $crawler = $client->request('GET', '/admin/guest');

        // Je récupère le formulaire de suppression
        // correspondant à l'invité créé pour le test.
        $form = $crawler
            ->filter(
                'form[action="/admin/guest/'
                . $guest->getId()
                . '/delete"]'
            )
            ->form();

        // Je soumets le formulaire pour supprimer l'invité.
        $client->submit($form);

        // Je vérifie que Symfony me redirige
        // vers la liste des invités.
        $this->assertResponseRedirects('/admin/guest');

        $client->followRedirect();

        // Je vérifie que l'invité supprimé
        // n'apparaît plus dans la liste.
        $this->assertSelectorTextNotContains(
            'body',
            'Invité à supprimer'
        );
    }
}