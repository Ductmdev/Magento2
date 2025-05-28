<?php

namespace Bap\Register\Controller\Account;

use Bap\Register\Api\AccountManagementInterface;
use Bap\Register\Api\Data\TempCustomerInterface;
use Bap\Register\Api\TempCustomerRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlFactory;
use Magento\Store\Model\StoreManagerInterface;

class RegisterPost implements HttpPostActionInterface
{
    /**
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;

    /**
     * @var RedirectInterface
     */
    protected $_redirect;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Bap\Register\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    protected $_tempCustomerModel;

    /**
     * @var Random
     */
    private $mathRandom;

    protected $resultJsonFactory;

    protected $tempCustomerRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        Context                         $context,
        Session                         $customerSession,
        RedirectFactory                 $resultRedirectFactory,
        ManagerInterface                $messageManager,
        CustomerRepositoryInterface     $customerRepository,
        RequestInterface                $request,
        Registration                    $registration,
        UrlFactory                      $urlFactory,
        AccountManagementInterface      $accountManagement,
        TempCustomerInterface           $tempCustomerInterface,
        StoreManagerInterface           $storeManager,
        Random                          $mathRandom,
        JsonFactory                     $resultJsonFactory,
        TempCustomerRepositoryInterface $tempCustomerRepository,
    ) {
        $this->session = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->customerRepository = $customerRepository;
        $this->request = $request;
        $this->registration = $registration;
        $this->urlModel = $urlFactory->create();
        $this->_redirect = $context->getRedirect();
        $this->accountManagement = $accountManagement;
        $this->_tempCustomerModel = $tempCustomerInterface;
        $this->storeManager = $storeManager;
        $this->mathRandom = $mathRandom;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->tempCustomerRepository = $tempCustomerRepository;
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultJson = $this->resultJsonFactory->create();

        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        try {
            $email = $this->request->getParam('email');

            if (empty($email)) {
                $this->messageManager->addErrorMessage(__('Please enter at least one email address.'));
            }
            elseif (!$this->checkEmailExists($email)) {
                $temp_customers = $this->tempCustomerRepository->getByEmail($email);

                foreach ($temp_customers as $customer) {
                    if ($customer->getDisabled() && strtotime($customer->getDisableUntil()) > time()) {
                        $response = [
                            'code' => 2,
                            'message' => __('The email address you entered is currently unavailable because it has exceeded the specified number of authentication attempts.')
                        ];
                        return $resultJson->setData($response);
                    }
                }

                $verify_token = $this->mathRandom->getRandomString(64);
                $temp_data = $this->request->getPostValue();
                $temp_data['token'] = $verify_token;
                $this->_tempCustomerModel->setEmail($email);
                $this->_tempCustomerModel->setTempData(json_encode($temp_data));
                $this->_tempCustomerModel->setToken($verify_token);
                $this->accountManagement->createTempCustomer($this->_tempCustomerModel);

                $response['code'] = 1;
                $response['redirect'] = $this->urlModel->getUrl('customer/account/verify', ['_query' => ['token' => $verify_token]]);

                return $resultJson->setData($response);
            } else {
                $response = [
                    'code' => 0,
                    'message' => __('Customer Already Exists')
                ];
                return $resultJson->setData($response);
            }
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/account/register');
        return $resultRedirect;
    }

    /**
     * @throws LocalizedException
     */
    private function checkEmailExists($email): bool
    {
        try {
            $this->customerRepository->get($email, $this->storeManager->getStore()->getWebsiteId());
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
