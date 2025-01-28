<?php
namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two client
 */
class Client
{
    private $vatNumber;
    private $streetName;
    private $buildingNumber;
    private $plotIdentification;
    private $subDivisionName;
    private $cityName;
    private $postalNumber;
    private $countryName;
    private $clientName;

    /**
     * Set client vat number
     */
    public function setVatNumber($vatNumber)
    {

        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * Set client street name
     */
    public function setStreetName($streetName)
    {

        $this->streetName = $streetName;

        return $this;
    }
    
    /**
     * Set client building number
     */
    public function setBuildingNumber($buildingNumber)
    {

        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    /**
     * Set client plot identification
     */
    public function setPlotIdentification($plotIdentification)
    {

        $this->plotIdentification = $plotIdentification;

        return $this;
    }

    /**
     * Set client sub divisionName
     */
    public function setSubDivisionName($subDivisionName)
    {

        $this->subDivisionName = $subDivisionName;

        return $this;
    }

    /**
     * Set client city name
     */
    public function setCityName($cityName)
    {

        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Set client postal number
     */
    public function setPostalNumber($postalNumber)
    {

        $this->postalNumber = $postalNumber;

        return $this;
    }

    /**
     * Set client country name
     */
    public function setCountryName($countryName)
    {

        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Set client name
     */
    public function setClientName($clientName)
    {

        $this->clientName = $clientName;

        return $this;
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
                            'value' => $this->vatNumber,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                            'attributes' => [
                                [
                                    'name' => 'schemeID',
                                    'value' => 'NAT',
                                    'namespaced' => false,
                                    'namespace' => null,
                                    'prefix' => null,
                                ],
                            ],
                        ]
                    ]
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
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'PartyTaxScheme',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
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
                                ]
                            ]
                        ]
                    ]
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
                            'value' => $this->clientName,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                        ]
                    ]  
                ]
            ]
        ];
    }
}