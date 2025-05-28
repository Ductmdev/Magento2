<?php

namespace Bap\GmoPayment\Gateway\Response;

use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Gateway\Exception\GmoPaymentException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use InvalidArgumentException;

class CaptureTransactionHandler implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response)
    {
        if (empty($response['object']['data'])) {
            throw new InvalidArgumentException(__('Response object does not exist'));
        }

        $transaction = $response['object']['data'];
        $transactionId = $transaction[GmoConst::KEY_TRAN_ID] ?? null;

        if (empty($transactionId)) {
            throw new GmoPaymentException(__('Bad Request'));
        }

        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $payment->setTransactionId($transactionId);
    }
}
