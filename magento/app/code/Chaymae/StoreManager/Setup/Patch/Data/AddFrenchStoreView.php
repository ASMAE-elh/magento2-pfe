<?php
namespace Chaymae\StoreManager\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Model\GroupFactory;
use Psr\Log\LoggerInterface;

class AddFrenchStoreView implements DataPatchInterface
{
    private $moduleDataSetup;
    private $storeFactory;
    private $websiteFactory;
    private $groupFactory;
    private $logger;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreFactory $storeFactory,
        WebsiteFactory $websiteFactory,
        GroupFactory $groupFactory,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeFactory = $storeFactory;
        $this->websiteFactory = $websiteFactory;
        $this->groupFactory = $groupFactory;
        $this->logger = $logger;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $website = $this->websiteFactory->create();
        $website->load('base', 'code');

        if (!$website->getId()) {
            $this->logger->warning('Website "base" introuvable, patch annule.');
            $this->moduleDataSetup->getConnection()->endSetup();
            return $this;
        }

        $group = $this->groupFactory->create();
        $group->load($website->getDefaultGroupId());

        $store = $this->storeFactory->create();
        $store->load('fr', 'code');

        if (!$store->getId()) {
            $store->setData([
                'code' => 'fr',
                'website_id' => $website->getId(),
                'group_id' => $group->getId(),
                'name' => 'French',
                'sort_order' => 0,
                'is_active' => 1,
            ]);
            $store->save();

            $this->logger->info('Store view "fr" cree avec succes.');
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
