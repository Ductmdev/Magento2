<?php

namespace Bap\Register\Api;

use Bap\Register\Api\Data\TempCustomerInterface;

interface AccountManagementInterface
{
    public function createTempCustomer(TempCustomerInterface $tempCustomer, $redirectUrl = '');

    public function sendEmailVerification(TempCustomerInterface $tempCustomer, $redirectUrl = '');
}
