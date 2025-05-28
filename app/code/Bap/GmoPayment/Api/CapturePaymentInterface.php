<?php

namespace Bap\GmoPayment\Api;

interface CapturePaymentInterface
{
    public function capture(array $arguments);
}
