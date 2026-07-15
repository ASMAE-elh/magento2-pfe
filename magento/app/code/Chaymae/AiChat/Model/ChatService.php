<?php
namespace Chaymae\AiChat\Model;

use Chaymae\AiChat\Api\ChatServiceInterface;
use Chaymae\AiChat\Model\Catalog\ProductFinder;
use Chaymae\AiChat\Model\OpenAi\Client;
use Chaymae\AiChat\Model\Vision\ImageCropper;
use Chaymae\AiChat\Model\Vision\YoloClient;
use Magento\Framework\Exception\LocalizedException;

class ChatService implements ChatServiceInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly Client $openAiClient,
        private readonly ProductFinder $productFinder,
        private readonly YoloClient $yoloClient,
        private readonly ImageCropper $imageCropper
    ) {
    }

    public function sendMessage(string $message): string
    {
        $message = trim($message);
        if ($message === '') {
            throw new LocalizedException(__('Le message ne peut pas etre vide.'));
        }

        return $this->processQuery($message);
    }

    public function sendImage(string $imageBase64, string $mimeType): string
    {
        if (!$this->config->isEnabled()) {
            throw new LocalizedException(__('Le chat AI est desactive.'));
        }

        $imageData = base64_decode($imageBase64, true);
        if ($imageData === false || $imageData === '') {
            throw new LocalizedException(__('Image invalide.'));
        }

        // 1. YOLOv8 localise l'objet principal (ex: la personne qui porte le vetement)
        $detection = $this->yoloClient->detectMainObject($imageData);

        // 2. Si une zone est detectee, on recadre l'image dessus pour affiner l'analyse
        $imageToAnalyze = $detection !== null
            ? $this->imageCropper->crop($imageData, $detection['box'])
            : $imageData;

        // 3. Gemini Vision decrit l'image (recadree ou non) en mots-cles
        $keywords = $this->openAiClient->describeImage($imageToAnalyze, $mimeType);

        if (trim($keywords) === '') {
            return json_encode([
                'reply' => __('Je n\'ai pas reussi a analyser cette photo. Peux-tu essayer avec une autre image ?')->render(),
                'products' => [],
            ], JSON_UNESCAPED_UNICODE);
        }

        return $this->processQuery($keywords);
    }

    private function processQuery(string $query): string
    {
        if (!$this->config->isEnabled()) {
            throw new LocalizedException(__('Le chat AI est desactive.'));
        }

        $products = $this->productFinder->find($query, 5);
        $systemPrompt = $this->buildSystemPrompt($products);

        $reply = $this->openAiClient->chatCompletion($systemPrompt, $query);

        return json_encode([
            'reply' => $reply,
            'products' => $products,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array<int, array{name:string, sku:string, price:string, url:string}> $products
     */
    private function buildSystemPrompt(array $products): string
    {
        $prompt = $this->config->getSystemPrompt();

        if (empty($products)) {
            $prompt .= "\n\nAucun produit du catalogue ne correspond directement a la demande. "
                . "Dis-le au client au lieu d'inventer un produit.";
            return $prompt;
        }

        $prompt .= "\n\nVoici des produits du catalogue qui pourraient correspondre a la demande :\n";
        foreach ($products as $product) {
            $prompt .= sprintf(
                "- %s (SKU: %s, prix: %s)\n",
                $product['name'],
                $product['sku'],
                $product['price']
            );
        }
        $prompt .= "Appuie-toi uniquement sur cette liste pour recommander des produits.";

        return $prompt;
    }
}
