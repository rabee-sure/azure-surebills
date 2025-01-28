<?php
namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two return reasons
 */
class ReturnReason
{
    private string $returnReason;

    /**
     * Set return reason
     */
    public function setReturnReason($returnReason)
    {

        $this->returnReason = $returnReason;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement()
    {
        return [
            'name' => 'InstructionNote',
            'value' => $this->returnReason,
            'namespaced' => true,
            'namespace' => null,
            'prefix' => 'cbc',
        ];
    }
}