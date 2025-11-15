<?php

    namespace App\Form;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    
        class RapportType extends AbstractType
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
                ->add('stat',EntityType::class,array(
                    'class' => 'App\Entity\Commande',
                    'choice_label' => function($commande) {
                         return $commande->getStatut(); 
                     },
                    'choice_value' => 'statut',
                    'placeholder' => 'Choisissez ...'))
            
                ;
            }
            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->setDefaults([
                'data_class' => null, // Important si vous n'utilisez pas une entité
                ]);
            }
        }
