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

class ConfirmInformation implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

    protected $tempCustomerRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RequestInterface $request,
        ForwardFactory $resultForwardFactory,
        Session $customerSession,
        Registration $registration,
        TempCustomerRepositoryInterface $tempCustomerRepository
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->session = $customerSession;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->registration = $registration;
        $this->tempCustomerRepository = $tempCustomerRepository;
    }

    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed())
        {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
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

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getLayout()->getBlock('customer_confirm_info')
            ->setData(['temp_data' => $tempData]);
        $resultPage->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $resultPage->setHeader('Pragma', 'no-cache', true);

        return $resultPage;
    }

    private function forwardToNoRoute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('defaultNoRoute');
        return $resultForward;
    }
}
