<?php

namespace Bap\Announcement\Block;

use Bap\Announcement\Helper\Data;
use Magento\Framework\View\Element\Template;

class AnnouncementText extends Template
{
    protected $_helperData;

    public function __construct(Template\Context $context, Data $helperData)
    {
        $this->_helperData = $helperData;
        parent::__construct($context);
    }

    public function getConfigurationData($config_id)
    {
        return $this->_helperData->getGeneralConfig($config_id);
    }
}
