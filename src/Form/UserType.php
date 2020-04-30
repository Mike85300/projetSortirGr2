<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('name')
            ->add('firstname')
            ->add('phone')
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class,
                array('mapped' => false,
                    'required' => false,
                    'type' => PasswordType::class,
                    'invalid_message' => 'Mot de passe différent. Veuillez réessayer.',
                    'first_options' => ['label' => 'Nouveau mot de passe'],
                    'second_options' => ['label' => 'Confirmer mot de passe']))
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name']);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

