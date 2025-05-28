<?php

namespace Bap\GmoPayment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class ResponseValidator extends AbstractValidator
{
    public function validate(array $validationSubject)
    {
        $isValid = true;
        $errorMessages = [];

        return $this->createResult($isValid, $errorMessages);
    }
}
