<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Site :',
                'required'=> false,
                'class' => Campus::class,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('q', TextType::class, [
                'label' => false,
                'required'=> false,
                'attr' => [
                    'placeholder' => 'Le nom de la sortie contient...'
                ]
        ])
        ->add('dateMin', DateType::class, [
            'label' => 'Du',
            'required'=> false,
            'widget' => 'single_text',
            'html5' => true,
            'attr' => ['class' => 'js-datepicker']
        ])
        ->add('dateMax', DateType::class, [
            'label' => 'au ',
            'required'=> false,
            'widget' => 'single_text',
            'html5' => true,
            'attr' => ['class' => 'js-datepicker']
        ])
        ->add('userOrganisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required'=> false,
                'data' => true,
            ])
            ->add('userInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required'=> false,
                'data' => true,
            ])
            ->add('userNonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required'=> false,
                'data' => true,
            ])
            ->add('finishedTrip', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required'=> false,
            ])




        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>SearchData::class,
            'method'=>'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}