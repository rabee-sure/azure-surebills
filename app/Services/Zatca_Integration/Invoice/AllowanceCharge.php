<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two allowance charge
 */
class AllowanceCharge
{
    private $allowanceChargeCurrency;

    private $allowanceChargeIndex;

    private $allowanceChargeAmount;

    private $allowanceChargeTaxCategory;

    private $allowanceChargeTaxPercentage;

    /**
     * Set allowance currency
     */
    public function setAllowanceChargeCurrency($allowanceChargeCurrency)
    {

        $this->allowanceChargeCurrency = $allowanceChargeCurrency;

        return $this;
    }

    /**
     * Set allowance index
     */
    public function setAllowanceChargeIndex($allowanceChargeIndex)
    {

        $this->allowanceChargeIndex = $allowanceChargeIndex;

        return $this;
    }

    /**
     * Set allowance amount
     */
    public function setAllowanceChargeAmount($allowanceChargeAmount)
    {

        $this->allowanceChargeAmount = $allowanceChargeAmount;

        return $this;
    }

    /**
     * Set allowance tax category
     */
    public function setAllowanceChargeTaxCategory($allowanceChargeTaxCategory)
    {

        $this->allowanceChargeTaxCategory = $allowanceChargeTaxCategory;

        return $this;
    }

    /**
     * Set allowance tax percentage
     */
    public function setAllowanceChargeTaxPercentage($allowanceChargeTaxPercentage)
    {

        $this->allowanceChargeTaxPercentage = $allowanceChargeTaxPercentage;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'AllowanceCharge',
            'value' => null,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cac',
            'childs' => [
                [
                    'name' => 'ID',
                    'value' => $this->allowanceChargeIndex,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                ],
                [
                    'name' => 'ChargeIndicator',
                    'value' => 'false',
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                ],
                [
                    'name' => 'AllowanceChargeReason',
                    'value' => 'discount',
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                ],
                [
                    'name' => 'Amount',
                    'value' => number_format($this->allowanceChargeAmount, 2, '.', ''),
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                    'attributes' => [
                        [
                            'name' => 'currencyID',
                            'value' => $this->allowanceChargeCurrency,
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
                            'value' => $this->allowanceChargeTaxCategory,
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
                            'value' => $this->allowanceChargeTaxPercentage,
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
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
