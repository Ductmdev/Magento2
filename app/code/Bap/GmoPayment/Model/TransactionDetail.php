<?php

namespace Bap\GmoPayment\Model;

use Magento\Framework\Model\AbstractModel;
use Bap\GmoPayment\Api\Data\TransactionDetailDataInterface;
use Bap\GmoPayment\Model\ResourceModel\TransactionDetail as TransactionDetailResource;

class TransactionDetail extends AbstractModel implements TransactionDetailDataInterface
{
    protected function _construct()
    {
        $this->_init(TransactionDetailResource::class);
    }

    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    public function getAccessId()
    {
        return $this->getData(self::ACCESS_ID);
    }

    public function getAccessPass()
    {
        return $this->getData(self::ACCESS_PASS);
    }

    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function setAccessId($accessId)
    {
        return $this->setData(self::ACCESS_ID, $accessId);
    }

    public function setAccessPass($accessPass)
    {
        return $this->setData(self::ACCESS_PASS, $accessPass);
    }
}
