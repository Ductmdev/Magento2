<?php

namespace Bap\GmoPayment\Gateway\Http\Client;

use Bap\GmoPayment\Api\GmoPaymentInterface;
use Bap\GmoPayment\Gateway\Exception\GmoPaymentException;
use Exception;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractTransaction implements ClientInterface
{
    public function __construct(protected LoggerInterface $logger, protected GmoPaymentInterface $paymentService) {}

    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $response['object'] = [];

        try {
            $response['object'] = $this->process($data);
        } catch (Exception $e) {
            $logMessage = $message = $e->getMessage() ?: 'Sorry, but something went wrong';
            if ($e instanceof GmoPaymentException && $e->getData()) {
                $logMessage .= ' | Data: ' . json_encode($e->getData());
            }

            $this->logger->critical('[GmoPayment] Error: ' . $logMessage);
            throw new ClientException(__($message));
        }

        return $response;
    }

    abstract protected function process(array $data);
}
