<?php

namespace Allam\Zatca\Invoice;

/**
 * A class defines zatca phase two xml namespaces
 */
class XmlBuilder
{
    private $xMLWriter;

    public function __construct()
    {

        $this->xMLWriter = new \XMLWriter;
        $this->xMLWriter->openMemory();
        $this->xMLWriter->setIndent(true);
        $this->xMLWriter->setIndentString('    ');
        $this->xMLWriter->startDocument();
    }

    /**
     * build xml elements
     */
    public function build($content)
    {
        $this->arrayToXml($content, $this->xMLWriter);
        $this->xMLWriter->endDocument();

        return $this;
    }

    /**
     * Generate xml elements as text
     */
    public function generateAsText()
    {
        $outputString = $this->xMLWriter->outputMemory(true);
        $this->xMLWriter->flush();

        return $outputString;
    }

    /**
     * Convert nested arrays to xml elements
     */
    public function arrayToXml($input, $xMLWriter)
    {
        foreach ($input as $item) {
            if (is_null($item)) {
                continue;
            }
            if ($item['namespaced']) {
                $xMLWriter->startElementNs($item['prefix'], $item['name'], $item['namespace']);
            } else {
                $xMLWriter->startElement($item['name']);
            }
            if (isset($item['attributes']) && count($item['attributes']) > 0) {
                foreach ($item['attributes'] as $key => $attribute) {
                    if ($attribute['namespaced']) {
                        $xMLWriter->startAttributeNs($attribute['prefix'], $attribute['name'], $attribute['namespace']);
                        $xMLWriter->text($attribute['value']);
                        $xMLWriter->endAttribute();
                    } else {
                        $xMLWriter->startAttribute($attribute['name']);
                        $xMLWriter->text($attribute['value']);
                        $xMLWriter->endAttribute();
                    }
                }
            }
            if (! isset($item['childs'])) {
                $xMLWriter->text($item['value']);
            }
            if (isset($item['childs']) && count($item['childs']) > 0) {
                $this->arrayToXml($item['childs'], $xMLWriter);
            }
            $xMLWriter->endElement();
        }
    }
}
