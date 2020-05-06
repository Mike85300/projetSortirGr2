<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class SearchData
{

    public $q ='';


    public $campus = [];

    public $dateMin;


    /**
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="dateMin",
     *     message="La date de début doit être postérieure à {{ compared_value }}."
     * )
     */
    public $dateMax;


    public $userOrganisateur = false;


    public $userInscrit = false;


    public $userNonInscrit = false;


    public $finishedTrip = false;





}