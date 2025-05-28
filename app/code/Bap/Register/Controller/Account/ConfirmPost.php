<?php

namespace Bap\Register\Controller\Account;

use Bap\Register\Api\Data\TempCustomerInterface;
use Bap\Register\Api\TempCustomerRepositoryInterface;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;

class ConfirmPost implements HttpPostActionInterface
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
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;

    /**
     * @var RedirectInterface
     */
    protected $_redirect;

    protected $tempCustomerRepository;

    public function __construct(
        Context $context,
        Session $customerSession,
        RedirectFactory $resultRedirectFactory,
        RequestInterface $request,
        Registration $registration,
        ManagerInterface $messageManager,
        UrlFactory $urlFactory,
        TempCustomerRepositoryInterface $tempCustomerRepository,
        Validator $formKeyValidator = null
    )
    {
        $this->session = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->request = $request;
        $this->registration = $registration;
        $this->_redirect = $context->getRedirect();
        $this->messageManager = $messageManager;
        $this->urlModel = $urlFactory->create();
        $this->tempCustomerRepository = $tempCustomerRepository;
        $this->formKeyValidator = $formKeyValidator ?: ObjectManager::getInstance()->get(Validator::class);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if (!$this->request->isPost()
            || !$this->formKeyValidator->validate($this->request)
        ) {
            $url = $this->urlModel->getUrl('*/*/register', ['_secure' => true]);
            return $this->resultRedirectFactory->create()
                ->setUrl($this->_redirect->error($url));
        }

        try {
            $token = (string)$this->request->getParam('token');

            if (empty($token)) {
                $this->messageManager->addErrorMessage(__('Something went wrong'));
            } else {
                /** @var TempCustomerInterface $tempCustomer */
                $tempCustomer = $this->tempCustomerRepository->getByToken($token);
                if ($tempCustomer->getStatus() != TempCustomerInterface::STATUS_VERIFIED)
                {
                    $this->messageManager->addErrorMessage(__('Something went wrong'));
                } else {
                    $password = $this->request->getParam('password');
                    $confirmation = $this->request->getParam('password_confirmation');
                    $this->checkPasswordConfirmation($password, $confirmation);

                    $postData = $this->request->getPostValue();
                    $postData['is_subscribed'] = $this->request->getParam('is_subscribed', 0);
                    $tempCustomer->setTempData(json_encode($postData));
                    $this->tempCustomerRepository->save($tempCustomer);

                    $resultRedirect->setPath('customer/account/confirminformation', ['_query' => ['token' => $this->request->getParam('token')]]);
                    return $resultRedirect;
                }
            }
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong'));
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->session->setCustomerFormData($this->request->getPostValue());
        $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true, '_query' => ['token' => $this->request->getParam('token')]]);
        return $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
    }

    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }
}
