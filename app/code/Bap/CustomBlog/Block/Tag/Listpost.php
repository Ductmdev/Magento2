<?php

namespace Bap\CustomBlog\Block\Tag;

use Mageplaza\Blog\Block\Tag\Listpost as MageplazaListpost;
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
            $tag     = $this->getBlogObject();
            $tagName = 'Tag';
            if ($tag) {
                $breadcrumbs->addCrumb($tag->getUrlKey(), [
                    'label' => __($tagName),
                    'title' => __($tagName)
                ]);
            }
        }
    }
}
