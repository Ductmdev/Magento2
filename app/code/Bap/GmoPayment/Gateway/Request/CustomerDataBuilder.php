<?php

namespace Bap\GmoPayment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

class CustomerDataBuilder implements BuilderInterface
{
    public const FIRST_NAME = 'firstName';
    public const LAST_NAME = 'lastName';
    public const COMPANY = 'company';
    public const PHONE = 'phone';
    public const EMAIL = 'email';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();

        return [
            'customer' => [
                self::FIRST_NAME => $billingAddress->getFirstname(),
                self::LAST_NAME => $billingAddress->getLastname(),
                self::COMPANY => $billingAddress->getCompany(),
                self::PHONE => $billingAddress->getTelephone(),
                self::EMAIL => $billingAddress->getEmail(),
            ]
        ];
    }
}
