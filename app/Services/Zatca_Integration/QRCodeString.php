<?php

namespace Allam\Zatca;

/**
 * A class defines zatca qr base64 string
 */
class QRCodeString
{
    private $result = '';

    public function __construct($params)
    {
        foreach ($params as $key => $value) {
            $tag = $key + 1;
            $length = $this->stringLen($value);
            $this->result .= $this->toString($tag, $length, $value);
        }
    }

    /**
     * Get string number of bytes start
     */
    public function stringLen($value)
    {
        return strlen($value);
    }

    /**
     * Get string representing the encoded TLV data structure
     */
    public function toString($tag, $length, $value)
    {

        return $this->__toHex($tag).$this->__toHex($length).($value);

    }

    /**
     * Convert the string value to hex start
     */
    public function __toHex($value)
    {
        return pack('H*', sprintf('%02X', $value));
    }

    /**
     * Convert the string value to hex end
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Convert qr string value to base64 encode
     */
    public function toBase64()
    {
        return base64_encode($this->result);
    }
}
