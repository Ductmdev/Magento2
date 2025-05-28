<?php

namespace Bap\GmoPayment\Block;

use Magento\Payment\Block\ConfigurableInfo;
use Magento\Framework\Phrase;

class Info extends ConfigurableInfo
{
    protected function getLabel($field)
    {
        return __($field);
    }
}
