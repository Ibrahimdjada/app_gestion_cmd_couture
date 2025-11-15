<?php

    namespace App\Form;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
        class RapporttailType extends AbstractType
        {
            public function buildForm(FormBuilderInterface $builder, array $options): void
            {
                $builder
                ->add('datDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text', // Permet de générer un champ input type="date"
                'required' => false, // Si non obligatoire
                ])
                ->add('datFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => false,
                ]);
            }
            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->setDefaults([
                'data_class' => null, // Important si vous n'utilisez pas une entité
                ]);
            }
        }
