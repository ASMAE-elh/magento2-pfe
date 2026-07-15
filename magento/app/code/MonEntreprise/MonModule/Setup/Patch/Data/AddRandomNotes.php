<?php
namespace MonEntreprise\MonModule\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use MonEntreprise\MonModule\Model\NoteFactory;
use MonEntreprise\MonModule\Model\NoteRepository;

class AddRandomNotes implements DataPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private NoteFactory $noteFactory,
        private NoteRepository $noteRepository
    ) {}

    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 40; $i++) {
            $note = $this->noteFactory->create();
            $note->setTitle($faker->sentence(3));
            $note->setContent($faker->paragraph(2));
            $this->noteRepository->save($note);
        }

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
