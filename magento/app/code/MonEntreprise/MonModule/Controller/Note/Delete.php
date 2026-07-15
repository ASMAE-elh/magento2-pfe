<?php
namespace MonEntreprise\MonModule\Controller\Note;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use MonEntreprise\MonModule\Model\NoteRepository;

class Delete implements HttpGetActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private RedirectFactory $redirectFactory,
        private NoteRepository $noteRepository
    ) {}

    public function execute()
    {
        $id   = (int) $this->request->getParam('id');
        $note = $this->noteRepository->getById($id);
        $this->noteRepository->delete($note);

        $redirect = $this->redirectFactory->create();
        return $redirect->setPath('monmodule/note/index');
    }
}
