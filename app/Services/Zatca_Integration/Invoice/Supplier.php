<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two supplier
 */
class Supplier
{
    private $crn;

    private $streetName;

    private $buildingNumber;

    private $plotIdentification;

    private $subDivisionName;

    private $cityName;

    private $postalNumber;

    private $countryName;

    private $vatNumber;

    private $vatName;

    /**
     * Set supplier commercial registration number
     */
    public function setCrn($crn)
    {

        $this->crn = $crn;

        return $this;
    }

    /**
     * Set supplier street name
     */
    public function setStreetName($streetName)
    {

        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Set supplier building number
     */
    public function setBuildingNumber($buildingNumber)
    {

        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    /**
     * Set supplier plot identification
     */
    public function setPlotIdentification($plotIdentification)
    {

        $this->plotIdentification = $plotIdentification;

        return $this;
    }

    /**
     * Set supplier sub divisionName
     */
    public function setSubDivisionName($subDivisionName)
    {

        $this->subDivisionName = $subDivisionName;

        return $this;
    }

    /**
     * Set supplier city name
     */
    public function setCityName($cityName)
    {

        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Set supplier postal number
     */
    public function setPostalNumber($postalNumber)
    {

        $this->postalNumber = $postalNumber;

        return $this;
    }

    /**
     * Set supplier country name
     */
    public function setCountryName($countryName)
    {

        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Set supplier vat number
     */
    public function setVatNumber($vatNumber)
    {

        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * Get supplier vat number
     */
    public function getVatNumber()
    {

        return $this->vatNumber;

    }

    /**
     * Set supplier name
     */
    public function setVatName($vatName)
    {

        $this->vatName = $vatName;

        return $this;
    }

    /**
     * Get supplier name
     */
    public function getVatName()
    {

        return $this->vatName;

    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'Party',
            'value' => null,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cac',
            'childs' => [
                [
                    'name' => 'PartyIdentification',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
                        [
                            'name' => 'ID',
                            'value' => $this->crn,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                            'attributes' => [
                                [
                                    'name' => 'schemeID',
                                    'value' => 'CRN',
                                    'namespaced' => false,
                                    'namespace' => null,
                                    'prefix' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'PostalAddress',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
                        [
                            'name' => 'StreetName',
                            'value' => $this->streetName,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                        [
                            'name' => 'BuildingNumber',
                            'value' => $this->buildingNumber,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                        [
                            'name' => 'PlotIdentification',
                            'value' => $this->plotIdentification,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                        [
                            'name' => 'CitySubdivisionName',
                            'value' => $this->subDivisionName,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                        [
                            'name' => 'CityName',
                            'value' => $this->cityName,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                        [
                            'name' => 'PostalZone',
                            'value' => $this->postalNumber,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                        [
                            'name' => 'Country',
                            'value' => null,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cac',
                            'childs' => [
                                [
                                    'name' => 'IdentificationCode',
                                    'value' => $this->countryName,
                                    'namespaced' => true,
                                    'namespace' => null,
                                    'prefix' => 'cbc',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'PartyTaxScheme',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
                        [
                            'name' => 'CompanyID',
                            'value' => $this->vatNumber,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                        [
                            'name' => 'TaxScheme',
                            'value' => null,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cac',
                            'childs' => [
                                [
                                    'name' => 'ID',
                                    'value' => 'VAT',
                                    'namespaced' => true,
                                    'namespace' => null,
                                    'prefix' => 'cbc',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'PartyLegalEntity',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
                        [
                            'name' => 'RegistrationName',
                            'value' => $this->vatName,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ],
                    ],
                ],
            ],
        ];
    }
}
