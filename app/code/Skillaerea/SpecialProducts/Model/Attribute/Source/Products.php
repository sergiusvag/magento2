<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skillaerea\SpecialProducts\Model\Attribute\Source;

class Products extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('True'), 'value' => 1],
                ['label' => __('False'), 'value' => 0],
            ];
        }
        return $this->_options;
    }
}
