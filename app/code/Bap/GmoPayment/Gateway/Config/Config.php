<?php

namespace Bap\GmoPayment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Config\Config as BaseConfig;
use Bap\GmoPayment\Model\StoreConfig;
use Magento\Framework\UrlInterface;

class Config extends BaseConfig
{
    public const KEY_API_BASE_URI = 'api_base_uri';
    public const KEY_SHOP_ID = 'shop_id';
    public const KEY_SHOP_PASSWORD = 'shop_password';
    public const KEY_SITE_ID = 'site_id';
    public const KEY_SITE_PASSWORD = 'site_password';
    public const KEY_VERIFY_3DSECURE = 'verify_3dsecure';
    public const KEY_TD_TENANT_NAME = 'td_tenant_name';
    public const KEY_PAYMENT_ACTION = 'payment_action';

    public const DEFAULT_MERCHANT_RETURN_URL = 'gmopayment/threedsecure/callback';

    public function __construct(
        private StoreConfig $storeConfig,
        private UrlInterface $urlInterface,
        ScopeConfigInterface $scopeConfig,
        $methodCode = null,
        string $pathPattern = self::DEFAULT_PATH_PATTERN,
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
    }

    /**
     * Get shop id
     *
     * @param int|null $storeId
     * @return string|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getShopId(int $storeId = null)
    {
        return $this->getValue(
            self::KEY_SHOP_ID,
            $storeId ?? $this->storeConfig->getStoreId()
        );
    }

    /**
     * Get shop password
     *
     * @param int|null $storeId
     * @return string|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getShopPassword(int $storeId = null)
    {
        return $this->getValue(
            self::KEY_SHOP_PASSWORD,
            $storeId ?? $this->storeConfig->getStoreId()
        );
    }

    /**
     * Get site id
     *
     * @param int|null $storeId
     * @return string|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getSiteId(int $storeId = null)
    {
        return $this->getValue(
            self::KEY_SITE_ID,
            $storeId ?? $this->storeConfig->getStoreId()
        );
    }

    /**
     * Get site password
     *
     * @param int|null $storeId
     * @return string|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getSitePassword(int $storeId = null)
    {
        return $this->getValue(
            self::KEY_SITE_PASSWORD,
            $storeId ?? $this->storeConfig->getStoreId()
        );
    }

    /**
     * Get Api Base Uri
     *
     * @param int|null $storeId
     * @return string|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getApiBaseUri(int $storeId = null)
    {
        return $this->getValue(
            self::KEY_API_BASE_URI,
            $storeId ?? $this->storeConfig->getStoreId()
        );
    }

    /**
     * Check if 3d secure verification enabled
     *
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function isVerify3DSecure(): bool
    {
        return (bool) $this->getValue(
            self::KEY_VERIFY_3DSECURE,
            $this->storeConfig->getStoreId()
        );
    }

    /**
     * Get td tenant name
     *
     * @param int|null $storeId
     * @return string|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getTdTenantName(int $storeId = null)
    {
        return $this->getValue(
            self::KEY_TD_TENANT_NAME,
            $storeId ?? $this->storeConfig->getStoreId()
        );
    }

    /**
     * Get merchant return url
     *
     * @return string
     */
    public function getMerchantReturnUrl()
    {
        return $this->urlInterface->getUrl(self::DEFAULT_MERCHANT_RETURN_URL);
    }

    /**
     * Get payment action
     *
     * @param int|null $storeId
     * @return string|null
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getPaymentAction(int $storeId = null)
    {
        return $this->getValue(
            self::KEY_PAYMENT_ACTION,
            $payment_action ?? $this->storeConfig->getStoreId()
        );
    }
}
