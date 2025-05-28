<?php

namespace Bap\GmoPayment\Model\Ui;

use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Gateway\Config\Config;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'gmo_payment';

    public function __construct(private Config $config) {}

    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'title' => 'GMO Payment',
                    'three_d_secure' => [
                        'enabled' => $this->config->isVerify3DSecure(),
                        'three_d_secure_url' => GmoConst::GET_THREE_D_SECURE_URL,
                    ]
                ]
            ]
        ];
    }
}
