<?php

namespace App\Form;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
       
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'First Name',
            'attr' => ['class' => 'form-control']
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Last Name',
            'attr' => ['class' => 'form-control']
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['class' => 'form-control']
        ])
        ->add('telephone', TextType::class, [
            'label' => 'Telephone',
            'attr' => ['class' => 'form-control']
        ])
        ->add('adresse', TextType::class, [
            'label' => 'Address',
            'attr' => ['class' => 'form-control']
        ])
       
;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
