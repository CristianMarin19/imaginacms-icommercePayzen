<?php

namespace Modules\Icommercepayzen\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

//Request
use Modules\Icommercepayzen\Http\Requests\InitRequest;

// Base Api
use Modules\Icommerce\Http\Controllers\Api\OrderApiController;
use Modules\Icommerce\Http\Controllers\Api\TransactionApiController;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Repositories Icommerce
use Modules\Icommerce\Repositories\TransactionRepository;
use Modules\Icommerce\Repositories\OrderRepository;

use Modules\Icommercepayzen\Repositories\IcommercePayzenRepository;


class IcommercePayzenApiController extends BaseApiController
{

  private $icommercepayzen;
  private $order;
  private $orderController;
  private $transaction;
  private $transactionController;

  private $payzenService;
  private $paymentMethod;
  private $log = "Icommercepayzen: ApiController|";

  public function __construct(
    IcommercePayzenRepository $icommercepayzen,
    OrderRepository $order,
    OrderApiController $orderController,
    TransactionRepository $transaction,
    TransactionApiController $transactionController
  ) {
    $this->icommercepayzen = $icommercepayzen;

    $this->order = $order;
    $this->orderController = $orderController;
    $this->transaction = $transaction;
    $this->transactionController = $transactionController;

    $this->payzenService = app('Modules\Icommercepayzen\Services\PayzenService');

    // Get Payment Method Configuration
    $this->paymentMethod = $this->payzenService->getPaymentMethod();
  }

  /**
   * Init Calculations (Validations to checkout)
   * @param Requests request
   * @return mixed
   */
  public function calculations(Request $request)
  {

    try {

      $response = $this->icommercepayzen->calculate($request->all(), $this->paymentMethod->options);
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }

    return response()->json($response, $status ?? 200);
  }



  /**
   * ROUTE - Init data
   * @param Requests request
   * @param Requests orderId
   * @return route
   */
  public function init(Request $request)
  {

    try {

      $data = $request->all();

      $this->validateRequestApi(new InitRequest($data));

      $orderID = $request->orderId;
      //\Log::info('Module Icommercepayzen: Init-ID:'.$orderID);

      // Order
      $order = $this->order->find($orderID);
      $statusOrder = 1; // Processing

      // Validate minimum amount order
      if (isset($this->paymentMethod->options->minimunAmount) && $order->total < $this->paymentMethod->options->minimunAmount)
        throw new \Exception(trans("icommercepayzen::icommercepayzens.messages.minimum") . " :" . $this->paymentMethod->options->minimunAmount, 204);

      // Create Transaction
      $transaction = $this->validateResponseApi(
        $this->transactionController->create(new Request(["attributes" => [
          'order_id' => $order->id,
          'payment_method_id' => $this->paymentMethod->id,
          'amount' => $order->total,
          'status' => $statusOrder
        ]]))
      );

      // Encri
      $eUrl = paymentezEncriptUrl($order->id, $transaction->id);

      $redirectRoute = route('icommercepayzen', [$eUrl]);

      // Response
      $response = ['data' => [
        "redirectRoute" => $redirectRoute,
        "external" => true
      ]];
    } catch (\Exception $e) {
      \Log::error($e->getMessage());
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }


    return response()->json($response, $status ?? 200);
  }

  /**
   * Response Api Method - Confirmation
   * @param Requests request
   * @return route
   */
  public function confirmation(Request $request)
  {

    \Log::info($this->log . 'Confirmation|INIT|' . time());
    $response = ['msj' => "Proceso Valido"];

    try {

      dd("YA VAAA");

      $data = $request->all();

      if (isset($data['vads_hash'])) {

        //$dataTransaction = $request->data['transaction'];
        //\Log::info('Module Icommercewompi: Transaction: '.json_encode($dataTransaction))

        // Get order id and transaction id from request
        //$inforReference = icommercewompi_getInforRefCommerce($dataTransaction['reference']);

        //$order = $this->order->find($inforReference['orderId']);
        //\Log::info('Module Icommercewompi: Order Status Id: '.$order->status_id);

        // Status Order 'pending'
        if ($order->status_id == 1) {

          // Default Status Order
          //$newStatusOrder = 7; // Status Order Failed

          // Get States From Commerce
          //$codTransactionState = "";
          //$transactionState = $dataTransaction['status'];

          // Get Signatures - OJO AQUI TOCA VER SI LLEGA LA KEY
          //$payzen = $this->payzenService->makeSignature(null,$lakeyyyy,$data);

          // Check signatures
          /*
            if ($x_signature == $signature) {
                $newStatusOrder =  $payzenService->getStatusOrder($data['vads_trans_status']);
            }else{
              $codTransactionState = "Error - Sign";
              \Log::info('Module Icommercewompi: **ERROR** en Firma');
            }
            */

          // Update Transaction
          /*
          $transaction = $this->validateResponseApi(
            $this->transactionController->update($inforReference['transactionId'], new Request(
              [
                "attributes" => [
                  'order_id' => $order->id,
                  'payment_method_id' => $this->paymentMethod->id,
                  'amount' => $order->total,
                  'status' => $newStatusOrder,
                  'external_status' => $transactionState,
                  'external_code' => $codTransactionState
                ]
              ]
            ))
          );
          */

          // Update Order Process
          /*
          $orderUP = $this->validateResponseApi(
            $this->orderController->update($order->id, new Request(
              [
                "attributes" => [
                  'order_id' => $order->id,
                  'status_id' => $newStatusOrder
                ]
              ]
            ))
          );
          */

        }
      }

      \Log::info($this->log . 'Confirmation - END');
      
    } catch (\Exception $e) {
      \Log::error($this->log . 'Message: ' . $e->getMessage());
      \Log::error($his->log . 'Code: ' . $e->getCode());
    }


    return response()->json($response, $status ?? 200);
  }
}
