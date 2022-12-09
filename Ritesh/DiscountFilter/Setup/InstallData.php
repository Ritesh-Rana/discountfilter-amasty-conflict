<?php
namespace Ritesh\DiscountFilter\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	private $eavSetupFactory;

	public function __construct(EavSetupFactory $eavSetupFactory)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}
	
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'discount_percentage',
			[
				'type' => 'int',
				'backend' => '',
				'frontend' => '',
				'label' => 'Discount',
				'input' => 'text',
				'class' => '',
				'source' => '',
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
				'visible' => false,
				'required' => false,
				'user_defined' => false,
				'default' => 0,
				'searchable' => true,
				'filterable' => true,
				'comparable' => true,
				'visible_on_front' => false,
				'used_in_product_listing' => false,
				'unique' => false,
				'apply_to' => ''
			]
		);
	}
}