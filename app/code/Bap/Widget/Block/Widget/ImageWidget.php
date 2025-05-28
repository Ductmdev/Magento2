<?php

namespace Bap\Widget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ImageWidget extends Template implements BlockInterface
{
    protected $_template = 'Bap_Widget::widget/image_widget.phtml';
}
