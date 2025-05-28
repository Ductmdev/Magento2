<?php

namespace Bap\GmoPayment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Bap\GmoPayment\Api\Data\TransactionDetailDataInterfaceFactory;
use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Api\Data\TransactionDetailDataInterface;

class SalesOrderSaveObserver implements ObserverInterface
{
    public function __construct(
        private TransactionDetailDataInterfaceFactory $transactionDetailFactory
    ) {}

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('order');

        if (!$order->getId()) {
            return;
        }

        $payment = $order->getPayment();
        if (is_null($payment) || $payment->getMethod() !== 'gmo_payment') {
            return;
        }

        $additionalInformation = $order->getPayment()->getAdditionalInformation();

        /** @var TransactionDetailDataInterface $transactionDetail */
        $transactionDetail = $this->transactionDetailFactory->create();
        $transactionDetail->getResource()->load($transactionDetail, $order->getId(), 'order_id');

        if (!$transactionDetail->getId()) {
            $transactionDetail->setOrderId($order->getId());

            if (!empty($additionalInformation[GmoConst::KEY_ACCESS_ID])) {
                $transactionDetail->setAccessId($additionalInformation[GmoConst::KEY_ACCESS_ID]);
            }
            if (!empty($additionalInformation[GmoConst::KEY_ACCESS_PASSWORD])) {
                $transactionDetail->setAccessPass($additionalInformation[GmoConst::KEY_ACCESS_PASSWORD]);
            }

            $transactionDetail->getResource()->save($transactionDetail);
        }
    }
}
