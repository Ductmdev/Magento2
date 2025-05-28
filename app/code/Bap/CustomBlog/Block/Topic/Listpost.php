<?php

namespace Bap\CustomBlog\Block\Topic;

use Mageplaza\Blog\Block\Topic\Listpost as MageplazaListpost;
use Exception;

class Listpost extends MageplazaListpost
{
    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $topic     = $this->getBlogObject();
            $topicName = 'Topic';
            if ($topic) {
                $breadcrumbs->addCrumb($topic->getUrlKey(), [
                    'label' => __($topicName),
                    'title' => __($topicName)
                ]);
            }
        }
    }
}
