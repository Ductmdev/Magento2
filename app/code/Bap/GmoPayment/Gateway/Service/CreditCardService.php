<?php

namespace Bap\GmoPayment\Gateway\Service;

use Bap\GmoPayment\Api\AuthenticateTranInterface;
use Bap\GmoPayment\Api\CapturePaymentInterface;
use Bap\GmoPayment\Api\GmoPaymentInterface;
use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Gateway\Request\GmoPaymentConfigDataBuilder;
use Bap\GmoPayment\Gateway\Request\PaymentDataBuilder;
use Bap\Gmopayment\Gateway\Request\ThreeDSecureDataBuilder;

class CreditCardService implements GmoPaymentInterface, AuthenticateTranInterface, CapturePaymentInterface
{
    public function __construct(private Client $client) {}

    public function entryTran(array $arguments)
    {
        $gmoConfig = $arguments[GmoPaymentConfigDataBuilder::GMO_CONFIG] ?? [];
        $paymentInfo = $arguments[PaymentDataBuilder::PAYMENT_DATA] ?? [];
        $threeDSecure = $arguments[ThreeDSecureDataBuilder::THREE_D_SECURE] ?? [];

        $inputs = [
            GmoConst::KEY_SHOP_ID => $gmoConfig[GmoConst::KEY_SHOP_ID] ?? '',
            GmoConst::KEY_SHOP_PASSWORD => $gmoConfig[GmoConst::KEY_SHOP_PASSWORD] ?? '',
            GmoConst::KEY_ORDER_ID => $paymentInfo[GmoConst::KEY_ORDER_ID] ?? '',
            GmoConst::KEY_AMOUNT => $paymentInfo[GmoConst::KEY_AMOUNT] ?? '0',
            GmoConst::KEY_JOB_CD => $gmoConfig[GmoConst::KEY_JOB_CD] ?? GmoConst::AUTH_JOB_CD,
        ];

        if (!empty($threeDSecure['enabled'])) {
            $inputs[GmoConst::KEY_TD_FLAG] = $threeDSecure[GmoConst::KEY_TD_FLAG] ?? '';
            $inputs[GmoConst::KEY_TD_S2_TYPE] = $threeDSecure[GmoConst::KEY_TD_S2_TYPE] ?? '';
            $inputs[GmoConst::KEY_TD_REQUIRED] = $threeDSecure[GmoConst::KEY_TD_REQUIRED] ?? '';
            $inputs[GmoConst::KEY_TD_TENANT_NAME] = $threeDSecure[GmoConst::KEY_TD_TENANT_NAME] ?? '';
        }

        return $this->client->sendPostRequest('payment/EntryTran.json', $inputs);
    }

    public function execTran(string $accessId, string $accessPass, array $arguments)
    {
        $paymentInfo = $arguments[PaymentDataBuilder::PAYMENT_DATA] ?? [];
        $threeDSecure = $arguments[ThreeDSecureDataBuilder::THREE_D_SECURE] ?? [];

        $inputs = [
            GmoConst::KEY_ACCESS_ID => $accessId,
            GmoConst::KEY_ACCESS_PASSWORD => $accessPass,
            GmoConst::KEY_ORDER_ID => $paymentInfo[GmoConst::KEY_ORDER_ID] ?? '',
            GmoConst::KEY_CARD_NUMBER => $paymentInfo[GmoConst::KEY_CARD_NUMBER] ?? '',
            GmoConst::KEY_EXPIRE => $paymentInfo[GmoConst::KEY_EXPIRE] ?? '',
            GmoConst::KEY_CHARGE_METHOD => GmoConst::DEFAULT_CHARGE_METHOD,
            GmoConst::KEY_CARD_HOLDER_NAME => $paymentInfo[GmoConst::KEY_CARD_HOLDER_NAME] ?? '',
        ];

        if (!empty($threeDSecure['enabled'])) {
            $inputs[GmoConst::KEY_MERCHANT_RETURN_URL] = $threeDSecure[GmoConst::KEY_MERCHANT_RETURN_URL] ?? '';
            $inputs[GmoConst::KEY_CALLBACK_TYPE] = $threeDSecure[GmoConst::KEY_CALLBACK_TYPE] ?? '';
        }

        return $this->client->sendPostRequest('/payment/ExecTran.json', $inputs);
    }

    public function authenticate(string $accessId, string $accessPass)
    {
        return $this->client->sendPostRequest('/payment/SecureTran2.json', [
            GmoConst::KEY_ACCESS_ID => $accessId,
            GmoConst::KEY_ACCESS_PASSWORD => $accessPass
        ]);
    }

    public function capture(array $arguments)
    {
        $gmoConfig = $arguments[GmoPaymentConfigDataBuilder::GMO_CONFIG] ?? [];
        $paymentData = $arguments[PaymentDataBuilder::PAYMENT_DATA] ?? [];

        return $this->client->sendPostRequest('payment/AlterTran.json', [
            GmoConst::KEY_SHOP_ID => $gmoConfig[GmoConst::KEY_SHOP_ID] ?? '',
            GmoConst::KEY_SHOP_PASSWORD => $gmoConfig[GmoConst::KEY_SHOP_PASSWORD] ?? '',
            GmoConst::KEY_ACCESS_ID => $paymentData[GmoConst::KEY_ACCESS_ID] ?? '',
            GmoConst::KEY_ACCESS_PASSWORD => $paymentData[GmoConst::KEY_ACCESS_PASSWORD] ?? '',
            GmoConst::KEY_JOB_CD => GmoConst::CAPTURE_JOB_CD,
            GmoConst::KEY_AMOUNT => strval($paymentData[GmoConst::KEY_AMOUNT] ?? 0),
        ]);
    }
}
