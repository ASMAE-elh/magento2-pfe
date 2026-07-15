<?php
declare(strict_types=1);

namespace Chaymae\MegaMenu\Plugin;

use Hyva\Theme\ViewModel\Navigation;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AddCategoryImageToNavigation
{
    private const CATEGORY_IMAGE_DIR = 'catalog/category/';

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function afterGetNavigation(Navigation $subject, array $result): array
    {
        return $this->addImages($result);
    }

    private function addImages(array $items): array
    {
        foreach ($items as $key => $item) {
            if (!empty($item['id'])) {
                $imageUrl = $this->getCategoryImageUrl((int)$item['id']);
                if ($imageUrl) {
                    $items[$key]['image'] = $imageUrl;
                }
            }

            if (!empty($item['childData']) && is_array($item['childData'])) {
                $items[$key]['childData'] = $this->addImages($item['childData']);
            }
        }

        return $items;
    }

    private function getCategoryImageUrl(int $categoryId): ?string
    {
        try {
            $category = $this->categoryRepository->get($categoryId);
            $imagePath = $category->getImage();

            if (!$imagePath) {
                return null;
            }

            $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

            return rtrim($mediaBaseUrl, '/') . '/' . self::CATEGORY_IMAGE_DIR . ltrim($imagePath, '/');
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
