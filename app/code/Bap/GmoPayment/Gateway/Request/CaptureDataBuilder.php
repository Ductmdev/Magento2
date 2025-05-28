<?php

namespace Bap\GmoPayment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Bap\GmoPayment\Api\Data\TransactionDetailDataInterface;
use Bap\GmoPayment\Api\Data\TransactionDetailDataInterfaceFactory;
use Bap\GmoPayment\Enum\GmoConst;
use Magento\Framework\Exception\LocalizedException;

class CaptureDataBuilder implements BuilderInterface
{
    public function __construct(
        private TransactionDetailDataInterfaceFactory $transactionDetailFactory
    ) {}

    public function build(array $buildSubject): array
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        /** @var TransactionDetailDataInterface $transactionDetail */
        $transactionDetail = $this->transactionDetailFactory->create();
        $transactionDetail->getResource()->load($transactionDetail, $order->getId(), 'order_id');

        if (!$transactionDetail->getId()) {
            throw new LocalizedException(__('No authorization transaction to proceed capture.'));
        }

        $accessId = $transactionDetail->getAccessId();
        $accessPass = $transactionDetail->getAccessPass();
        if (empty($accessId) || empty($accessPass)) {
            throw new LocalizedException(__('Missing arguments.'));
        }

        return [
            PaymentDataBuilder::PAYMENT_DATA => [
                GmoConst::KEY_ACCESS_ID => $accessId,
                GmoConst::KEY_ACCESS_PASSWORD => $accessPass,
                GmoConst::KEY_AMOUNT => SubjectReader::readAmount($buildSubject),
            ]
        ];
    }
}
