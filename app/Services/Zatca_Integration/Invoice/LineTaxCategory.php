<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two line tax category
 */
class LineTaxCategory
{
    private $taxCategory;

    private $taxPercentage;

    /**
     * Set item tax category
     */
    public function setTaxCategory($taxCategory)
    {

        $this->taxCategory = $taxCategory;

        return $this;
    }

    /**
     * Set item tax percentage
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
            'name' => 'ClassifiedTaxCategory',
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
                ],
                [
                    'name' => 'Percent',
                    'value' => number_format($this->taxPercentage, 2, '.', ''),
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
        ];
    }
}
