<?php

namespace Bap\BestsellerWidget\Block\Widget;

use Magento\Catalog\Block\Product\Context;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;

class BestsellerProducts extends AbstractProduct
{
    public const DEFAULT_PRODUCTS_COUNT = 20;
    public const IDENTITIES = 'bap_best_seller_products';

    protected $_template = "Bap_BestsellerWidget::widget/bestseller_products.phtml";

    public function __construct(
        Context $context,
        private CollectionFactory $productCollectionFactory,
        private Visibility $catalogProductVisibility,
        private HttpContext $httpContext,
        private BestSellersCollectionFactory $bestSellersCollectionFactory,
        private StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Initialize block's cache
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData(
            ['cache_lifetime' => 86400, 'cache_tags' => [Product::CACHE_TAG]]
        );
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            strtoupper(self::IDENTITIES),
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $this->getProductsCount()
        ];
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function _getProductCollection()
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection->getSelect()->join(
            ['bestsellers' => $collection->getTable('sales_bestsellers_aggregated_monthly')],
            'e.entity_id = bestsellers.product_id',
            ['qty_ordered' => 'SUM(bestsellers.qty_ordered)']
        );

        $collection->getSelect()->where("bestsellers.store_id={$storeId}");
        $collection->getSelect()->group('e.entity_id');
        $collection->getSelect()->order('bestsellers.qty_ordered DESC');
        $collection->setPageSize($this->getProductsCount());

        return $this->_addProductAttributesAndPrices($collection);
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            $this->setData('products_count', self::DEFAULT_PRODUCTS_COUNT);
        }

        return $this->getData('products_count');
    }

    /**
     * Prepare collection with best sellers products
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::IDENTITIES];
    }
}
