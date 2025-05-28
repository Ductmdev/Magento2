<?php

namespace Bap\GmoPayment\Api;

interface ThreeDSecureUrlInterface
{
    /**
     * Handle redirect success.
     * @param string $orderId
     * @return mixed
     */
    public function getSecureUrl($orderId);
}
