<?php
namespace Chaymae\AiChat\Model\Catalog;

use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

/**
 * Cherche dans le catalogue les produits les plus pertinents par rapport
 * au message envoye par l'utilisateur, afin de donner un contexte reel
 * (et non invente) a l'IA.
 */
class ProductFinder
{
    private const STOPWORDS = [
        'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'a',
        'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'avez', 'avoir',
        'pour', 'que', 'qui', 'quoi', 'est', 'suis', 'cherche',
        'bonjour', 'salut', 'merci', 'the', 'is', 'are', 'for', 'and', 'you',
        'have', 'looking', 'want', 'need', 'hello', 'hi', 'with', 'this',
    ];

    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly PricingHelper $pricingHelper,
        private readonly MediaConfig $mediaConfig
    ) {
    }

    public function find(string $message, int $limit = 5): array
    {
        $keywords = $this->extractKeywords($message);

        if (empty($keywords)) {
            return [];
        }

        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'description', 'image', 'small_image', 'thumbnail'])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', array('in' => array(2, 3, 4)))
            ->addUrlRewrite()
            ->addMinimalPrice()
            ->addFinalPrice();

        $keywordConditions = [];
        foreach ($keywords as $word) {
            $keywordConditions[] = ['like' => '%' . $word . '%'];
        }

        $collection->addAttributeToFilter('name', $keywordConditions, 'left');
        $collection->addAttributeToFilter('description', $keywordConditions, 'left');

        $collection->setPageSize(200);

        $scored = [];
        foreach ($collection as $product) {
            $name = mb_strtolower($product->getName());
            $description = mb_strtolower((string) $product->getDescription());

            $score = 0;
            foreach ($keywords as $word) {
                $word = mb_strtolower($word);
                if (mb_strpos($name, $word) !== false) {
                    $score += 2;
                }
                if (mb_strpos($description, $word) !== false) {
                    $score += 1;
                }
            }
            if ($score > 0) {
                $scored[] = ['product' => $product, 'score' => $score];
            }
        }

        usort($scored, static fn ($a, $b) => $b['score'] <=> $a['score']);

        $results = [];
        foreach (array_slice($scored, 0, $limit) as $entry) {
            $product = $entry['product'];
            $results[] = [
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $this->pricingHelper->currency($product->getData('min_price') ?: $product->getPrice(), true, false),
                'url' => $product->getProductUrl(),
                'image' => $this->buildImageUrl($product),
            ];
        }

        return $results;
    }

    private function buildImageUrl($product): string
    {
        // On essaie thumbnail, puis small_image, puis image, dans cet ordre
        foreach (['thumbnail', 'small_image', 'image'] as $attributeCode) {
            $value = $product->getData($attributeCode);
            if ($value && $value !== 'no_selection') {
                return rtrim($this->mediaConfig->getBaseMediaUrl(), '/') . '/' . ltrim($value, '/');
            }
        }

        return '';
    }

    private function extractKeywords(string $message): array
    {
        $message = mb_strtolower($message);
        $words = preg_split('/[^\p{L}\p{N}]+/u', $message, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $keywords = array_filter($words, static function (string $word): bool {
            return mb_strlen($word) >= 3 && !in_array($word, self::STOPWORDS, true);
        });

        return array_slice(array_values($keywords), 0, 8);
    }
}
