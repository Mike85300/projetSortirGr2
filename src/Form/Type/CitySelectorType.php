<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CityToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Romain Tanguy
 * Class CitySelectorType
 * @package App\Form\Type
 */
class CitySelectorType extends AbstractType
{
    private $transformer;

    public function __construct(CityToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer($this->transformer);
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'La ville saisie n\' est pas sauvegardée',
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
