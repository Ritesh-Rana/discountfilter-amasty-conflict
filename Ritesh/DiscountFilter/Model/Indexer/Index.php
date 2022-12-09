<?php

namespace Ritesh\DiscountFilter\Model\Indexer;

use Magento\Catalog\Model\ProductRepository;
/**
 * Class Index
 *
 * @category Ritesh
 * @package  Rana
 */

class Index implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    private $action;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;
    public $i;
    protected $productRepository;
    /**
     * Index constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\ResourceModel\Product\Action $action
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ResourceModel\Product\Action $action,
        ProductRepository $productRepository
        )
    {
        $this->_resource = $resource;
        $this->action = $action;
        $this->productRepository=$productRepository;
    }

    /*
     * Used by mview, allows process indexer in the "Update on schedule" mode
     */
    public function execute($ids){

        $this->executeFull();
    }

    /*
     * Will take all of the data and reindex
     * Will run when reindex via command line
     */
    public function executeFull(){

        $connection = $this->_getConnection();
        $a = array(3034, 3167,3166 ,3783 ,3910, 3911,3912 , 3913, 3930 ,3969);
        for($i=1943;$i<=3970;$i++){
            
            // $i=3034, 3167,3166 ,3783 ,3910 - 3913, 3930 ,3969
            if(in_array($i,$a))
                continue;
            $product=$this->productRepository->getById($i);
            $insert="INSERT INTO"
        }
    }

    /**
     * Retrieve connection instance
     *
     * @return bool|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }
        return $this->_connection;
    }


    /*
     * Works with a set of entity changed (may be massaction)
     */
    public function executeList(array $ids){
        $this->executeFull();
    }


    /*
     * Works in runtime for a single entity using plugins
     */
    public function executeRow($id){
        $this->executeFull();
    }
}
