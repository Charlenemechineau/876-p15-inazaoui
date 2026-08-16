<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MediaRepository; //Me permet de utiliser le MediaRepository pour récupérer les médias//
use Doctrine\ORM\EntityManagerInterface; // Me permet d'utiliser l'EntityManagerInterface pour gérer les entités//
use Symfony\Component\HttpFoundation\Response; //Me permet d'utiliser la classe Response pour retourner une réponse HTTP//

class MediaController extends AbstractController
{
    //  méthode qui permet de récupérer tous les médias et
    // de les afficher dans la vue admin/media/index.html.twig
     #[Route("/admin/media", name:"admin_media_index")]

     public function index(
         Request $request,
         MediaRepository $mediaRepository
     ): Response
    {
        $page = max(1, $request->query->getInt('page', 1));

        $criteria = [];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $medias = $mediaRepository->findBy(
            $criteria,
            ['id' => 'DESC'],
            25,
            25 * ($page - 1)
        );
        $total = $mediaRepository->count($criteria);

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page
        ]);
    }

    //Méthode qui permet d'ajouter un nouveau média en utilisant
    // le formulaire MediaType et de le sauvegarder dans la base de données
     #[Route("/admin/media/add", name:"admin_media_add")]

     public function add(
         Request $request,
         EntityManagerInterface $entityManager
     ): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser());
            }
            $fileName = md5(uniqid()) . '.' . $media->getFile()->guessExtension();

            $media->getFile()->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads',
                $fileName

            );

            $media->setPath('uploads/' . $fileName);
            $entityManager->persist($media);
            $entityManager->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    // Méthode qui permet de supprimer un média.
// Un administrateur peut supprimer tous les médias,
// tandis qu'un invité ne peut supprimer que ses propres médias.
    #[Route("/admin/media/delete/{id}", name:"admin_media_delete")]
    public function delete(
        MediaRepository $mediaRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        // Récupère le média à partir de son identifiant.
        $media = $mediaRepository->find($id);

        // Retourne une erreur 404 si le média n'existe pas.
        if (!$media) {
            throw $this->createNotFoundException('Média introuvable.');
        }

        // Vérifie que l'utilisateur est autorisé à supprimer ce média.
        // Un invité ne peut supprimer que ses propres médias.
        if (
            !$this->isGranted('ROLE_ADMIN')
            && $media->getUser() !== $this->getUser()
        ) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas supprimer ce média.'
            );
        }

        // Construit le chemin absolu du fichier afin de le supprimer du serveur.
        $filePath = $this->getParameter('kernel.project_dir')
            . '/public/'
            . ltrim($media->getPath(), '/');

        // Supprime le média de la base de données.
        $entityManager->remove($media);
        $entityManager->flush();

        // Supprime le fichier physique s'il existe encore sur le serveur.
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // Redirige l'utilisateur vers la liste des médias.
        return $this->redirectToRoute('admin_media_index');
    }
}