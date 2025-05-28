<?php

namespace Bap\GmoPayment\Gateway\Request;

use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Gateway\Config\Config;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\MethodInterface;

class GmoPaymentConfigDataBuilder implements BuilderInterface
{
    public const GMO_CONFIG = 'gmo_config';

    public function __construct(private Config $config) {}

    public function build(array $buildSubject): array
    {
        return [
            self::GMO_CONFIG => [
                GmoConst::KEY_SHOP_ID => $this->config->getShopId(),
                GmoConst::KEY_SHOP_PASSWORD => $this->config->getShopPassword(),
                GmoConst::KEY_SITE_ID => $this->config->getSiteId(),
                GmoConst::KEY_SITE_PASSWORD => $this->config->getSitePassword(),
                GmoConst::KEY_JOB_CD => $this->config->getPaymentAction() === MethodInterface::ACTION_AUTHORIZE_CAPTURE
                    ? GmoConst::INSTANT_CAPTURE_JOB_CD
                    : GmoConst::AUTH_JOB_CD,
            ]
        ];
    }
}
