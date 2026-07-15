<?php
namespace Chaymae\AiChat\Api;

interface ChatServiceInterface
{
    /**
     * @param string $message
     * @return string
     */
    public function sendMessage(string $message): string;

    /**
     * @param string $imageBase64 Image encodee en base64 (sans prefixe data:...)
     * @param string $mimeType Type MIME de l'image (ex: image/jpeg)
     * @return string
     */
    public function sendImage(string $imageBase64, string $mimeType): string;
}
