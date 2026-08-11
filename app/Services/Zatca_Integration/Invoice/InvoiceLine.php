<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two invoice line
 */
class InvoiceLine
{
    private $lineID;

    private $lineQuantity;

    private $lineSubTotal;

    private $lineTaxTotal;

    private $lineNetTotal;

    private $lineName;

    private $linePrice;

    private $lineCurrency;

    private $lineTaxCategories;

    private $lineDiscountReason = ' ';

    private $lineDiscountAmount = 0;

    /**
     * Set line ID
     */
    public function setLineID($lineID)
    {

        $this->lineID = $lineID;

        return $this;
    }

    /**
     * Set line quantity
     */
    public function setLineQuantity($lineQuantity)
    {

        $this->lineQuantity = $lineQuantity;

        return $this;
    }

    /**
     * Set line sub total without tax
     */
    public function setLineSubTotal($lineSubTotal)
    {

        $this->lineSubTotal = $lineSubTotal;

        return $this;
    }

    /**
     * Set line tax total
     */
    public function setLineTaxTotal($lineTaxTotal)
    {

        $this->lineTaxTotal = $lineTaxTotal;

        return $this;
    }

    /**
     * Set line net total
     */
    public function setLineNetTotal($lineNetTotal)
    {

        $this->lineNetTotal = $lineNetTotal;

        return $this;
    }

    /**
     * Set line name
     */
    public function setLineName($lineName)
    {

        $this->lineName = $lineName;

        return $this;
    }

    /**
     * Set line price
     */
    public function setLinePrice($linePrice)
    {

        $this->linePrice = $linePrice;

        return $this;
    }

    /**
     * Set line currency
     */
    public function setLineCurrency($lineCurrency)
    {

        $this->lineCurrency = $lineCurrency;

        return $this;
    }

    /**
     * Set line tax categories
     */
    public function setLineTaxCategories(...$lineTaxCategories)
    {

        $this->lineTaxCategories = $lineTaxCategories;

        return $this;
    }

    /**
     * Set line discount reason
     */
    public function setLineDiscountReason($lineDiscountReason)
    {

        $this->lineDiscountReason = $lineDiscountReason;

        return $this;
    }

    /**
     * Set line discount amount
     */
    public function setLineDiscountAmount($lineDiscountAmount)
    {

        $this->lineDiscountAmount = $lineDiscountAmount;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        array_unshift($this->lineTaxCategories, [
            'name' => 'Name',
            'value' => $this->lineName,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cbc',
        ]);

        return [
            'name' => 'InvoiceLine',
            'value' => null,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cac',
            'childs' => [
                [
                    'name' => 'ID',
                    'value' => $this->lineID,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                ],
                [
                    'name' => 'InvoicedQuantity',
                    'value' => number_format($this->lineQuantity, 2, '.', ''),
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                    'attributes' => [
                        [
                            'name' => 'unitCode',
                            'value' => 'PCE',
                            'namespaced' => false,
                            'namespace' => null,
                            'prefix' => null,
                        ],
                    ],
                ],
                [
                    'name' => 'LineExtensionAmount',
                    'value' => number_format($this->lineSubTotal, 2, '.', ''),
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                    'attributes' => [
                        [
                            'name' => 'currencyID',
                            'value' => $this->lineCurrency,
                            'namespaced' => false,
                            'namespace' => null,
                            'prefix' => null,
                        ],
                    ],
                ],
                [
                    'name' => 'TaxTotal',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
                        [
                            'name' => 'TaxAmount',
                            'value' => number_format($this->lineTaxTotal, 2, '.', ''),
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                            'attributes' => [
                                [
                                    'name' => 'currencyID',
                                    'value' => $this->lineCurrency,
                                    'namespaced' => false,
                                    'namespace' => null,
                                    'prefix' => null,
                                ],
                            ],
                        ],
                        [
                            'name' => 'RoundingAmount',
                            'value' => number_format($this->lineNetTotal, 2, '.', ''),
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                            'attributes' => [
                                [
                                    'name' => 'currencyID',
                                    'value' => $this->lineCurrency,
                                    'namespaced' => false,
                                    'namespace' => null,
                                    'prefix' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Item',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => $this->lineTaxCategories,
                ],
                [
                    'name' => 'Price',
                    'value' => null,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cac',
                    'childs' => [
                        [
                            'name' => 'PriceAmount',
                            'value' => number_format($this->linePrice, 2, '.', ''),
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cbc',
                            'attributes' => [
                                [
                                    'name' => 'currencyID',
                                    'value' => $this->lineCurrency,
                                    'namespaced' => false,
                                    'namespace' => null,
                                    'prefix' => null,
                                ],
                            ],
                        ],
                        [
                            'name' => 'AllowanceCharge',
                            'value' => null,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cac',
                            'childs' => [
                                [
                                    'name' => 'ChargeIndicator',
                                    'value' => 'false',
                                    'namespaced' => true,
                                    'namespace' => null,
                                    'prefix' => 'cbc',
                                ],
                                [
                                    'name' => 'AllowanceChargeReason',
                                    'value' => $this->lineDiscountReason,
                                    'namespaced' => true,
                                    'namespace' => null,
                                    'prefix' => 'cbc',
                                ],
                                [
                                    'name' => 'Amount',
                                    'value' => number_format($this->lineDiscountAmount, 2, '.', ''),
                                    'namespaced' => true,
                                    'namespace' => null,
                                    'prefix' => 'cbc',
                                    'attributes' => [
                                        [
                                            'name' => 'currencyID',
                                            'value' => $this->lineCurrency,
                                            'namespaced' => false,
                                            'namespace' => null,
                                            'prefix' => null,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
