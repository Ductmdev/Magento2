<?php

namespace Bap\GmoPayment\Model;

use Bap\GmoPayment\Api\ThreeDSecureUrlInterface;
use Bap\GmoPayment\Enum\GmoConst;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;

class ThreeDSecureUrl implements ThreeDSecureUrlInterface
{
    public function __construct(private OrderRepositoryInterface $orderRepository) {}

    public function getSecureUrl($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
            $payment = $order->getPayment();

            $secureUrl = $payment->getAdditionalInformation(GmoConst::KEY_3DS_REDIRECT_URL);

            if (!$secureUrl) {
                throw new WebapiException(__('GMO 3D Secure Url not found'), 0, WebapiException::HTTP_NOT_FOUND);
            }

            $payment->unsAdditionalInformation(GmoConst::KEY_3DS_REDIRECT_URL);

            return $secureUrl;
        } catch (NoSuchEntityException $exception) {
            throw new WebapiException(__('Order not found'), 0, WebapiException::HTTP_NOT_FOUND);
        }
    }
}
