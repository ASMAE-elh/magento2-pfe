<?php
namespace Chaymae\AiChat\Model\Vision;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

/**
 * Client vers le service Python YOLOv8 (conteneur "vision"),
 * qui detecte et localise l'objet principal d'une image.
 */
class YoloClient
{
    private const ENDPOINT = 'http://vision:8000/detect';

    public function __construct(
        private readonly Curl $curl,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param string $imageBinaryData Contenu binaire brut de l'image
     * @param string $fileName Nom de fichier (pour le Content-Disposition du multipart)
     * @return array{label: string, confidence: float, box: array{x1:int,y1:int,x2:int,y2:int}}|null
     */
    public function detectMainObject(string $imageBinaryData, string $fileName = 'upload.jpg'): ?array
    {
        try {
            $boundary = uniqid('boundary');
            $body = "--{$boundary}\r\n"
                . "Content-Disposition: form-data; name=\"file\"; filename=\"{$fileName}\"\r\n"
                . "Content-Type: application/octet-stream\r\n\r\n"
                . $imageBinaryData . "\r\n"
                . "--{$boundary}--\r\n";

            $this->curl->addHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
            $this->curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->curl->post(self::ENDPOINT, $body);

            $status = $this->curl->getStatus();
            if ($status !== 200) {
                $this->logger->error('Vision service error: HTTP ' . $status);
                return null;
            }

            $result = json_decode($this->curl->getBody(), true);
            return $result['best'] ?? null;
        } catch (\Throwable $e) {
            $this->logger->error('Vision client exception: ' . $e->getMessage());
            return null;
        }
    }
}
