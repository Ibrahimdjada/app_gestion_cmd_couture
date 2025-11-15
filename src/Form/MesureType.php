<?php

namespace App\Form;

namespace App\Form;

use App\Entity\Mesure;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FloatType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use App\Repository\UserRepository;

class MesureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('user', EntityType::class, array(
            'class' => 'App\Entity\User',
            'choice_label' => function ($user) {
                return $user->getFirstName() . ' ' . $user->getLastName();
            },
            'required' => true,
            'multiple' => false,
            'placeholder' => "Sélectionnez le client",
            'query_builder' => function (UserRepository $repository) {

                // Filtrer les utilisateurs avec isClient = true
                return $repository->createQueryBuilder('u')
                    ->where('u.isClient = :isClient')
                    ->setParameter('isClient', true);
            }

        ))
            ->add('epaule')
            ->add('poitrine')
            ->add('manche')
            ->add('encolure')
            ->add('poignee')
            ->add('ecartDos')
            ->add('tourVentrale')
            ->add('longueur')
            ->add('cuisse')
            ->add('fermeture')
            ->add('ceinture')
            ->add('taille')
            ->add('longueurPantalon')
            ->add('basPantalon')
            ;
    }
   
 
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mesure::class,
        ]);
    }
}
