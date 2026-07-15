<?php
namespace Chaymae\CustomerAvatar\Controller\Account;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Upload implements HttpPostActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private RedirectFactory $redirectFactory,
        private ManagerInterface $messageManager,
        private Session $customerSession,
        private Filesystem $filesystem,
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function execute()
    {
        $redirect = $this->redirectFactory->create();
        $redirect->setPath('customer/account/index');

        // Vérifier si le client est connecté
        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('Please log in first.'));
            return $redirect;
        }

        $customerId = $this->customerSession->getCustomerId();

        $files = $this->request->getFiles('avatar');

        if (empty($files) || $files['error'] !== UPLOAD_ERR_OK) {
            $this->messageManager->addErrorMessage(__('No file uploaded or upload error.'));
            return $redirect;
        }

        // Validate type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($files['tmp_name']);
        $allowedTypes = ['image/png', 'image/jpeg'];
        if (!in_array($mimeType, $allowedTypes)) {
            $this->messageManager->addErrorMessage(__('Only PNG and JPEG images are allowed.'));
            return $redirect;
        }

        // Validate size (max 1MB)
        if ($files['size'] > 1048576) {
            $this->messageManager->addErrorMessage(__('Image size must be less than 1 MB.'));
            return $redirect;
        }

        // Save file
        $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $avatarDir = 'customer/avatar/';
        $mediaDir->create($avatarDir);

        $ext = pathinfo($files['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . $customerId . '.' . $ext;
        $filePath = $avatarDir . $fileName;

        $mediaDir->writeFile($filePath, file_get_contents($files['tmp_name']));

        // Save to customer via model (pas API)
        $customer = $this->customerSession->getCustomer();
        $customer->setData('avatar', $filePath);
        $customer->save();

        // Refresh session
        $this->customerSession->setCustomer($customer);

        $this->messageManager->addSuccessMessage(__('Avatar updated successfully.'));
        return $redirect;
    }
}
