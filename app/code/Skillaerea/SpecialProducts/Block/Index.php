<?php
namespace Skillaerea\SpecialProducts\Block;


use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;

class Index extends \Magento\Catalog\Block\Product\ListProduct
{
    //private $productCollectionFactory;
    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param array $data
     * @param OutputHelper|null $outputHelper
     */
    public function __construct(
//        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        array $data = [],
        ?OutputHelper $outputHelper = null
    )
    {
        //$this->productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data,
            $outputHelper
        );
    }

    protected function _getProductCollection()
    {
        $productCollection = parent::_getProductCollection();

        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('is_special', true);

        return $productCollection;
    }
}
