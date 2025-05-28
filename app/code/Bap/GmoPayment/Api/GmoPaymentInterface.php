<?php

namespace Bap\GmoPayment\Api;

interface GmoPaymentInterface
{
    public function entryTran(array $arguments);

    public function execTran(string $accessId, string $accessPass, array $arguments);
}
