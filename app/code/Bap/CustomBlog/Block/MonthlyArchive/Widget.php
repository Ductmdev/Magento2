<?php

namespace Bap\CustomBlog\Block\MonthlyArchive;

use Mageplaza\Blog\Block\MonthlyArchive\Widget as MageplazaWidget;
use Exception;

class Widget extends MageplazaWidget
{
    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDateArray()
    {
        $dateArray = [];
        foreach ($this->getPostDate() as $postDate) {
            $publishDate = new \DateTime($postDate, new \DateTimeZone('UTC'));
            $publishDate->setTimezone(new \DateTimeZone($this->helperData->getTimezone()));
            $dateArray[] = $publishDate->format('F Y');
        }

        return $dateArray;
    }
}