<?php
namespace MonEntreprise\MonModule\Controller\Note;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use MonEntreprise\MonModule\Model\NoteFactory;
use MonEntreprise\MonModule\Model\NoteRepository;

class Save implements HttpPostActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private RedirectFactory $redirectFactory,
        private NoteFactory $noteFactory,
        private NoteRepository $noteRepository,
        private EventManager $eventManager
    ) {}

    public function execute()
    {
        $title   = $this->request->getParam('title');
        $content = $this->request->getParam('content');
        $id      = $this->request->getParam('note_id');

        $note = $id
            ? $this->noteRepository->getById((int) $id)
            : $this->noteFactory->create();

        $note->setTitle($title);
        $note->setContent($content);

        try {
            $this->noteRepository->save($note);

            $this->eventManager->dispatch('after_note_saved', [
                'note' => $note
            ]);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $redirect = $this->redirectFactory->create();
            return $redirect->setPath('monmodule/note/create',
                ['error' => urlencode($e->getMessage())]
            );
        }

        $redirect = $this->redirectFactory->create();
        return $redirect->setPath('monmodule/note/index');
    }
}
