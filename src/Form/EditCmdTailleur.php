<?php

namespace App\Form;

use App\Entity\Commande;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditCmdTailleur extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('usert', EntityType::class, array(
            'class' => 'App\Entity\User',
            'choice_label' => function ($usert) {
                return $usert->getFirstName() . ' ' . $usert->getLastName();
            },
            'required' => true,
            'multiple' => false,
            'placeholder' => "Sélectionnez le tailleur",
            'query_builder' => function (UserRepository $repository) {

                // Filtrer les utilisateurs avec isTailleur = true
                return $repository->createQueryBuilder('u')
                    ->where('u.isTailleur = :isTailleur')
                    ->setParameter('isTailleur', true);
            }

        ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
