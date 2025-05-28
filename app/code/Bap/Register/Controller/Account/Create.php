<?php

namespace Bap\Register\Controller\Account;

use Bap\Register\Api\Data\TempCustomerInterface;
use Bap\Register\Api\TempCustomerRepositoryInterface;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Create implements HttpGetActionInterface
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
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    protected $tempCustomerRepository;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        Registration $registration,
        ForwardFactory $resultForwardFactory,
        RequestInterface $request,
        TempCustomerRepositoryInterface $tempCustomerRepository
    )
    {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->registration = $registration;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->tempCustomerRepository = $tempCustomerRepository;
        $this->request = $request;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }

        $verifyToken = (string)$this->request->getParam('token');

        if (empty($verifyToken)) {
            return $this->forwardToNoRoute();
        }

        try {
            /** @var TempCustomerInterface $tempCustomer */
            $tempCustomer = $this->tempCustomerRepository->getByToken($verifyToken);
            if ($tempCustomer->getStatus() != TempCustomerInterface::STATUS_VERIFIED)
            {
                return $this->forwardToNoRoute();
            }
        } catch (NoSuchEntityException $e) {
            return $this->forwardToNoRoute();
        }

        $tempData = (array)json_decode($tempCustomer->getTempData());

        if (empty($tempData))
        {
            $this->session->setCustomerFormData(
                [
                    'email' => $tempCustomer->getEmail(),
                    'token' => $verifyToken
                ]
            );
        } else {
            $this->session->setCustomerFormData($tempData);
        }

        return $this->resultPageFactory->create();
    }

    private function forwardToNoRoute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('defaultNoRoute');
        return $resultForward;
    }
}
