<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two billing reference when credit or debit notes
 */
class BillingReference
{
    private $billingReference;

    /**
     * Set billing reference
     */
    public function setBillingReference($billingReference)
    {

        $this->billingReference = $billingReference;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'InvoiceDocumentReference',
            'value' => null,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cac',
            'childs' => [
                [
                    'name' => 'ID',
                    'value' => 'Invoice Number: '.$this->billingReference,
                    'namespaced' => true,
                    'namespace' => null,
                    'prefix' => 'cbc',
                ],
            ],
        ];
    }
}
