<?php

namespace Bap\GmoPayment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @var array
     */
    public static array $additionalInformationList = [
        'cc_type',
        'cc_exp_year',
        'cc_exp_month',
        'cc_number',
        'cc_holder_name',
    ];

    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (empty($additionalData)) return;

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach (self::$additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
