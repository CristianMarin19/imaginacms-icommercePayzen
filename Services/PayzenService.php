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
        $payzen->setVadsTransId($this->getOrderRefCommerce($order,$transaction));
        $payzen->setVadsTransDate($order->created_at->format('Ymdhis'));//Final Result = AAAAMMDDHHMMSS
        $payzen->setVadsVersion( config('asgard.icommercepayzen.config.apiUrl.version'));
        $payzen->setSignature($this->makeSignature($payzen,$paymentMethod->options->signatureKey));

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
        $reference = $order->id."-".$transaction->id."-".time();
        return $reference;
    }

     /**
     * Make the Signature
     * @param Payzen (Class with configurations)
     * @param signKey (signatureKey from DB)
     * @return signature
     */
    public function makeSignature($payzen,$signKey)
    {   

        $content = $payzen->vadsActionMode."+".$payzen->amount."+".strtoupper($payzen->mode)."+".$payzen->currency."+".$payzen->vadsPageAction."+".$payzen->vadsPaymentConfig."+".$payzen->vadsSiteId."+".$payzen->vadsTransDate."+".$payzen->vadsTransId."+".$payzen->vadsVersion."+".$signKey;
        
        //Encoding base64 encoded chain with SHA-256 algorithm
        $signature = base64_encode(hash_hmac('sha256',$content, $signKey, true));

        return $signature;
    }

     /**
     * Get Status to Order
     * @param String cod
     * @return Int 
     */
    //https://payzen.io/lat/form-payment/quick-start-guide/tratamiento-de-los-datos-de-la-respuesta.html
    public function getStatusOrder($cod)
    {

        switch ($cod) {

            case "ACCEPTED":
                $newStatus = 13; //processed
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
        
        \Log::info($this->log.'NewStatus: '.$newStatus);

        return $newStatus; 

    }
    


}