<?php

namespace Bap\GmoPayment\Gateway\Http\Client;

use Bap\GmoPayment\Enum\GmoConst;
use Bap\GmoPayment\Gateway\Exception\GmoPaymentException;

class TransactionRegister extends AbstractTransaction
{
    protected function process(array $inputs)
    {
        $entryTran = $this->paymentService->entryTran($inputs);
        $accessId = $entryTran['data'][GmoConst::KEY_ACCESS_ID] ?? null;
        $accessPass = $entryTran['data'][GmoConst::KEY_ACCESS_PASSWORD] ?? null;

        if (empty($accessId) || empty($accessPass)) {
            throw new GmoPaymentException(__('Cound not perform EntryTran'));
        }

        $execTran = $this->paymentService->execTran($accessId, $accessPass, $inputs);

        if (empty($execTran['data'])) {
            throw new GmoPaymentException(__('Cound not perform ExecTran'));
        }

        return [
            GmoConst::KEY_ACCESS_ID => $accessId,
            GmoConst::KEY_ACCESS_PASSWORD => $accessPass,
            GmoConst::KEY_EXEC_TRAN_DATA => $execTran['data'],
        ];
    }
}
