<?php

namespace Bap\Register\Model;

use Bap\Register\Api\Data\TempCustomerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class EmailNotification implements EmailNotificationInterface
{
    public const XML_PATH_VERIFICATION_EMAIL_TEMPLATE = 'customer/verify_account/email_template';

    public const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';

    public const TEMPLATE_TYPES = [
        self::NEW_ACCOUNT_EMAIL_VERIFICATION => self::XML_PATH_VERIFICATION_EMAIL_TEMPLATE
    ];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Emulation
     */
    private $emulation;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        SenderResolverInterface $senderResolver = null,
        Emulation $emulation = null
    )
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->senderResolver = $senderResolver ?? ObjectManager::getInstance()->get(SenderResolverInterface::class);
        $this->emulation = $emulation ?? ObjectManager::getInstance()->get(Emulation::class);
    }

    /**
     * @throws LocalizedException
     */
    public function newVerification(
        TempCustomerInterface $tempCustomer,
        $type = self::NEW_ACCOUNT_EMAIL_VERIFICATION,
        $backUrl = '',
        $storeId = null,
        $sendEmailStoreId = null
    ): void
    {
        $types = self::TEMPLATE_TYPES;

        if (!isset($types[$type])) {
            throw new LocalizedException(
                __('The transactional account email type is incorrect. Verify and try again.')
            );
        }

        if ($storeId === null) {
            $storeId = $this->getWebsiteStoreId($tempCustomer, $sendEmailStoreId);
        }

        $store = $this->storeManager->getStore($tempCustomer->getStoreId());

        $tempData = (array)json_decode($tempCustomer->getTempData());
        $customerName = $tempData['lastname'] . ' ' . $tempData['firstname'];
        $verifyCode = $tempCustomer->getVerifyCode();

        $this->sendEmailTemplate(
            $tempCustomer,
            $types[$type],
            self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            ['customerName' => $customerName, 'verifyCode' => $verifyCode, 'back_url' => $backUrl, 'store' => $store],
            $storeId
        );
    }

    /**
     * @throws LocalizedException
     */
    private function getWebsiteStoreId($tempCustomer, $defaultStoreId = null): int
    {
        if ($tempCustomer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($tempCustomer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * @throws MailException
     * @throws LocalizedException
     */
    private function sendEmailTemplate(
        $customer,
        $template,
        $sender,
        $templateParams = [],
        $storeId = null,
        $email = null
    ): void
    {
        $templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
        if ($email === null) {
            $email = $customer->getEmail();
        }

        $from = $this->senderResolver->resolve(
            $this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId),
            $storeId
        );

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($from)
            ->addTo($email)
            ->getTransport();

        $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND);
        $transport->sendMessage();
        $this->emulation->stopEnvironmentEmulation();
    }
}
