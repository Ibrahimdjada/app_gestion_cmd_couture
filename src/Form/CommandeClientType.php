<?php

namespace App\Form;

use App\Entity\Commande;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CommandeClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('typeCom')
            ->add('datRec')
            ->add('montant')
            ->add('avance')
           
            ->add('filemod', FileType::class, [
                'label' => 'Fichiers Modèle',
                'required' => false,
                'multiple' => true, // Permettre plusieurs fichiers
                'mapped' => false,
                ])
            ->add('filetissu', FileType::class, [
                'label' => 'Fichiers Tissu',
                'required' => false,
                'multiple' => true, // Permettre plusieurs fichiers
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
