<?php
namespace MonEntreprise\MonModule\Model;

use MonEntreprise\MonModule\Model\ResourceModel\Note as NoteResource;
use MonEntreprise\MonModule\Model\ResourceModel\Note\CollectionFactory;

class NoteRepository
{
    public function __construct(
        private NoteFactory $noteFactory,
        private NoteResource $noteResource,
        private CollectionFactory $collectionFactory
    ) {}

    public function getById(int $id): Note
    {
        $note = $this->noteFactory->create();
        $this->noteResource->load($note, $id);
        return $note;
    }

    public function save(Note $note): void
    {
        $this->noteResource->save($note);
    }

    public function delete(Note $note): void
    {
        $this->noteResource->delete($note);
    }

    public function getAll(): \MonEntreprise\MonModule\Model\ResourceModel\Note\Collection
    {
        return $this->collectionFactory->create();
    }
}
