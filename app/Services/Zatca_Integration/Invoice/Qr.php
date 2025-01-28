<?php
namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two qr element
 */
class Qr
{
    private $qrCode;

    /**
     * Set qr value
     */
    public function setQrCode($qrCode)
    {

        $this->qrCode = $qrCode;

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
                'value' => 'QR',
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc'
            ],
            [
                'name' => 'Attachment',
                'value' => null,
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cac',
                'childs' => [
                    [
                        'name' => 'EmbeddedDocumentBinaryObject',
                        'value' => $this->qrCode,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                        'attributes' => [
                            [
                                'name' => 'mimeCode',
                                'value' => 'text/plain',
                                'namespaced' => false,
                                'namespace' => null,
                                'prefix' => null,
                            ],
                        ]
                    ]
                ]
            ],
        ];
    }
}