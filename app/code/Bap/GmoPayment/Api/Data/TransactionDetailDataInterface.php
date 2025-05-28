<?php

namespace Bap\GmoPayment\Api\Data;

interface TransactionDetailDataInterface
{
    const ENTITY_ID = 'entity_id';
    const ACCESS_ID = 'access_id';
    const ACCESS_PASS = 'access_pass';
    const ORDER_ID = 'order_id';

    public function getId();

    public function setId($id);

    public function getAccessId();

    public function setAccessId($accessId);

    public function getAccessPass();

    public function setAccessPass($accessPass);

    public function getOrderId();

    public function setOrderId($orderId);
}
