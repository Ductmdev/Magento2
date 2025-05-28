<?php

namespace Bap\GmoPayment\Controller\Threedsecure;

use Bap\GmoPayment\Api\AuthenticateTranInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Bap\GmoPayment\Api\Data\TransactionDetailDataInterfaceFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Callback extends Action implements HttpGetActionInterface
{
    public const ACCESS_ID_PARAM = 'AccessID';

    public function __construct(
        Context $context,
        private TransactionDetailDataInterfaceFactory $transactionDetailFactory,
        private AuthenticateTranInterface $authenticateTran,
        private LoggerInterface $logger,
        private OrderRepositoryInterface $orderRepository,
        private CheckoutSession $checkoutSession
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $accessId = $this->getRequest()->getParam(self::ACCESS_ID_PARAM);

        /** @var TransactionDetailDataInterface $transactionDetail */
        $transactionDetail = $this->transactionDetailFactory->create();
        $transactionDetail->getResource()->load($transactionDetail, $accessId, 'access_id');

        if (!$transactionDetail->getId()) {
            $this->messageManager->addErrorMessage(__('GMO Payment Gateway: Failed to authenticate'));
            return $this->_redirect('checkout/cart');
        }

        $order = $this->orderRepository->get($transactionDetail->getOrderId());
        try {
            $this->authenticateTran->authenticate($accessId, $transactionDetail->getAccessPass());

            return $this->_redirect('checkout/onepage/success');
        } catch (\Exception $exception) {
            $this->logger->error('[GMO Payment Gateway] 3D Secure Callback Error: ' . $exception->getMessage());
            $this->messageManager->addErrorMessage(__('GMO Payment Gateway: Failed to authenticate'));

            $order->cancel();
            $this->orderRepository->save($order);

            return $this->_redirect('checkout/cart');
        }
    }
}
