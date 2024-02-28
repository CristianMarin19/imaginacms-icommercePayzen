<?php

namespace Modules\Icommercepayzen\Entities;

class Payzen
{

	public $urlAction; 
  public $vadsCurrency;
  public $vadsAmount;
  public $vadsCtxMode;
  public $vadsActionMode;
  public $vadsPageAction;
  public $vadsPaymentConfig;
  public $vadsSiteId;
  public $vadsTransDate;
  public $vadsTransId;

  public $vadsOrderId;
  public $vadsOrderInfo;

  public $vadsVersion;
  public $vadsUrlReturn;
  public $vadsReturnMode;

  public $vadsCustEmail;
  public $vadsCustFirstName;
  public $vadsCustLastName;

  public $signature;
  	
  private $_htmlFormCode;
  private $_htmlCode;
  private $nameForm;

  //Construct
  function __construct(){ }

  //========================================= SET VARIABLES
  public function setUrlgate($url){
    $this->urlAction=$url;
  }

 
  public function setCurrency($vadsCurrency){
    $this->vadsCurrency = $vadsCurrency;
  }

  public function setVadsSiteId($vadsSiteId){
    $this->vadsSiteId = $vadsSiteId;
  }

  public function setVadsTransId($vadsTransId){
  	    $this->vadsTransId = $vadsTransId;
  }

  public function setVadsTransDate($vadsTransDate){
    $this->vadsTransDate = $vadsTransDate;
  }

  //Amount in Cents
  public function setAmount($vadsAmount){
  	   $this->vadsAmount = $vadsAmount * 100;
  }
  
  public function setVadsUrlReturn($vadsUrlReturn){
  	   $this->vadsUrlReturn= $vadsUrlReturn;
  }

  public function setVadsReturnMode($vadsReturnMode="GET"){
    $this->vadsReturnMode = $vadsReturnMode;
  }

  public function setSignature($signature){
    $this->signature = $signature;
  }

  public function setMode($vadsCtxMode){
    $this->vadsCtxMode = strtoupper($vadsCtxMode);
  }

  public function setVadsActionMode($vadsActionMode){
    $this->vadsActionMode = $vadsActionMode;
  }

  public function setVadsPageAction($vadsPageAction){
    $this->vadsPageAction = $vadsPageAction;
  }

  public function setVadsPaymentConfig($vadsPaymentConfig){
    $this->vadsPaymentConfig = $vadsPaymentConfig;
  }
 
  public function setVadsVersion($vadsVersion){
    $this->vadsVersion = $vadsVersion;
  }

  public function setVadsOrderId($reference){
    $this->vadsOrderId = $reference;
  }

  public function setVadsOrderInfo($info){
    $this->vadsOrderInfo = trans("icommerce::cms.orderDate").": ".$info;
  }

  public function setVadsCustEmail($email){
    $this->vadsCustEmail = $email;
  }

  public function setVadsCustFirstName($firstName){
    $this->vadsCustFirstName = $firstName;
  }

  public function setVadsCustLastName($lastName){
    $this->vadsCustLastName = $lastName;
  }

  /**
  * FORM - Set Form Name
  */
  public function setNameForm($name = 'payForm')
  {
    $this->nameForm = $name;
  }

  /**
  * FORM - Add input
  */
  private function _addInput($string, $value)
  {
    return '<input type="hidden" name="' .$string. '" value="' . htmlentities($value, ENT_COMPAT, 'UTF-8') . '"/>' . "\n";
  }

  /**
  * FORM - Add make fields
  */
  public function _makeFields(){
  
    $this->_htmlFormCode.=$this->_addInput('vads_action_mode',$this->vadsActionMode);
    $this->_htmlFormCode.=$this->_addInput('vads_amount',$this->vadsAmount);
    $this->_htmlFormCode.=$this->_addInput('vads_ctx_mode',$this->vadsCtxMode);
    $this->_htmlFormCode.=$this->_addInput('vads_currency',$this->vadsCurrency);
    $this->_htmlFormCode.=$this->_addInput('vads_page_action',$this->vadsPageAction);
    $this->_htmlFormCode.=$this->_addInput('vads_payment_config',$this->vadsPaymentConfig);
    $this->_htmlFormCode.=$this->_addInput('vads_site_id',$this->vadsSiteId);
    $this->_htmlFormCode.=$this->_addInput('vads_trans_date',$this->vadsTransDate);
    $this->_htmlFormCode.=$this->_addInput('vads_trans_id',$this->vadsTransId);
    $this->_htmlFormCode.=$this->_addInput('vads_version',$this->vadsVersion);

    $this->_htmlFormCode.=$this->_addInput('vads_order_id',$this->vadsOrderId);
    $this->_htmlFormCode.=$this->_addInput('vads_order_info',$this->vadsOrderInfo);

    $this->_htmlFormCode.=$this->_addInput('vads_return_mode',$this->vadsReturnMode);
    $this->_htmlFormCode.=$this->_addInput('vads_url_return',$this->vadsUrlReturn);

    $this->_htmlFormCode.=$this->_addInput('vads_cust_email',$this->vadsCustEmail);
    $this->_htmlFormCode.=$this->_addInput('vads_cust_first_name',$this->vadsCustFirstName);
    $this->_htmlFormCode.=$this->_addInput('vads_cust_last_name',$this->vadsCustLastName);
    
    $this->_htmlFormCode.=$this->_addInput('signature',$this->signature);

  }
  
  /**
  * FORM - Make Form
  */
  private function _makeForm()
  {
        $this->_htmlCode .= '<form action="' . $this->urlAction . '" method="POST" id="'.$this->nameForm.'" name="'.$this->nameForm.'"/>' . "\n";
        $this->_htmlCode .=$this->_htmlFormCode;

  }
 
  /**
  * FORM - Render
  */
  public function renderPaymentForm()
  {
  		$this->setNameForm();

      $time = time();
      error_log("---Payment page sampledan gelen loglar---".$time,0);

      $this->_makeFields();
      $this->_makeForm();

      return $this->_htmlCode;
  }

  /**
  * Execute Redirection
  */
  public function executeRedirection()
  { 
    echo $this->renderPaymentForm();
    echo '<script>document.forms["'.$this->nameForm.'"].submit();</script>';
  }
	

}