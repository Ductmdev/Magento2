<?php

namespace Bap\GmoPayment\Gateway\Response;

use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Gateway\Exception\GmoPaymentException;
use Bap\GmoPayment\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use InvalidArgumentException;

class PaymentDetailsHandler implements HandlerInterface
{
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($response['object'])) {
            throw new InvalidArgumentException(__('Response object does not exist'));
        }

        $transaction = $response['object'];
        $accessId = $transaction[GmoConst::KEY_ACCESS_ID] ?? null;
        $accessPass = $transaction[GmoConst::KEY_ACCESS_PASSWORD] ?? null;
        $transactionData = $transaction[GmoConst::KEY_EXEC_TRAN_DATA] ?? [];

        if (empty($accessId) || empty($accessPass) || empty($transactionData)) {
            throw new GmoPaymentException(__('Bad Request'));
        }

        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var OrderPaymentInterface $payment */
        $payment = $paymentDO->getPayment();

        $payment->setAdditionalInformation(GmoConst::KEY_ACCESS_ID, $accessId);
        $payment->setAdditionalInformation(GmoConst::KEY_ACCESS_PASSWORD, $accessPass);

        if (!empty($transactionData['tranID'])) {
            $payment->setCcTransId($transactionData['tranID']);
            $payment->setLastTransId($transactionData['tranID']);
        }

        if (!empty($transactionData[GmoConst::KEY_3DS_REDIRECT_URL])) {
            $payment->setAdditionalInformation(GmoConst::KEY_3DS_REDIRECT_URL, $transactionData[GmoConst::KEY_3DS_REDIRECT_URL]);
        }

        // Remove card details from additional data.
        foreach (DataAssignObserver::$additionalInformationList as $information) {
            $payment->unsAdditionalInformation($information);
        }
    }
}
