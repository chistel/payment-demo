<?php

namespace App\Http\Controllers;

use Chistel\MultiPayment\Actions\CancelPaymentAction;
use Chistel\MultiPayment\Actions\CompletePaymentAction;
use Chistel\MultiPayment\Actions\InitiatePaymentAction;
use Chistel\MultiPayment\Exceptions\InvalidGatewayConfigException;
use Chistel\MultiPayment\Exceptions\InvalidGatewayDriverException;
use Chistel\MultiPayment\Exceptions\UnSupportedGatewayException;
use Chistel\MultiPayment\GatewayManager;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private InitiatePaymentAction $initiatePaymentAction,
        private CompletePaymentAction $completePaymentAction,
        private GatewayManager $gatewayManager
    ) {
    }

    public function index()
    {
        return view('payment');
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     * @throws InvalidGatewayDriverException
     */
    public function processPayment(Request $request): JsonResponse|RedirectResponse
    {
        $gateway = $request->gateway;
        try {
            $response = $this->initiatePaymentAction->execute($request->email, $gateway, $request->toArray());

            if ($response->isRedirect()) {
                return redirect()->to($response->redirectUrl());
               /* return \response()->json(
                    [
                        'status' => true,
                        'redirectUrl' => $response->redirectUrl(),
                        'data' => $response->data() ?? [],
                    ]
                );*/
            }
            if ($response->failed()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => $response->message(),
                        'redirectUrl' => '',
                    ]
                );
            }
            return response()->json(
                [
                    'status' => true,
                    'data' => [
                        'reference' => $response->transactionRef(),
                        'amount' => $response->amount(),
                    ],
                    'redirectUrl' => route('billing.gateway.success', ['gateway' => $gateway]),
                ]
            );
        } catch (UnSupportedGatewayException | InvalidGatewayConfigException $e) {
            $message = ($e instanceof InvalidGatewayConfigException) ? '' : $e->getMessage();
            return response()->json(
                [
                    'status' => false,
                    'message' => $message,
                    'redirectUrl' => '',
                ]
            );
        }
    }

    /**
     * Handle completing payment from off-site/onsite payment gateway
     *
     * /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function gatewayReturn(Request $request): JsonResponse|RedirectResponse
    {
        $gateway = $request->gateway;
        try {
            $response = $this->completePaymentAction->execute($gateway, $request->email, []);
        } catch (Exception $e) {
            logger()->error('gateway return error : ' . $e);
            return response()->json(
                [
                    'status' => false,
                    'message' => $e->getMessage(),
                    'redirectUrl' => '',
                ]
            );
        }

        if (!$response->success()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $response->message(),
                    'redirectUrl' => route('billing.gateway.cancel', ['gateway' => $gateway])
                ]
            );
        }
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'reference' => $response->transactionRef(),
                    'amount' => $response->amount(),
                ],
                'redirectUrl' => route('billing.gateway.success', ['gateway' => $gateway])
            ]
        );
    }

    /**
     * Display pending payment page
     *
     * @param Request $request
     * @throws InvalidGatewayDriverException
     * @throws UnSupportedGatewayException
     */
    public function pending(Request $request)
    {
        $paymentMethod = $request->gateway;
        $gateway = $this->gatewayManager->create($paymentMethod);
    }


    public function success()
    {
        echo 'yes it done';
    }


    /**
     * Cancel payment
     *
     * @param Request $request
     * @param
     */
    public function gatewayCancel(Request $request)
    {
        app(CancelPaymentAction::class)->execute($request->email, $request->gateway);
    }
}
