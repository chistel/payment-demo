<?php

namespace App\Services\Omnipay\Paystack;

use App\Services\Omnipay\Paystack\Message\CompletePurchaseRequest;
use App\Services\Omnipay\Paystack\Message\PurchaseRequest;
use App\Services\Omnipay\Paystack\Message\RefundRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Class Gateway
 *
 * @package App\Services\Omnipay\Paystack
 */
class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Paystack';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'secret_key' => '',
            'public_key' => '',
        ];
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getParameter('secret_key');
    }

    /**
     * @param $value
     * @return Gateway
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secret_key', $value);
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->getParameter('public_key');
    }

    /**
     * @param $value
     * @return Gateway
     */
    public function setPublicKey($value)
    {
        return $this->setParameter('public_key', $value);
    }

    /**
     * @inheritDoc
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function completePurchase(array $options = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function refund(array $options = [])
    {
        return $this->createRequest(RefundRequest::class, $options);
    }
}
