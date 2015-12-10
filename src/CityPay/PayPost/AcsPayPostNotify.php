<?php
namespace CityPay\PayPost;

use CityPay\Lib\Rpc\Http;
use CityPay\Lib\ApiEncoding;
use CityPay\Lib\ApiRequest;
use CityPay\Lib\InvalidGatewayResponseException;
use CityPay\Lib\NamedValueNotFoundException;
use CityPay\Encoding\Serializable;
use CityPay\Encoding\FormUrl\FormUrlSerializable;

/**
 * The AcsPayPostNotify class allows the Merchant Application to transmit
 * the pares and md parameters received through a HTTP POST to the Merchant
 * Application URL to the PayPost API to obtain a report of the status of
 * the transaction.
 *  
 */
class AcsPayPostNotify
    extends ApiRequest
    implements FormUrlSerializable,
        Serializable
{
    /**
     * The merchant identifier for processing the relevant transaction.
     *
     * @var string 
     */
    private $merchantId;
    
    /**
     * The licence key associated with the merchant identifier for processing
     * of the relevant transaction.
     *
     * @var string 
     */
    private $licenceKey;
    
    /**
     * The payment authorisation result string generated by the payment card
     * issuers' access control server ("ACS") and returned to the Merchant
     * Application indirectly via a HTTP POST operation from the Customer
     * Browser to the Merchant Application using the Merchant Terminal URL
     * provided in the originating PayPost request.
     * 
     * @var string 
     */
    private $paRes;
    
    /**
     * Merchant data associated with the relevant payment authorisation
     * session required to maintain continuity between the PayPost, the
     * Merchant Application and the payment card issuers' ACS.
     * 
     * @var string 
     */
    private $md;
    
    /**
     *
     */
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     * @return \CityPay\PayPost\AcsPayPostRequest
     */
    protected function this() {
        return $this;
    }
    
    /**
     * 
     * @param type $md
     * @return type
     */
    public function md($md) {
        $this->md = $md;
        return $this->this();
    }
    
    /**
     * 
     * @param string $merchantId
     * @return \CityPay\PayPost\AcsPayPostRequest
     */
    public function merchantId($merchantId) {
        $this->merchantId = $merchantId;
        return $this->this();
    }
    
    /**
     * 
     * @param string $licenceKey
     * @return \CityPay\PayPost\AcsPayPostRequest
     */
    public function licenceKey($licenceKey) {
        $this->licenceKey = $licenceKey;
        return $this->this();
    }
    
    /**
     * 
     * @param string $paRes
     */
    public function paRes($paRes) {
        $this->paRes = $paRes;
        return $this->this();
    }

    /**
     * 
     * @return \CityPay\Lib\ApiMessage
     * @throws InvalidGatewayResponseException
     */
    public function notifyAcsResult() {
        //
        //
        //
        $deserializedPayload = null;
        $responseCode = self::invokeRpcAndDeserializeResponse(
            ApiEndpoints::notifyAcsResult(),
            ApiEncoding::FORM_URL,
            ApiEncoding::XML,
            $deserializedPayload
        );

        if ($deserializedPayload == null) {
            throw new InvalidGatewayResponseException(
                $responseCode
            );
        }

        //
        //  If a connection to the PayPOST API server was successfully
        //  established, the response code returned by the API server
        //  is HttpRpc.HTTP_OK.
        //
        if ($responseCode == Http::HTTP_OK) {
            try {
                return (new PayPostResponse())
                    ->xmlDeserialize($deserializedPayload)
                    ->validate($this->merchantId, $this->licenceKey);
            } catch (NamedValueNotFoundException $e) {
                //
                //  TODO: Determine better result to return
                //
                return null;
            }
        } else {
            //
            //  TODO: Determine better result to return
            //
            return null;
        }
    }
    
    /**
     * 
     * @return array
     */
    private function serialize() {
        return array(
            "paRes" => $this->paRes,
            "md" => $this->md
        );
    }
    
    /**
     * 
     * @return type
     */
    public function formUrlSerialize() {
        return $this->serialize();
    }

    /**
     * 
     * @return type
     */
    public function jsonSerialize() {
        return $this->serialize();
    }
}

