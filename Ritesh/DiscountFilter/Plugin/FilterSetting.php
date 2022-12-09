<?php

declare(strict_types=1);

namespace Ritesh\DiscountFilter\Plugin;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class FilterSetting extends \Amasty\ShopbyBase\Helper\FilterSetting
{
    /**
     * @param FilterInterface $layerFilter
     * @return string|null
     */
    public function aftergetFilterCode($subject,$result,FilterInterface $layerFilter)
    {
        $attribute = $layerFilter->getData('attribute_model');
        $filterCode = is_object($attribute) ? $attribute->getAttributeCode() : null;

        if (!$filterCode) {
            if ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Category) {
                $filterCode = \Amasty\Shopby\Helper\Category::ATTRIBUTE_CODE;
            } elseif ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Stock) {
                $filterCode = 'stock';
            } elseif ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Rating) {
                $filterCode = 'rating';
            } elseif ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\IsNew) {
                $filterCode = 'am_is_new';
            } elseif ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\OnSale) {
                $filterCode = 'am_on_sale';
            }elseif ($layerFilter instanceof \Ritesh\DiscountFilter\Model\Layer\Filter\Discount) {
                $filterCode = 'discount';
            }
        }

        return $filterCode;
    }
}
