<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two delivery date & time
 */
class Delivery
{
    private $deliveryDateTime;

    /**
     * Set delivery date & time
     */
    public function setDeliveryDateTime($deliveryDateTime)
    {

        $this->deliveryDateTime = $deliveryDateTime;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'ActualDeliveryDate',
            'value' => $this->deliveryDateTime,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cbc',
        ];
    }
}
