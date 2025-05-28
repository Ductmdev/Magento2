<?php

namespace Bap\GmoPayment\Gateway\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class GmoPaymentException extends LocalizedException
{
    public function __construct(Phrase $phrase, protected mixed $data = null)
    {
        parent::__construct($phrase);
    }

    public function getData()
    {
        return $this->data;
    }
}
