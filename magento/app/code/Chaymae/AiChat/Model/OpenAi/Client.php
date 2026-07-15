<?php
namespace Chaymae\AiChat\Model\OpenAi;

use Chaymae\AiChat\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class Client
{
    private const ENDPOINT_TEMPLATE = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    public function __construct(
        private readonly Curl $curl,
        private readonly Config $config,
        private readonly LoggerInterface $logger
    ) {
    }

    public function chatCompletion(string $systemPrompt, string $userMessage): string
    {
        $apiKey = $this->config->getApiKey();
        if ($apiKey === '') {
            throw new LocalizedException(__('La cle API Gemini n\'est pas configuree.'));
        }
        $url = sprintf(self::ENDPOINT_TEMPLATE, $this->config->getModel());
        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $userMessage]],
                ],
            ],
        ];
        return $this->callGemini($url, $payload);
    }

    /**
     * Envoie une image a Gemini Vision et retourne une description textuelle courte.
     *
     * @param string $imageBinaryData Contenu binaire brut de l'image (deja recadree si besoin)
     * @param string $mimeType Type MIME de l'image (ex: image/jpeg)
     */
    public function describeImage(string $imageBinaryData, string $mimeType = 'image/jpeg'): string
    {
        $apiKey = $this->config->getApiKey();
        if ($apiKey === '') {
            throw new LocalizedException(__('La cle API Gemini n\'est pas configuree.'));
        }
        $url = sprintf(self::ENDPOINT_TEMPLATE, $this->config->getModel());

        $instruction = 'Describe this clothing item or accessory in 3 to 8 simple English keywords '
            . '(garment type, color, style). Reply only with the keywords, '
            . 'separated by spaces, no sentence, no punctuation.';

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $instruction],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => base64_encode($imageBinaryData),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->callGemini($url, $payload);
    }

    private function callGemini(string $url, array $payload): string
    {
        try {
            $this->curl->addHeader('Content-Type', 'application/json');
            $this->curl->addHeader('x-goog-api-key', $this->config->getApiKey());
            $this->curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->curl->post($url, json_encode($payload));
            $status = $this->curl->getStatus();
            $body = json_decode($this->curl->getBody(), true);
            if ($status !== 200) {
                $errorMessage = $body['error']['message'] ?? 'Erreur inconnue de l\'API Gemini';
                $this->logger->error('Gemini API error: ' . $errorMessage);
                throw new LocalizedException(__('Le service IA est momentanement indisponible.'));
            }
            return trim((string) ($body['candidates'][0]['content']['parts'][0]['text'] ?? ''));
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->error('Gemini client exception: ' . $e->getMessage());
            throw new LocalizedException(__('Le service IA est momentanement indisponible.'));
        }
    }
}
