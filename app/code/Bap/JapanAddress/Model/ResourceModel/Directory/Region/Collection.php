<?php

declare(strict_types=1);

namespace Bap\JapanAddress\Model\ResourceModel\Directory\Region;

use Magento\Framework\Data\Collection as DataCollection;
use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;
use Magento\Directory\Model\Region as DirectoryRegion;
use Magento\Directory\Model\ResourceModel\Region as DirectoryRegionResource;

class Collection extends RegionCollection
{
    protected function _construct()
    {
        $this->_init(
            DirectoryRegion::class,
            DirectoryRegionResource::class
        );

        $this->_countryTable = $this->getTable('directory_country');
        $this->_regionNameTable = $this->getTable('directory_country_region_name');

        $this->addOrder('region_id', DataCollection::SORT_ORDER_ASC);
    }
}
