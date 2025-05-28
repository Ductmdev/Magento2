<?php

namespace Bap\Widget\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;

class ImageChooser extends Template
{
    /**
     * @var Factory
     */
    protected $elementFactory;

    public function __construct(Template\Context $context, Factory $elementFactory, array $data = [])
    {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    public function prepareElementHtml(AbstractElement $element)
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl('cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']);

        /** @var Button $chooser */
        $chooser = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setType('button')
            ->setClass('btn-chooser')
            ->setLabel($config['button']['open'])
            ->setOnClick('MediabrowserUtility.openDialog(\'' . $sourceUrl . '\', 0, 0, "MediaBrowser", {})')
            ->setDisabled($element->getReadonly());

        /** @var Text $input */
        $input = $this->elementFactory->create("hidden", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->addCustomAttribute('data-force_static_path', 1);

        $previewImage = '<div><img id="'.$element->getId().'_preview" src="'.$element->getValue().'"
                 style="max-width: 200px; max-height: 200px; display: '.($element->getValue() ? 'block' : 'none').'; margin-top: 10px;" /></div>';

        $script = '<script>
            require(["jquery"], function($) {
                $("#'.$element->getId().'").on("change", function() {
                    var imgPath = $(this).val();
                    $("#'.$element->getId().'_preview").attr("src", imgPath).show();
                });

                $(".control-value").hide();
            });
        </script>';

        $element->setData('after_element_html', $input->getElementHtml() . $chooser->toHtml() . $previewImage . $script
            . "<script>require(['mage/adminhtml/browser']);</script>");

        return $element;
    }
}
