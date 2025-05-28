<?php

namespace Bap\Gmopayment\Gateway\Request;

use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Gateway\Config\Config;
use Magento\Payment\Gateway\Request\BuilderInterface;

class ThreeDSecureDataBuilder implements BuilderInterface
{
    public const THREE_D_SECURE = '3dsecure';
    public const TD_FLAG_VALUE = '2';
    public const TD_S2_TYPE_VALUE = '2';
    public const TD_REQUIRED_VALUE = '1';

    public function __construct(protected Config $config) {}

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $result = [
            self::THREE_D_SECURE => [
                'enabled' => false,
            ]
        ];
        if ($this->config->isVerify3DSecure()) {
            $result[self::THREE_D_SECURE]['enabled'] = true;
            $result[self::THREE_D_SECURE][GmoConst::KEY_TD_FLAG] = self::TD_FLAG_VALUE;
            $result[self::THREE_D_SECURE][GmoConst::KEY_TD_S2_TYPE] = self::TD_S2_TYPE_VALUE;
            $result[self::THREE_D_SECURE][GmoConst::KEY_TD_REQUIRED] = self::TD_REQUIRED_VALUE;
            $result[self::THREE_D_SECURE][GmoConst::KEY_TD_TENANT_NAME] = $this->config->getTdTenantName();
            $result[self::THREE_D_SECURE][GmoConst::KEY_MERCHANT_RETURN_URL] = $this->config->getMerchantReturnUrl();
            $result[self::THREE_D_SECURE][GmoConst::KEY_CALLBACK_TYPE] = GmoConst::DEFAULT_CALLBACK_TYPE;
        }

        return $result;
    }
}
