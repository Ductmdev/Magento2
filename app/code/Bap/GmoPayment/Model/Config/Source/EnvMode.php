<?php

namespace Bap\GmoPayment\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class EnvMode implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'test',
                'label' => __('Test')
            ],
            [
                'value' => 'prod',
                'label' => __('Production')
            ],
        ];
    }
}
