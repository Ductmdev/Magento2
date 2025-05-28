<?php

namespace Bap\GmoPayment\Gateway\Http\Client;

use Bap\GmoPayment\Api\CapturePaymentInterface;
use Magento\Framework\Exception\LocalizedException;

class CaptureTransaction extends AbstractTransaction
{
    protected function process(array $inputs)
    {
        if (!$this->paymentService instanceof CapturePaymentInterface) {
            throw new LocalizedException(__('Capture action are not supported.'));
        }

        return $this->paymentService->capture($inputs);
    }
}
