<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\GuestType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


// Réserve l’ensemble des actions de ce contrôleur à l’administratrice Ina.
#[IsGranted('ROLE_ADMIN')]
final class GuestController extends AbstractController
{
    // Récupère uniquement les utilisateurs non administrateurs
    // afin d’afficher la liste des invités dans l’espace d’administration.
    #[Route('/admin/guest', name: 'admin_guest_index')]
    public function index(UserRepository $userRepository): Response
    {
        $guests = $userRepository->findBy([
            'admin' => false,
        ]);

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
        ]);
    }

    // Permet à l’administratrice de créer un nouvel invité
    // à partir du formulaire GuestType.
    #[Route('/admin/guest/new', name: 'admin_guest_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $guest = new User();

        // Associe le formulaire à la nouvelle entité User,
        // puis récupère les données envoyées par l’utilisateur.
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire place temporairement le mot de passe saisi
            // dans l’entité avant son hachage.
            $plainPassword = $guest->getPassword();

            if ($plainPassword === null || $plainPassword === '') {
                throw new \LogicException(
                    'Le mot de passe ne peut pas être vide.'
                );
            }

            // Hache le mot de passe avant son enregistrement en base.
            $guest->setPassword(
                $passwordHasher->hashPassword($guest, $plainPassword)
            );

            // Définit les valeurs imposées pour un nouveau compte invité.
            $guest->setAdmin(false);
            $guest->setBlocked(false);
            $guest->setRoles(['ROLE_USER']);

            $entityManager->persist($guest);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L’invité a bien été ajouté.'
            );

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Permet de modifier le statut d’un invité :
    // un compte actif devient bloqué et inversement.
    #[Route(
        '/admin/guest/{id}/toggle-block',
        name: 'admin_guest_toggle_block',
        methods: ['POST']
    )]
    public function toggleBlock(
        User $guest,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        // Vérifie le jeton CSRF afin de sécuriser l’action.
        if (!$this->isCsrfTokenValid(
            'toggle-block-' . $guest->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        // Empêche le blocage accidentel d’un compte administrateur.
        if ($guest->isAdmin()) {
            throw $this->createAccessDeniedException(
                'Un administrateur ne peut pas être bloqué.'
            );
        }

        // Inverse la valeur actuelle du statut blocked.
        $guest->setBlocked(!$guest->isBlocked());

        $entityManager->flush();

        $this->addFlash(
            'success',
            $guest->isBlocked()
                ? 'L’invité a bien été bloqué.'
                : 'L’invité a bien été débloqué.'
        );

        return $this->redirectToRoute('admin_guest_index');
    }

    // Supprime un invité, ses médias en base de données
    // ainsi que les fichiers physiques associés.
    #[Route(
        '/admin/guest/{id}/delete',
        name: 'admin_guest_delete',
        methods: ['POST']
    )]
    public function delete(
        User $guest,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        // Vérifie le jeton CSRF avant toute suppression.
        if (!$this->isCsrfTokenValid(
            'delete-guest-' . $guest->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        // Protège le compte administrateur contre toute suppression.
        if ($guest->isAdmin()) {
            throw $this->createAccessDeniedException(
                'Un administrateur ne peut pas être supprimé.'
            );
        }

        $filePaths = [];

        // Mémorise le chemin de chaque fichier avant de supprimer
        // les entités Media associées à l’invité.
        foreach ($guest->getMedias() as $media) {
            $filePaths[] = $media->getPath();
            $entityManager->remove($media);
        }

        // Supprime ensuite l’invité et valide les changements en base.
        $entityManager->remove($guest);
        $entityManager->flush();

        // Supprime les fichiers physiques uniquement après la réussite
        // de la suppression en base de données.
        foreach ($filePaths as $filePath) {
            $absolutePath = $this->getParameter('kernel.project_dir')
                . '/public/'
                . ltrim($filePath, '/');

            if (is_file($absolutePath)) {
                unlink($absolutePath);
            }
        }

        $this->addFlash(
            'success',
            'L’invité et ses médias ont bien été supprimés.'
        );

        return $this->redirectToRoute('admin_guest_index');
    }
}