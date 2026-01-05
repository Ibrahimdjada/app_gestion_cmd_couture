<?php

namespace App\Form;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
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
        ->add('role', ChoiceType::class, [
            'label' => 'Profile',
            'choices'  => [
                'Client' => 'ROLE_CLIENT',
                'Tailleur' => 'ROLE_TAILLEUR',
            ],
            'mapped' => false,
            'attr' => ['class' => 'form-control']
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'required' => true,
            'first_options'  => ['label' => 'Password', 'attr' => ['class' => 'form-control']],
            'second_options' => ['label' => 'Confirm Password', 'attr' => ['class' => 'form-control']],
        ]);  
        
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
