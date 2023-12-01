<?php

namespace Modules\Icommercepayzen\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class IcommercepayzenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        Model::unguard();

        if(!is_module_enabled('Icommercepayzen')){
            $this->command->alert("This module: Icommercepayzen is DISABLED!! , please enable the module and then run the seed");
            exit();
        }
        
        //Validation if the module has been installed before
        $name = config('asgard.icommercepayzen.config.paymentName');
        $paymentMethod =  app('Modules\Icommercepayzen\Services\PayzenService')->getPaymentMethod();

    
        if(!$paymentMethod){

            $options['init'] = "Modules\Icommercepayzen\Http\Controllers\Api\IcommercePayzenApiController";
            
            $options['mode'] = "test";
            $options['minimunAmount'] = 0;
            $options['maximumAmount'] = null;
            $options['showInCurrencies'] = ["COP"];

            $options['siteId'] = null;
            $options['signatureKey'] = null;
      
            $title = 'icommercepayzen::icommercepayzens.single';
            $description = 'icommercepayzen::icommercepayzens.description';

            $params = array(
                'name' => $name,
                'status' => 1,
                'options' => $options,
                'es' => ['title' => trans($title,[],'es'),'description' => trans($description,[],'es')],
                'en' => ['title' => trans($title,[],'en'),'description' => trans($description,[],'en')]
            );
            
            $paymentMethodCreated =  app('Modules\Icommerce\Repositories\PaymentMethodRepository')->create($params);

        }else{

            $this->command->alert("This method has already been installed !!");

        }
   
    }


}