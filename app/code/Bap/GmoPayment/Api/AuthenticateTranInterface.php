<?php

namespace Bap\GmoPayment\Api;

interface AuthenticateTranInterface
{
    public function authenticate(string $accessId, string $accessPass);
}
