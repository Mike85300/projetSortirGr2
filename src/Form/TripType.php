<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\Trip;
use App\Form\Type\CitySelectorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie :',
                'required' => true,
                'trim' => true,
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'date_widget' => 'single_text',
                'required' => true,
            ])
            ->add('registrationDeadline', DateTimeType::class, [
                'label' => 'Date limite d\'inscription :',
                'date_widget' => 'single_text',
                'required' => true,
            ])
            ->add('maxRegistrationNumber', IntegerType::class, [
                'label' => 'Nombre de places :',
                'required' => false,
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée :',
                'required' => true,
            ])
            ->add('information', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => false,
                'trim' => true,
            ])
            ->add('city', CitySelectorType::class, [
                'label' => 'Ville :',
                'attr' => ['list' => 'cities'],
                'mapped' => false,
                'required' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publish', SubmitType::class, ['label' => 'Publier la sortie'])
            ;

        $formModifier = function (FormInterface $form, City $city = null)
        {
            $locations = [];

            if ($city)
            {
                $locationRepo = $this->entityManager->getRepository(Location::class);
                $locations = $locationRepo->findBy(['city' => $city->getId()]);
            }

            $form->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'choices' => $locations,
                'label' => 'Lieu :',
                'required' => true,
            ]);
        };

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                //this would be the entity Trip
                $data = $event->getData();

                $city = new City();

                if ($data != null && $data->getLocation() != null)
                {
                    $city = $data->getLocation()->getCity();
                }

                $formModifier($event->getForm(), $city);
            })
        ;

        $builder->get('city')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier)
            {
                $city = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $city);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
