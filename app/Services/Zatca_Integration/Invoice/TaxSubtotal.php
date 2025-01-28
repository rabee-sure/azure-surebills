<?php
namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two tax sub totals
 */
class TaxSubtotal
{
    private $taxCurrencyCode;
    private $taxableAmount;
    private $taxAmount;
    private $taxCategory;
    private $taxPercentage;

    /**
     * Set tax currency code
     */
    public function setTaxCurrencyCode($taxCurrencyCode)
    {

        $this->taxCurrencyCode = $taxCurrencyCode;

        return $this;
    }

    /**
     * Set taxable amount
     */
    public function setTaxableAmount($taxableAmount)
    {

        $this->taxableAmount = $taxableAmount;

        return $this;
    }

    /**
     * Set tax amount
     */
    public function setTaxAmount($taxAmount)
    {

        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * Set tax category
     */
    public function setTaxCategory($taxCategory)
    {

        $this->taxCategory = $taxCategory;

        return $this;
    }

    /**
     * Set tax percentage
     */
    public function setTaxPercentage($taxPercentage)
    {

        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'TaxSubtotal',
            'value' => null,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cac',
            'childs' => [
                [
                    'name' => 'TaxableAmount',
                    'value' => number_format($this->taxableAmount,2,'.',''),
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                    'attributes' => [
                        [
                            'name' => 'currencyID',
                            'value' => $this->taxCurrencyCode,
                            'namespaced' => false,
                            'namespace' => null,
                            'prefix' => null,
                        ],
                    ],
                ],
                [
                    'name' => 'TaxAmount',
                    'value' => number_format($this->taxAmount,2,'.',''),
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                    'attributes' => [
                        [
                            'name' => 'currencyID',
                            'value' => $this->taxCurrencyCode,
                            'namespaced' => false,
                            'namespace' => null,
                            'prefix' => null,
                        ],
                    ],
                ],
                [
                    'name' => 'TaxCategory',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
                        [
                            'name' => 'ID',
                            'value' => $this->taxCategory,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                            'attributes' => [
                                [
                                    'name' => 'schemeAgencyID',
                                    'value' => '6',
                                    'namespaced' => false,
                                    'namespace' => null,
                                    'prefix' => null,
                                ],
                                [
                                    'name' => 'schemeID',
                                    'value' => 'UN/ECE 5305',
                                    'namespaced' => false,
                                    'namespace' => null,
                                    'prefix' => null,
                                ],
                            ],
                        ],
                        [
                            'name' => 'Percent',
                            'value' => number_format($this->taxPercentage,2,'.',''),
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
                                    'attributes' => [
                                        [
                                            'name' => 'schemeAgencyID',
                                            'value' => '6',
                                            'namespaced' => false,
                                            'namespace' => null,
                                            'prefix' => null,
                                        ],
                                        [
                                            'name' => 'schemeID',
                                            'value' => 'UN/ECE 5153',
                                            'namespaced' => false,
                                            'namespace' => null,
                                            'prefix' => null,
                                        ],
                                    ],
                                ]
                            ]
                        ],
                    ],
                ],
            ]
        ];
    }
}