<?php

//Ritesh

namespace Ritesh\DiscountFilter\Model\Layer\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Rating
 * @package Ritesh\DiscountFilter\Model\Layer\Filter
 */
class Discount extends AbstractFilter
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $productCollectionFactory;
    /**
     * @var PriceFactory
     */
    private $dataProvider;
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        CollectionFactory $productCollectionFactory,
        PriceFactory $dataProviderFactory,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_requestVar = 'discount';
        $this->dataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param RequestInterface $request
     * @return $this|AbstractFilter
     */
    public function apply(RequestInterface $request)
    {
        
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            return $this;
        }
        $totalproduct = 0;
        $filter = explode('-', $filter);
        if (count($filter) == 2)
            list($from, $to) = $filter;
        else {
            $from = 1;
            $to = 100;
        }
        $this->getLayer()->getState()->addFilter($this->_createItem($from . "% - " . $to . "%", 0));
        $entity_id = array();
        $collection = $this->getLayer()->getCurrentCategory()
            ->getProductCollection()
            ->addAttributeToSelect(array('sku', 'price', 'special_price'))
            ->addAttributeToFilter('special_price', ['neq' => NULL]);
        // echo $collection->getSelect();
        
        $collectionwithdiscountfromto=$this->getLayer()->getCurrentCategory()
            ->getProductCollection()
            ->addFieldToFilter('discount_percentage',array('gteq'=>$from))
            ->addFieldToFilter('discount_percentage',array('lseq'=>$to));
        
        foreach($collectionwithdiscountfromto as $product){
            array_push($entity_id,$product->getId());
        }
        $collection=$this->getLayer()
            ->getProductCollection()
            ->addAttributeToFilter('entity_id', array('in' => ($entity_id)));
        echo $collection->getSelect();
        die;
        return $collection;
    }

    /**
     * @return Phrase
     */
    public function getName(): Phrase
    {
        return __('Discount');
    }

    protected function _getItemsData(): array
    {
        $facets = array();
        if ($this->scopeConfig->getValue('discountFiltered/discountFilterGroup/isEnableDisable')) {
            $startingPercentage = $this->scopeConfig->getValue('discountFiltered/discountFilterGroup/startPercent');
            $intervalPercentage = $this->scopeConfig->getValue('discountFiltered/discountFilterGroup/rangeInterval');
            $endingPercentage = $this->scopeConfig->getValue('discountFiltered/discountFilterGroup/endPercent');
            $facets = array();
            $facets2 = array('1-20', '21-40', '41-60', '61-80', '81-99');
            if ($startingPercentage != 0 && $intervalPercentage != 0 && $endingPercentage != 0) {
                $loopto = ($endingPercentage - $startingPercentage) / $intervalPercentage;
                for ($i = 0; $i < $loopto; $i++) {
                    if ($startingPercentage >= 100)
                        break;
                    if ($startingPercentage > $endingPercentage)
                        break;
                    if ($startingPercentage + $intervalPercentage > 100) {
                        array_push($facets, $startingPercentage . "-" . '99');
                        break;
                    }
                    if ($startingPercentage + $intervalPercentage > $endingPercentage) {
                        array_push($facets, $startingPercentage . "-" . $endingPercentage);
                        break;
                    }

                    array_push($facets, $startingPercentage . "-" . $startingPercentage + $intervalPercentage);
                    if ($i == 0) {
                        $intervalPercentage--;
                        $startingPercentage++;
                    }
                    $startingPercentage = $startingPercentage + $intervalPercentage + 1;
                }
            }
            if (count($facets) == 0)
                $facets = $facets2;
            if (count($facets) >= 1) {
                foreach ($facets as $key) {
                    $filter = explode('-', $key);
                    list($from, $to) = $filter;
                    $collection = $this->getLayer()->getCurrentCategory()
                        ->getProductCollection()
                        ->addAttributeToSelect(array('sku', 'price', 'special_price'))
                        ->addAttributeToFilter('special_price', ['neq' => NULL]);

                    $count1 = 0;
                    foreach ($collection as $product) {
                        $price  = $product->getPrice();
                        $sprice = $product->getSpecialPrice();
                        if ($sprice > 0) {
                            $dis = (($price - $sprice) * 100) / $price;
                            $dis = (int)$dis;
                            if ($dis >= (int)$from && $dis <= (int)$to) {
                                $count1++;
                            }
                        }
                    }

                    if ($count1 >= 0) {
                        $this->itemDataBuilder->addItemData(
                            $from . "% - " . $to . "%",
                            $key,
                            $count1
                        );
                    }
                    $this->totalproduct[$key] = $count1;
                }
            }
            return $this->itemDataBuilder->build();
        }
    }
}
