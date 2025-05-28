<?php

namespace Bap\Register\Controller\Account;

use Bap\Register\Api\Data\TempCustomerInterface;
use Bap\Register\Api\TempCustomerRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlFactory;

class Timeout implements HttpPostActionInterface
{
    protected $resultJsonFactory;

    protected $request;

    protected $tempCustomerRepository;

    protected $urlModel;

    public function __construct(
        JsonFactory $resultJsonFactory,
        RequestInterface $request,
        TempCustomerRepositoryInterface $tempCustomerRepository,
        UrlFactory $urlFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->tempCustomerRepository = $tempCustomerRepository;
        $this->urlModel = $urlFactory->create();
    }

    public function execute()
    {
        $token = (string)$this->request->getParam('token');
        /** @var TempCustomerInterface $tempCustomer */
        $tempCustomer = $this->tempCustomerRepository->getByToken($token);

        $tempCustomer->setStatus(TempCustomerInterface::STATUS_TIMEOUT);
        $this->tempCustomerRepository->save($tempCustomer);

        $resultJson = $this->resultJsonFactory->create();
        $response['valid'] = true;
        $response['redirect'] = $this->urlModel->getUrl('timeout');

        return $resultJson->setData($response);
    }
}
