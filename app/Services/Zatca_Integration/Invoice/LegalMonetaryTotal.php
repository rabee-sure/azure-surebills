<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two invoice totals
 */
class LegalMonetaryTotal
{
    private $totalCurrency;

    private $lineExtensionAmount;

    private $taxExclusiveAmount;

    private $taxInclusiveAmount;

    private $allowanceTotalAmount;

    private $prepaidAmount;

    private $payableAmount;

    /**
     * Set totals currency
     */
    public function setTotalCurrency($totalCurrency)
    {

        $this->totalCurrency = $totalCurrency;

        return $this;
    }

    /**
     * Set line extension amount
     */
    public function setLineExtensionAmount($lineExtensionAmount)
    {

        $this->lineExtensionAmount = $lineExtensionAmount;

        return $this;
    }

    /**
     * Set tax exclusive amount
     */
    public function setTaxExclusiveAmount($taxExclusiveAmount)
    {

        $this->taxExclusiveAmount = $taxExclusiveAmount;

        return $this;
    }

    /**
     * Set tax inclusive amount
     */
    public function setTaxInclusiveAmount($taxInclusiveAmount)
    {

        $this->taxInclusiveAmount = $taxInclusiveAmount;

        return $this;
    }

    /**
     * Get tax inclusiveAmount amount
     */
    public function getTaxInclusiveAmount()
    {

        return $this->taxInclusiveAmount;

    }

    /**
     * Set allowance total amount
     */
    public function setAllowanceTotalAmount($allowanceTotalAmount)
    {

        $this->allowanceTotalAmount = $allowanceTotalAmount;

        return $this;
    }

    /**
     * Set prepaid amount
     */
    public function setPrepaidAmount($prepaidAmount)
    {

        $this->prepaidAmount = $prepaidAmount;

        return $this;
    }

    /**
     * Set payable amount
     */
    public function setPayableAmount($payableAmount)
    {

        $this->payableAmount = $payableAmount;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            [
                'name' => 'LineExtensionAmount',
                'value' => number_format($this->lineExtensionAmount, 2, '.', ''),
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
                'attributes' => [
                    [
                        'name' => 'currencyID',
                        'value' => $this->totalCurrency,
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                ],
            ],
            [
                'name' => 'TaxExclusiveAmount',
                'value' => number_format($this->taxExclusiveAmount, 2, '.', ''),
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
                'attributes' => [
                    [
                        'name' => 'currencyID',
                        'value' => $this->totalCurrency,
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                ],
            ],
            [
                'name' => 'TaxInclusiveAmount',
                'value' => number_format($this->taxInclusiveAmount, 2, '.', ''),
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
                'attributes' => [
                    [
                        'name' => 'currencyID',
                        'value' => $this->totalCurrency,
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                ],
            ],
            [
                'name' => 'AllowanceTotalAmount',
                'value' => number_format($this->allowanceTotalAmount, 2, '.', ''),
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
                'attributes' => [
                    [
                        'name' => 'currencyID',
                        'value' => $this->totalCurrency,
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                ],
            ],
            [
                'name' => 'PrepaidAmount',
                'value' => number_format($this->prepaidAmount, 2, '.', ''),
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
                'attributes' => [
                    [
                        'name' => 'currencyID',
                        'value' => $this->totalCurrency,
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                ],
            ],
            [
                'name' => 'PayableAmount',
                'value' => number_format($this->payableAmount, 2, '.', ''),
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
                'attributes' => [
                    [
                        'name' => 'currencyID',
                        'value' => $this->totalCurrency,
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                ],
            ],
        ];
    }
}
