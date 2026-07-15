<?php
namespace Chaymae\StoreManager\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Store\Model\StoreFactory;

class SetFrenchLocale implements DataPatchInterface
{
    private $moduleDataSetup;
    private $configResource;
    private $storeFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ConfigResource $configResource,
        StoreFactory $storeFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configResource = $configResource;
        $this->storeFactory = $storeFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $store = $this->storeFactory->create();
        $store->load('fr', 'code');

        if ($store->getId()) {
            $this->configResource->saveConfig(
                'general/locale/code',
                'fr_FR',
                'stores',
                $store->getId()
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    public static function getDependencies()
    {
        return [\Chaymae\StoreManager\Setup\Patch\Data\AddFrenchStoreView::class];
    }

    public function getAliases()
    {
        return [];
    }
}
