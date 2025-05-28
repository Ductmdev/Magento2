<?php

namespace Bap\Register\Model;

use Bap\Register\Api\Data\TempCustomerInterface;
use Bap\Register\Api\TempCustomerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Bap\Register\Model\ResourceModel\TempCustomer\CollectionFactory;

class TempCustomerRepository implements TempCustomerRepositoryInterface
{
    protected $objectResourceModel;

    protected $objectFactory;

    protected $collectionFactory;

    public function __construct(
        \Bap\Register\Model\ResourceModel\TempCustomer $objectResourceModel,
        TempCustomerFactory $objectFactory,
        CollectionFactory $collectionFactory
    )
    {
        $this->objectResourceModel = $objectResourceModel;
        $this->objectFactory = $objectFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(TempCustomerInterface $object): TempCustomerInterface
    {
        try {
            $this->objectResourceModel->save($object);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $object;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByToken($token)
    {
        $object = $this->objectFactory->create();
        $this->objectResourceModel->load($object, $token, TempCustomerInterface::TOKEN);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object does not exist.'));
        }
        return $object;
    }

    public function getByEmail($email)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('email', $email);

        return $collection;
    }
}
