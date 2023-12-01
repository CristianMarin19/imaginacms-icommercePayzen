<?php

namespace Modules\Icommercepayzen\Entities;

class Payzen
{

	public $urlAction; 
  public $currency;
  public $amount;
  public $mode;
  public $vadsActionMode;
  public $vadsPageAction;
  public $vadsPaymentConfig;
  public $vadsSiteId;
  public $vadsTransDate;
  public $vadsTransId;
  public $vadsVersion;
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

 
  public function setCurrency($currency){
    $this->currency = $currency;
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
  public function setAmount($amount){
  	   $this->amount = $amount * 100;
  }
  
  public function setRedirectUrl($redirectUrl){
  	   $this->redirectUrl =$redirectUrl;
  }

  public function setSignature($signature){
    $this->signature = $signature;
  }

  public function setMode($mode){
    $this->mode = $mode;
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
    $this->_htmlFormCode.=$this->_addInput('vads_amount',$this->amount);
    $this->_htmlFormCode.=$this->_addInput('vads_ctx_mode',$this->mode);
    $this->_htmlFormCode.=$this->_addInput('vads_currency',$this->currency);
    $this->_htmlFormCode.=$this->_addInput('vads_page_action',$this->vadsPageAction);
    $this->_htmlFormCode.=$this->_addInput('vads_payment_config',$this->vadsPaymentConfig);
    $this->_htmlFormCode.=$this->_addInput('vads_site_id',$this->vadsSiteId);
    $this->_htmlFormCode.=$this->_addInput('vads_trans_date',$this->vadsTransDate);
    $this->_htmlFormCode.=$this->_addInput('vads_trans_id',$this->vadsTransId);
    $this->_htmlFormCode.=$this->_addInput('vads_version',$this->vadsVersion);
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