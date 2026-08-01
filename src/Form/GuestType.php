<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    // Permet de bloquer ou débloquer un invité
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
        if (!$this->isCsrfTokenValid(
            'toggle-block-' . $guest->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($guest->isAdmin()) {
            throw $this->createAccessDeniedException(
                'Un administrateur ne peut pas être bloqué.'
            );
        }

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
}
