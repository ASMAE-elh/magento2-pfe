<?php
namespace MonEntreprise\MonModule\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddDefaultConfig implements DataPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {}

    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        // Insère une valeur par défaut dans core_config_data
        $this->moduleDataSetup->getConnection()->insertOnDuplicate(
            $this->moduleDataSetup->getTable('core_config_data'),
            [
                'scope'    => 'default',
                'scope_id' => 0,
                'path'     => 'monmodule/general/installed',
                'value'    => '1'
            ]
        );

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
