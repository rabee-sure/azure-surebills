<?php
namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two taxes totals
 */
class TaxesTotal
{
    private $taxCurrencyCode;
    private $taxTotal;

    /**
     * Set tax currency code
     */
    public function setTaxCurrencyCode($taxCurrencyCode)
    {

        $this->taxCurrencyCode = $taxCurrencyCode;

        return $this;
    }

    /**
     * Set tax total
     */
    public function setTaxTotal($taxTotal)
    {

        $this->taxTotal = $taxTotal;

        return $this;
    }

    /**
     * Get tax total
     */
    public function getTaxTotal()
    {

        return $this->taxTotal;

    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'TaxAmount',
            'value' => number_format($this->taxTotal,2,'.',''),
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
            ]
        ];
    }
}