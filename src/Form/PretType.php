<?php

namespace App\Form;
use App\Entity\Pret;
use Doctrine\DBAL\Types\DateType;
use App\Repository\UserRepository;
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

class PretType extends AbstractType
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
        
            ->add('mntP')    
            
            ->add('datEch')
            ->add('prd')
           
            
            
            
;        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => pret::class,
        ]);
    }
}
