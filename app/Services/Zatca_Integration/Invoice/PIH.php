<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two previous hash
 */
class PIH
{
    private $pih;

    /**
     * Set previous hash
     */
    public function setPIH($pih)
    {

        $this->pih = $pih;

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
                'value' => 'PIH',
                'namespaced' => true,
                'namespace' => null,
                'prefix' => 'cbc',
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
                        'value' => $this->pih,
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
                        ],
                    ],
                ],
            ],
        ];
    }
}
