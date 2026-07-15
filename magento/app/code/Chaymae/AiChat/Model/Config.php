<?php
namespace Chaymae\AiChat\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'chaymae_aichat/general/enabled';
    private const XML_PATH_API_KEY = 'chaymae_aichat/general/api_key';
    private const XML_PATH_MODEL = 'chaymae_aichat/general/model';
    private const XML_PATH_SYSTEM_PROMPT = 'chaymae_aichat/general/system_prompt';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiKey(): string
    {
        $encrypted = (string) $this->scopeConfig->getValue(
            self::XML_PATH_API_KEY,
            ScopeInterface::SCOPE_STORE
        );

        if ($encrypted === '') {
            return '';
        }

        return $this->encryptor->decrypt($encrypted);
    }

    public function getModel(): string
    {
        $model = (string) $this->scopeConfig->getValue(
            self::XML_PATH_MODEL,
            ScopeInterface::SCOPE_STORE
        );

        return $model !== '' ? $model : 'gpt-4o-mini';
    }

    public function getSystemPrompt(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_SYSTEM_PROMPT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
