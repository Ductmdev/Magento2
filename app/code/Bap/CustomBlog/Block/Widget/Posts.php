<?php

namespace Bap\CustomBlog\Block\Widget;

use Mageplaza\Blog\Block\Widget\Posts as MageplazaPosts;
use Exception;

class Posts extends MageplazaPosts
{
    /**
     * @param Post $post
     *
     * @return Phrase|string
     */
    public function getCustomPostInfo($post)
    {
        try {
            $publishDate = new \DateTime($post->getPublishDate(), new \DateTimeZone('UTC'));
            $publishDate->setTimezone(new \DateTimeZone($this->helperData->getTimezone()));
            $publishDate = $publishDate->format('Y/m/d');

            $html = __('<div class="post-date">%1</div>', $publishDate);

            if ($categoryPost = $this->getPostCategoryHtml($post)) {
                $html .= __('<div class="category">%1</div>', $categoryPost);
            }

            $html .= __('<div class="post-title"><a class="post-link-title" title="%1" href="%2">%1</a></div>', $post->getName(), $post->getUrl());
        } catch (Exception $e) {
            $html = '';
        }

        return $html;
    }
}
