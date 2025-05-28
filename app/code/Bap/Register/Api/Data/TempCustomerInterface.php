<?php

namespace Bap\Register\Api\Data;

interface TempCustomerInterface
{
    const ID = 'id';
    const EMAIL = 'email';
    const CODE = 'code';
    const TOKEN = 'token';
    const EXPIRED_AT = 'expired_at';
    const STATUS = 'status';
    const FAILED_ATTEMPTS = 'failed_attempts';
    const IS_DISABLED = 'is_disabled';
    const DISABLED_UNTIL = 'disabled_until';
    const WEBSITE_ID = 'website_id';
    const STORE_ID = 'store_id';
    const TEMP_DATA = 'temp_data';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS_PENDING = 1;
    const STATUS_VERIFIED = 2;
    const STATUS_ACCOUNT_CREATED = 3;
    const STATUS_TIMEOUT = 4;

    public function getId();

    public function setId($id);

    public function getToken();

    public function setToken($token);

    public function getEmail();

    public function setEmail($email);

    public function getVerifyCode();

    public function setVerifyCode($verifyCode);

    public function getExpiredAt();

    public function setExpiredAt($expiredAt);

    public function getStatus();

    public function setStatus($status);

    public function getFailAttempt();

    public function setFailAttempt($fail_attempt);

    public function getDisabled();

    public function setDisabled($disabled);

    public function getDisableUntil();

    public function setDisableUntil($disable_until);

    public function getWebsiteId();

    public function setWebsiteId($websiteId);

    public function getStoreId();

    public function setStoreId($storeId);

    public function getTempData();

    public function setTempData($tempData);
}
