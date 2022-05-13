<?php

namespace App\Services\Omnipay\Paystack\Message;

use Omnipay\Common\Message\AbstractResponse;

class RefundResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data['data'], $transaction['status']) && ($transaction = $this->data['data']['transaction']) && $transaction['status'] === 'reversed';
    }

    public function isRedirect()
    {
        return false;
    }

    public function getMessage()
    {
        if (isset($this->data['message']) && $message = $this->data['message']) {
            return $message;
        }

        return '';
    }

    public function getCode()
    {
        if (isset($this->data['data']) && $data = $this->data['data']) {
            return $data['access_code'];
        }

        return '';
    }

    public function getTransactionReference()
    {
        if (isset($this->data['data'], $data['transaction']) && ($data = $this->data['data']) && ($transaction = $data['transaction'])) {
            return $transaction['reference'];
        }

        return '';
    }
}
