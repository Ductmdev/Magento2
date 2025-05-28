<?php

namespace Bap\CustomBlog\Helper;

use Mageplaza\Blog\Helper\Data as MageplazaDataHelper;
use Exception;

class Data extends MageplazaDataHelper
{
    /**
     * @param null $type
     * @param null $id
     * @param null $storeId
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostCollection($type = null, $id = null, $storeId = null)
    {
        if ($id === null) {
            $id = $this->_request->getParam('id');
        }

        /** @var PostCollection $collection */
        $collection = $this->getPostList($storeId);

        switch ($type) {
            case self::TYPE_AUTHOR:
                $collection->addFieldToFilter('author_id', $id);
                break;
            case self::TYPE_CATEGORY:
                $collection->join(
                    ['category' => $collection->getTable('mageplaza_blog_post_category')],
                    'main_table.post_id=category.post_id AND category.category_id=' . $id,
                    ['position']
                );
                $collection->getSelect()->order('position asc');
                break;
            case self::TYPE_TAG:
                $collection->join(
                    ['tag' => $collection->getTable('mageplaza_blog_post_tag')],
                    'main_table.post_id=tag.post_id AND tag.tag_id=' . $id,
                    ['position']
                );
                $collection->getSelect()->order('position asc');
                break;
            case self::TYPE_TOPIC:
                $collection->join(
                    ['topic' => $collection->getTable('mageplaza_blog_post_topic')],
                    'main_table.post_id=topic.post_id AND topic.topic_id=' . $id,
                    ['position']
                );
                $collection->getSelect()->order('position asc');
                break;
            case self::TYPE_MONTHLY:
                $collection->getSelect()->where(new \Zend_Db_Expr("CONVERT_TZ(publish_date, 'UTC', '". $this->getTimezone() ."') LIKE '" . $id . "%'"));
                break;
        }

        return $collection;
    }
}