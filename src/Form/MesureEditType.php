<?php

namespace App\Form;

use App\Entity\Mesure;
use Doctrine\DBAL\Types\FloatType;
use phpDocumentor\Reflection\Types\Float_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MesureEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('epaule',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('poitrine',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('encolure',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('manche',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('poignee',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('ecartDos',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('tourVentrale',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('longueur',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('cuisse',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('fermeture',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('ceinture',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('taille',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('longueurPantalon',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ])
            ->add('basPantalon',NumberType::class,
            [
                'scale'=>2,
                'required'=>true,
                    
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mesure::class,
        ]);
    }
}
