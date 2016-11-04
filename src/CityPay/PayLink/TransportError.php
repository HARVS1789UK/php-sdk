<?php
namespace CityPay\PayLink;

use CityPay\Lib\ApiMessage;

class TransportError
    extends ApiMessage
{
    /**
     *
     * @var type 
     */
    private $responseCode;

    /**
     * 
     * @param type $responseCode
     */
    protected function __construct($responseCode) {
        parent::__construct();
        $this->responseCode = $responseCode;
    }
}