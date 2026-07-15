<?php
namespace MonEntreprise\MonModule\Block\Note;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MonEntreprise\MonModule\Model\NoteRepository;

class Index extends Template
{
    public function __construct(
        Context $context,
        private NoteRepository $noteRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getNotes()
    {
        return $this->noteRepository->getAll();
    }
}
