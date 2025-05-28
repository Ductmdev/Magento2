<?php

namespace Bap\Register\Controller\Account;

use Bap\Register\Api\Data\TempCustomerInterface;
use Bap\Register\Api\TempCustomerRepositoryInterface;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;

class VerifyPost implements HttpPostActionInterface
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

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
     * @var ManagerInterface
     */
    protected $messageManager;

    protected $tempCustomerRepository;

    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        RedirectFactory $resultRedirectFactory,
        Session $customerSession,
        Registration $registration,
        RequestInterface $request,
        UrlFactory $urlFactory,
        ManagerInterface $messageManager,
        TempCustomerRepositoryInterface $tempCustomerRepository,
        JsonFactory $resultJsonFactory
    )
    {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->session = $customerSession;
        $this->registration = $registration;
        $this->request = $request;
        $this->urlModel = $urlFactory->create();
        $this->_redirect = $context->getRedirect();
        $this->messageManager = $messageManager;
        $this->tempCustomerRepository = $tempCustomerRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultJson = $this->resultJsonFactory->create();

        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        try {
            $verifyToken = (string)$this->request->getParam('verify_token');
            $verifyCode = (string)$this->request->getParam('verify_code');

            if (empty($verifyToken)) {
                $this->messageManager->addErrorMessage(__('Something went wrong'));
            } else {
                /** @var TempCustomerInterface $tempCustomer */
                $tempCustomer = $this->tempCustomerRepository->getByToken($verifyToken);
                if ($tempCustomer->getStatus() != TempCustomerInterface::STATUS_PENDING)
                {
                    $this->messageManager->addErrorMessage(__('Something went wrong'));
                } elseif ($tempCustomer->getDisabled()) {
                    $response = [
                        'valid' => false,
                        'message' => __('The email address you entered will not be available until 12 hours have passed because you have entered the verification number incorrectly more than the specified number of times.')
                    ];
                    return $resultJson->setData($response);
                } elseif (strtotime($tempCustomer->getExpiredAt()) < time()) {
                    $response = [
                        'valid' => false,
                        'message' => __('The verification number has expired. Please reissue the verification number from the previous screen.')
                    ];
                    return $resultJson->setData($response);
                } else {
                    if ($tempCustomer->getVerifyCode() == $verifyCode) {
                        $tempCustomer->setStatus(TempCustomerInterface::STATUS_VERIFIED);
                        $this->tempCustomerRepository->save($tempCustomer);

                        $response['valid'] = true;
                        $response['redirect'] = $this->urlModel->getUrl('customer/account/create', ['_query' => ['token' => $verifyToken]]);

                        return $resultJson->setData($response);
                    } else {
                        $fail_attempt = (int)$tempCustomer->getFailAttempt() + 1;
                        $tempCustomer->setFailAttempt($fail_attempt);

                        if ($fail_attempt >= 6) {
                            $disable_until = date('Y-m-d H:i:s', strtotime('+12 hours'));
                            $tempCustomer->setDisableUntil($disable_until);
                            $tempCustomer->setDisabled(true);

                            $response = [
                                'valid' => false,
                                'message' => __('The email address you entered will not be available until 12 hours have passed because you have entered the verification number incorrectly more than the specified number of times.')
                            ];
                        } else {
                            $response = [
                                'valid' => false,
                                'message' => __('The verification number you entered is incorrect. Please check the verification number and re-enter it.')
                            ];
                        }

                        $this->tempCustomerRepository->save($tempCustomer);
                        return $resultJson->setData($response);
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong'));
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/account/verify', ['_query' => ['token' => $verifyToken]]);
        return $resultRedirect;
    }
}
