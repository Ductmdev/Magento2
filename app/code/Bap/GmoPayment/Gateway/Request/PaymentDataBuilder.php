<?php

namespace Bap\GmoPayment\Gateway\Request;

use Bap\GmoPayment\Enum\GmoConst;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class PaymentDataBuilder implements BuilderInterface
{
    public const PAYMENT_DATA = 'payment';

    public function build(array $buildSubject): array
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $payment = $paymentDO->getPayment();
        $additionalData = $payment->getAdditionalInformation();

        return [
            self::PAYMENT_DATA => [
                GmoConst::KEY_AMOUNT => SubjectReader::readAmount($buildSubject),
                GmoConst::KEY_ORDER_ID => $order->getOrderIncrementId(),
                GmoConst::KEY_CARD_NUMBER => $additionalData['cc_number'] ?? '',
                GmoConst::KEY_EXPIRE => substr($additionalData['cc_exp_year'] ?? '', -2) . sprintf('%02d', $additionalData['cc_exp_month'] ?? ''),
                GmoConst::KEY_CARD_HOLDER_NAME => $additionalData['cc_holder_name'] ?? '',
            ]
        ];
    }
}
