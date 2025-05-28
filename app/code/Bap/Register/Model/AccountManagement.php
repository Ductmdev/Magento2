<?php

namespace Bap\Register\Model;

use Bap\Register\Api\AccountManagementInterface;
use Bap\Register\Api\Data\TempCustomerInterface;
use Bap\Register\Api\TempCustomerRepositoryInterface;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLogger;

class AccountManagement implements AccountManagementInterface
{
    public const NEW_ACCOUNT_EMAIL_VERIFICATION = 'verification';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigShare
     */
    private $configShare;

    /**
     * @var TempCustomerRepositoryInterface
     */
    private $tempCustomerRepository;

    /**
     * @var PsrLogger
     */
    protected $logger;

    /**
     * @var EmailNotificationInterface
     */
    private $emailNotification;

    /**
     * @var Random
     */
    private $mathRandom;

    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigShare $configShare,
        TempCustomerRepositoryInterface $tempCustomerRepository,
        PsrLogger $logger,
        Random $mathRandom,
    )
    {
        $this->storeManager = $storeManager;
        $this->configShare = $configShare;
        $this->tempCustomerRepository = $tempCustomerRepository;
        $this->logger = $logger;
        $this->mathRandom = $mathRandom;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function createTempCustomer(TempCustomerInterface $tempCustomer, $redirectUrl = '')
    {
        if (!$tempCustomer->getStoreId()) {
            if ($tempCustomer->getWebsiteId()) {
                $storeId = null;
                $website = $this->storeManager->getWebsite($tempCustomer->getWebsiteId());
                if ($website->getDefaultStore()) {
                    $storeId = $website->getDefaultStore()->getId();
                }
            } else {
                $this->storeManager->setCurrentStore(null);
                $storeId = $this->storeManager->getStore()->getId();
            }
            $tempCustomer->setStoreId($storeId);
        }

        if (!$tempCustomer->getWebsiteId()) {
            $websiteId = $this->storeManager->getStore($tempCustomer->getStoreId())->getWebsiteId();
            $tempCustomer->setWebsiteId($websiteId);
        }

        $this->validateCustomerStoreIdByWebsiteId($tempCustomer);

        $verify_code = $this->mathRandom->getRandomNumber(100000, 999999);
        $verify_expired_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $status = TempCustomerInterface::STATUS_PENDING;
        $tempCustomer->setVerifyCode($verify_code);
        $tempCustomer->setExpiredAt($verify_expired_at);
        $tempCustomer->setStatus($status);

        return $this->tempCustomerRepository->save($tempCustomer);
    }

    /**
     * @throws LocalizedException
     */
    public function validateCustomerStoreIdByWebsiteId(TempCustomerInterface $tempCustomer): bool
    {
        if (!$this->isCustomerInStore($tempCustomer->getWebsiteId(), $tempCustomer->getStoreId())) {
            throw new LocalizedException(__('The store view is not in the associated website.'));
        }

        return true;
    }

    /**
     * @throws LocalizedException
     */
    public function isCustomerInStore($customerWebsiteId, $storeId): bool
    {
        $ids = [];
        if ((bool)$this->configShare->isWebsiteScope()) {
            $ids = $this->storeManager->getWebsite($customerWebsiteId)->getStoreIds();
        } else {
            foreach ($this->storeManager->getStores() as $store) {
                $ids[] = $store->getId();
            }
        }

        return in_array($storeId, $ids);
    }

    public function sendEmailVerification(TempCustomerInterface $tempCustomer, $redirectUrl = '')
    {
        try {
            $templateType = self::NEW_ACCOUNT_EMAIL_VERIFICATION;
            $this->getEmailNotification()->newVerification($tempCustomer, $templateType, $redirectUrl, $tempCustomer->getStoreId());
        } catch (MailException $e) {
            $this->logger->critical($e);
        } catch (\UnexpectedValueException $e) {
            $this->logger->error($e);
        }
    }

    private function getEmailNotification()
    {
        if (!($this->emailNotification instanceof EmailNotificationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                EmailNotificationInterface::class
            );
        } else {
            return $this->emailNotification;
        }
    }
}
