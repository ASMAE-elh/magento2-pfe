<?php
namespace Chaymae\CustomerAvatar\Setup\Patch\Data;

use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddAvatarAttribute implements DataPatchInterface
{
    public function __construct(
        private CustomerSetupFactory $customerSetupFactory,
        private AttributeResource $attributeResource
    ) {}

    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create();

        $customerSetup->addAttribute('customer', 'avatar', [
            'type'         => 'varchar',
            'label'        => 'Avatar',
            'input'        => 'text',
            'required'     => false,
            'visible'      => false,
            'user_defined' => true,
            'position'     => 999,
            'system'       => false,
        ]);

        $attribute = $customerSetup->getEavConfig()
            ->getAttribute('customer', 'avatar')
            ->addData(['used_in_forms' => ['adminhtml_customer']]);

        $this->attributeResource->save($attribute);

        return $this;
    }

    public static function getDependencies(): array { return []; }
    public function getAliases(): array { return []; }
}
