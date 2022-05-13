<?php
namespace App\Services\Payment\Gateways;

use Chistel\MultiPayment\AbstractGateway;
use Chistel\MultiPayment\Contracts\Gateway;
use Chistel\MultiPayment\RedirectResponse;
use Chistel\MultiPayment\Response;
use Illuminate\Support\Str;

class InvoiceGateway extends AbstractGateway implements Gateway
{
    /**
     * @var string
     */
    protected $currency;

    /**
     * Get test mode
     *
     * @return bool
     */
    public function getTestMode()
    {
        return false;
    }

    /**
     * Set test mode
     *
     * @param $mode
     *
     * @return void
     */
    public function setTestMode($mode)
    {
    }

    /**
     * Get currency code
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
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
        $this->currency = $currency;
    }

    /**
     * Handle making the purchase
     *
     * @param array $data
     * @return Response|RedirectResponse
     */
    public function purchase(array $data = []): Response|RedirectResponse
    {
        return new Response(true, 'Success', $this->generateInvoiceReference(), $data['amount'], 'NGN');
    }

    /**
     * Return a random string to be used as an invoice reference
     *
     * @return string
     */
    protected function generateInvoiceReference(): string
    {
        return Str::random();
    }

    public function complete(): Response
    {
        // TODO: Implement complete() method.
    }
}
