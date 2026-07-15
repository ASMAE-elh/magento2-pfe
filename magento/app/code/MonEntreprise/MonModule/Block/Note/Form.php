<?php
namespace MonEntreprise\MonModule\Block\Note;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MonEntreprise\MonModule\Model\NoteRepository;
use MonEntreprise\MonModule\Model\NoteFactory;

class Form extends Template
{
    public function __construct(
        Context $context,
        private NoteRepository $noteRepository,
        private NoteFactory $noteFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getNote()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            return $this->noteRepository->getById($id);
        }
        return $this->noteFactory->create();
    }
}
