<?php
/*
 * Copyright (C) 2022,  Chistel Brown,  - All Rights Reserved
 * @project                  multi-payment
 * @file                           PaystackGateway.php
 * @author                  Chistel Brown
 * @site                          <http://twitter.com/chistelbrown>
 * @email                      chistelbrown@gmail.com
 * @lastmodified     12/05/2022, 10:56 PM
 */

namespace App\Services\Payment\Gateways;

use Chistel\MultiPayment\AbstractGateway;
use Chistel\MultiPayment\Contracts\Gateway;
use Chistel\MultiPayment\RedirectResponse;
use Chistel\MultiPayment\Response;
use Exception;
use Omnipay\Common\GatewayFactory as Omnipay;
use App\Services\Omnipay\Paystack\Gateway as OmnipayPaystack;

class PaystackGateway extends AbstractGateway implements Gateway
{
    /**
     * Initialize payment gateway.
     *
     * @param bool $testMode
     */
    public function __construct(bool $testMode = false)
    {
        //$apiKey = $testMode ? config('services.paystack.api_key_test') : config('services.paystack.api_key_live');
        $apiKey = config('services.paystack.secret_key');
        $omnipay = new Omnipay();
        $this->gateway = $omnipay->create('\\' . OmnipayPaystack::class);
        $this->gateway->setTestMode($testMode);
        $this->gateway->setSecretKey($apiKey);
    }

    /**
     * Get test mode
     *
     * @return bool
     */
    public function getTestMode(): bool
    {
        return $this->gateway->getTestMode();
    }

    /**
     * Set test mode
     *
     * @param $mode
     *
     * @return void
     */
    public function setTestMode($mode): void
    {
        $this->gateway->setTestMode($mode);
    }

    /**
     * Get currency code
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->gateway->getCurrency();
    }

    /**
     * Set the currency code
     *
     * @param string $currency
     *
     * @return void
     */
    public function setCurrency($currency)
    {
        $this->gateway->setCurrency($currency);
    }


    public function purchase(array $data = []): Response|RedirectResponse
    {
        $purchase = $this->gateway->purchase(
            [
                'amount' => $data['amount'],
                'email' => $data['email'],
                'currency' => 'NGN',
                'returnUrl' => route('billing.gateway.complete', ['gateway' => 'paystack', 'email' => $this->payer])
            ]
        )->send();
        $paymentReference = $purchase->getTransactionReference();

        $this->storeParams([
                'reference' => $paymentReference,
            ]);

        if ($purchase->isRedirect()) {
            return new RedirectResponse(
                $purchase->getRedirectUrl()
            );
        }

        return new Response(
            $purchase->isSuccessful(),
            $purchase->getMessage(),
            $paymentReference,
            $this->getGatewayReturnedAmount($purchase),
            '',
        );
    }

    /**
     * Return the gateway charged amount - formatted
     *
     * @param       $response
     * @param float $default
     *
     * @return float
     */
    protected function getGatewayReturnedAmount($response, float $default = 0.00): float
    {
        $data = $response->getData();
        if (isset($data['data']['amount'])) {
            return (float)($data['data']['amount'] / 100);
        }
        return $default;
    }

    /**
     * @param array $data
     * @return Response
     * @throws Exception
     */
    public function complete(array $data = []): Response
    {
        $params = $this->retrieveParams();
        $this->clearParams();

        $response = $this->gateway->completePurchase()->setTransactionReference($params['reference'])->send();
        if (!$response->isSuccessful()) {
            return new Response(
                false,
                'Something went wrong, your payment cold not be verified',
                $params['reference'],
                $this->getGatewayReturnedAmount($response)
            );
        }

        return new Response(
            $response->isSuccessful(),
            $response->getMessage(),
            $this->getTransactionRef($response),
            $this->getGatewayReturnedAmount($response)
        );
    }

    /**
     * Get transaction reference or return an empty string is none exists (because of payment failure).
     *
     * @param $response
     *
     * @return string
     */
    protected function getTransactionRef($response): string
    {
        $ref = $response->getTransactionReference();
        if (!is_null($ref)) {
            return $ref;
        }
        return '';
    }

    protected function gatewayName(): string
    {
        return 'paystack';
    }
}
