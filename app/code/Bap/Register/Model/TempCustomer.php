<?php

namespace Bap\Register\Model;

use Bap\Register\Api\Data\TempCustomerInterface;
use Magento\Framework\DataObject\IdentityInterface;

class TempCustomer extends \Magento\Framework\Model\AbstractModel implements TempCustomerInterface, IdentityInterface
{
    const CACHE_TAG = 'customer_verification';

    protected function _construct()
    {
        $this->_init(\Bap\Register\Model\ResourceModel\TempCustomer::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getToken()
    {
        return $this->getData(self::TOKEN);
    }

    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function getVerifyCode()
    {
        return $this->getData(self::CODE);
    }

    public function setVerifyCode($verifyCode)
    {
        return $this->setData(self::CODE, $verifyCode);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }

    public function setExpiredAt($expiredAt)
    {
        return $this->setData(self::EXPIRED_AT, $expiredAt);
    }

    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    public function getTempData()
    {
        return $this->getData(self::TEMP_DATA);
    }

    public function setTempData($tempData)
    {
        return $this->setData(self::TEMP_DATA, $tempData);
    }

    public function getFailAttempt()
    {
        return $this->getData(self::FAILED_ATTEMPTS);
    }

    public function setFailAttempt($fail_attempt)
    {
        return $this->setData(self::FAILED_ATTEMPTS, $fail_attempt);
    }

    public function getDisabled()
    {
        return $this->getData(self::IS_DISABLED);
    }

    public function setDisabled($disabled)
    {
        return $this->setData(self::IS_DISABLED, $disabled);
    }

    public function getDisableUntil()
    {

        return $this->getData(self::DISABLED_UNTIL);
    }

    public function setDisableUntil($disable_until)
    {
        return $this->setData(self::DISABLED_UNTIL, $disable_until);
    }
}
