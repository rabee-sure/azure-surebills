<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two payment type
 */
class PaymentType
{
    private $paymentType;

    /**
     * Set payment type
     */
    public function setPaymentType($paymentType)
    {

        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'PaymentMeansCode',
            'value' => $this->paymentType,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cbc',
        ];
    }
}
