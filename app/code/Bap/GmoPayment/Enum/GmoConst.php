<?php

namespace Bap\GmoPayment\Enum;

final class GmoConst
{
    public const KEY_AMOUNT = 'amount';
    public const KEY_ORDER_ID = 'orderID';
    public const KEY_CARD_NUMBER = 'cardNo';
    public const KEY_EXPIRE = 'expire';
    public const KEY_SHOP_ID = 'shopID';
    public const KEY_CARD_HOLDER_NAME = 'holderName';
    public const KEY_SHOP_PASSWORD = 'shopPass';
    public const KEY_SITE_ID = 'siteID';
    public const KEY_SITE_PASSWORD = 'sitePass';
    public const KEY_TD_FLAG = 'tdFlag';
    public const KEY_TD_S2_TYPE = 'tds2Type';
    public const KEY_TD_REQUIRED = 'tdRequired';
    public const KEY_TD_TENANT_NAME = 'tdTenantName';
    public const KEY_JOB_CD = 'jobCd';
    public const KEY_ACCESS_ID = 'accessID';
    public const KEY_ACCESS_PASSWORD = 'accessPass';
    public const KEY_CHARGE_METHOD = 'method';
    public const KEY_MERCHANT_RETURN_URL = 'retUrl';
    public const KEY_CALLBACK_TYPE = 'callbackType';
    public const KEY_3DS_REDIRECT_URL = 'redirectUrl';
    public const KEY_EXEC_TRAN_DATA = 'execTranData';
    public const KEY_TRAN_ID = 'tranID';

    public const DEFAULT_CHARGE_METHOD = '1';
    public const AUTH_JOB_CD = 'AUTH';
    public const CAPTURE_JOB_CD = 'SALES';
    public const INSTANT_CAPTURE_JOB_CD = 'CAPTURE';
    public const DEFAULT_CALLBACK_TYPE = '3';
    public const GET_THREE_D_SECURE_URL = '/rest/V1/gmo/threedsecure-url/:orderId';
}
