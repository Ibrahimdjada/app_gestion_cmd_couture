<?php

    namespace App\Form;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use App\Repository\UserRepository;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
        class RapportconcType extends AbstractType
        {
            public function buildForm(FormBuilderInterface $builder, array $options): void
            {
                $builder
                ->add('datDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text', // Permet de générer un champ input type="date"
                'required' => true, // Si non obligatoire
                ])
                ->add('datFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => true,
                ])
                ->add('usert', EntityType::class, array(
                    'class' => 'App\Entity\User',
                    'choice_label' => function ($usert) {
                        return $usert->getFirstName() . ' ' . $usert->getLastName();
                    },
                    'required' => false,
                    'multiple' => false,
                    'placeholder' => "Sélectionnez le tailleur",
                    'query_builder' => function (UserRepository $repository) {
        
                        // Filtrer les utilisateurs avec istailleur = true
                        return $repository->createQueryBuilder('u')
                            ->where('u.isTailleur = :isTailleur')
                            ->setParameter('isTailleur', true);
                    }
        
                ))
                
                ;
            }
            
            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->setDefaults([
                'data_class' => null, // Important si vous n'utilisez pas une entité
                ]);
            }
        }
