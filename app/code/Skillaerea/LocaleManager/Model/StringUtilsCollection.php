<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Model;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Translation\Model\StringUtils;
use Magento\Translation\Model\ResourceModel\StringUtils as StringUtilsResource;

/**
 * Class StringUtilsCollection
 * @package Skillaerea\LocaleManager\Model
 */
class StringUtilsCollection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'key_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(StringUtils::class, StringUtilsResource::class);
    }
}
