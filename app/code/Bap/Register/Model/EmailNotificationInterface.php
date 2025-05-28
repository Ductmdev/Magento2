<?php

namespace Bap\Register\Model;

use Bap\Register\Api\Data\TempCustomerInterface;

interface EmailNotificationInterface
{
    const NEW_ACCOUNT_EMAIL_VERIFICATION = 'verification';

    public function newVerification(
        TempCustomerInterface $tempCustomer,
        $type = self::NEW_ACCOUNT_EMAIL_VERIFICATION,
        $backUrl = '',
        $storeId = 0,
        $sendEmailStoreId = null
    );
}
