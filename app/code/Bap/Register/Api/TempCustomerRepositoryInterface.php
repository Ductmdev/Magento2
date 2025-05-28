<?php

namespace Bap\Register\Api;

use Bap\Register\Api\Data\TempCustomerInterface;

interface TempCustomerRepositoryInterface
{
    public function save(TempCustomerInterface $object);

    public function getByToken($token);

    public function getByEmail($email);
}
