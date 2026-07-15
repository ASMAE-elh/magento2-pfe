<?php
namespace MonEntreprise\MonModule\Plugin\Note;

use MonEntreprise\MonModule\Model\NoteRepository;
use MonEntreprise\MonModule\Model\Note;
use Magento\Framework\Exception\LocalizedException;

class ValidateNotePlugin
{
    public function beforeSave(NoteRepository $subject, Note $note): array
    {
        // Validation du titre
        if (empty(trim($note->getTitle()))) {
            throw new LocalizedException(__('Le titre ne peut pas être vide.'));
        }

        if (strlen(trim($note->getTitle())) < 3) {
            throw new LocalizedException(__('Le titre doit contenir au moins 3 caractères.'));
        }

        // Validation du contenu
        if (empty(trim($note->getContent()))) {
            throw new LocalizedException(__('Le contenu ne peut pas être vide.'));
        }

        return [$note];
    }
}
