<?php

namespace Modules\Icommercepayzen\Services;

use Modules\Icommerce\Repositories\PaymentMethodRepository;

use Modules\Icommercepayzen\Entities\Payzen as PayzenEntity;

class PayzenService
{

	private $paymentMethod;
    private $log = "Icommercepayzen: PayzenService|";
	
	public function __construct(
       PaymentMethodRepository $paymentMethod
    ){
        $this->paymentMethod = $paymentMethod;
    }

    /*
    * Get shipping method by name
    */
	public function getPaymentMethod()
    {

        $paymentName = config('asgard.icommercepayzen.config.paymentName');
        
        $params = ['filter' => ['field' => 'name']];
        $paymentMethod = $this->paymentMethod->getItem($paymentName,json_decode(json_encode($params)));

        return $paymentMethod;

	}

     /**
    * Configuration to reedirect
    * @param 
    * @return Object Configuration
    */
	public function create($order,$transaction)
    {
        // Get Payment Method Configuration
        $paymentMethod = $this->getPaymentMethod();

        //Base Infor
		$payzen = new PayzenEntity();

        $mode = $paymentMethod->options->mode;
        $urlPayzen =  config('asgard.icommercepayzen.config.apiUrl.'.$mode);

        $payzen->setUrlgate($urlPayzen);

        $payzen->setAmount(round($order->total)); //No admite decimales
        $payzen->setMode($mode);
        $payzen->setCurrency($this->getCurrencyIso($order->currency_code));
        $payzen->setVadsActionMode("INTERACTIVE");//Modo de adquisición de la información del medio de pago
        $payzen->setVadsPageAction("PAYMENT");//Acción a realizar
        $payzen->setVadsPaymentConfig("SINGLE");//Tipo de pago
        $payzen->setVadsSiteID($paymentMethod->options->siteId);
        $payzen->setVadsTransId($this->getTransactionId($order));
        $payzen->setVadsTransDate($this->getTransDate($order));
        $payzen->setVadsVersion(config('asgard.icommercepayzen.config.apiUrl.version'));
        
        //Order Data
        $payzen->setVadsOrderId($this->getOrderRefCommerce($order,$transaction));
        $payzen->setVadsOrderInfo($order->created_at->format('d-m-Y h:i:s'));

        //Return Configurations
        $payzen->setVadsUrlReturn(Route("icommercepayzen.response",$order->id));
        $payzen->setVadsReturnMode();

        //Order Customer Data
        $payzen->setVadsCustEmail($order->email);
        $payzen->setVadsCustFirstName($order->first_name);
        $payzen->setVadsCustLastName($order->last_name);

        $payzen->setSignature($this->makeSignature((array)$payzen,$paymentMethod->options->signatureKey));

        return $payzen;

	}

    /**
     * Code according to ISO 4217 standard
     */
    public function getCurrencyIso($currencyCode)
    {
        if($currencyCode=="USD")    $currencyISO = 840;
        if($currencyCode=="COP")    $currencyISO = 170;

        return $currencyISO;
    }

    /**
    * Get Order Reference Commerce
    * @param $order
    * @param $transaction
    * @return reference
    */
    public function getOrderRefCommerce($order,$transaction)
    {
        $reference = $order->id."-".$transaction->id;
        return $reference;
    }

    /*
    * https://payzen.io/lat/form-payment/error-code/error-03.html
    * Create transaction id to Payzen
    */
    public function getTransactionId($order)
    {
        
        $transactionId = $order->id;
        $digits = strlen($transactionId);

        if($digits<6){
            $faltan = 6 - $digits;
            $allowedChars = 'abcdefghijklmnopqrstuvwxyz';
            $transactionId.= substr(str_shuffle($allowedChars), 0, $faltan);
        }
        
        return $transactionId;
    }

    /**
     * https://payzen.io/lat/error-code/error-04.html
     * Trans date in UTC | Format AAAAMMDDHHMMSS / 24Hrs
     */
    public function getTransDate($order)
    {

        $dateUtc = new \DateTime("now", new \DateTimeZone("UTC"));
        $formated = $dateUtc->format('YmdHis');

        return $formated;
        
    }

     /**
     * Make the Signature
     * @param Params (Array)
     * @param signKey (signatureKey from DB)
     * @return signature
     */
    public function makeSignature($params,$signKey)
    {   
        
        $content = "";

        //Sort fields alphabetically
        ksort($params);
        
        //search in each field
        foreach($params as $nom=>$valeur){
            //Only fields with prefix'vads'
            if (substr($nom,0,4)=='vads'){
                $content .= $valeur."+";
            }
        }

        //Add the key to the end of the string
        $content.= $signKey;
        //\Log::info($this->log.'makeSignature|Content: '.$content);

        //Encoding base64 encoded chain with SHA-256 algorithm
        $signature = base64_encode(hash_hmac('sha256',$content, $signKey, true));

        return $signature;
    }

     /**
     * Get Status to Order
     * @param String cod
     * @return Int 
     */
    public function getStatusOrder($cod)
    {

        switch ($cod) {

            case "ACCEPTED":
                $newStatus = 13; //processed
            break;

            case "AUTHORISED":
                $newStatus = 13; //processed
            break;

            case "CAPTURED":
                $newStatus = 13; //processed
            break;

            case "AUTHORISED_TO_VALIDATE":
                $newStatus = 11; //confirming payment
            break;

            case "WAITING_FOR_PAYMENT":
                $newStatus = 11; //confirming payment
            break;

            case "ABANDONED":
                $newStatus = 3; //cancelled
            break;

            case "CANCELLED":
                $newStatus = 3; //cancelled
            break;

            case "EXPIRED":
                $newStatus = 14; //expired
            break;

            case "REFUSED":
                $newStatus = 5; //denied
            break;

            case "CAPTURE_FAILED":
                $newStatus = 7; //failed
            break;
            
            default:
                $newStatus = 1; //Pending
        }
        
        \Log::info($this->log.'getStatusOrder|NewStatus: '.$newStatus);

        return $newStatus; 

    }

    /**
    * Get Infor Reference From Commerce
    * @param $reference
    * @return array
    */
    function getInforRefCommerce($reference)
    {

        $result = explode('-',$reference);

        $infor['orderId'] = $result[0];
        $infor['transactionId'] = $result[1];

        \Log::info($this->log.'OrderId: '.$infor['orderId']);
        \Log::info($this->log.'TransactionId: '. $infor['transactionId']);
           
        return $infor;
    }

    
}