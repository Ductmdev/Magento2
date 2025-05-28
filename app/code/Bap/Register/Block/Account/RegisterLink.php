<?php

namespace Bap\Register\Block\Account;

class RegisterLink extends \Magento\Customer\Block\Account\RegisterLink
{
    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Registration
     */
    protected $_registration;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $registration, $customerUrl);
        $this->httpContext = $httpContext;
        $this->_registration = $registration;
        $this->_customerUrl = $customerUrl;
    }

    public function getHref(): string
    {
        return $this->getUrl('customer/account/register');
    }
}
