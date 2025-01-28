<?php
namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two invoice additional document reference
 */
class AdditionalDocumentReference
{
    private $invoiceID;

    /**
     * Set invoice ID
     */
    public function setInvoiceID($invoiceID)
    {

        $this->invoiceID = $invoiceID;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            [
                'name' => 'ID',
                'value' => 'ICV',
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
            ],
            [
                'name' => 'UUID',
                'value' => $this->invoiceID,
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
            ],
        ];
    }
}