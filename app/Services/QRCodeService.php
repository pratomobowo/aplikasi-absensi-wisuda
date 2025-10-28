<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;

class QRCodeService
{
    /**
     * Generate QR code image as base64 string
     *
     * @param string $data
     * @return string Base64 encoded image
     */
    public function generateQRCode(string $data): string
    {
        // Use SVG backend which doesn't require any PHP extensions
        $renderer = new ImageRenderer(
            new RendererStyle(300, 1),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($data);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }

    /**
     * Encrypt QR data with AES-256 encryption
     *
     * @param array $data
     * @return string Encrypted data
     */
    public function encryptQRData(array $data): string
    {
        // Add timestamp for replay attack prevention
        $data['timestamp'] = now()->toIso8601String();
        
        // Generate HMAC signature
        $data['signature'] = $this->generateSignature($data);
        
        // Encrypt the entire payload using dedicated QR encryption key
        $encrypter = $this->getQREncrypter();
        return $encrypter->encryptString(json_encode($data));
    }

    /**
     * Decrypt QR data
     *
     * @param string $encrypted
     * @return array|null Decrypted data or null if invalid
     */
    public function decryptQRData(string $encrypted): ?array
    {
        try {
            // Decrypt using dedicated QR encryption key
            $encrypter = $this->getQREncrypter();
            $decrypted = $encrypter->decryptString($encrypted);
            $data = json_decode($decrypted, true);
            
            if (!is_array($data)) {
                return null;
            }
            
            // Validate signature
            if (!$this->validateQRSignature($data)) {
                return null;
            }
            
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate QR signature using HMAC
     *
     * @param array $data
     * @return bool
     */
    public function validateQRSignature(array $data): bool
    {
        if (!isset($data['signature'])) {
            return false;
        }
        
        $signature = $data['signature'];
        unset($data['signature']);
        
        $expectedSignature = $this->generateSignature($data);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate HMAC signature for data
     *
     * @param array $data
     * @return string
     */
    protected function generateSignature(array $data): string
    {
        // Remove signature if present
        unset($data['signature']);
        
        // Sort data for consistent signature
        ksort($data);
        
        // Generate HMAC using QR encryption key
        $key = config('security.qr_encryption.key');
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        
        return hash_hmac('sha256', json_encode($data), $key);
    }

    /**
     * Get encrypter instance for QR code encryption
     *
     * @return \Illuminate\Encryption\Encrypter
     */
    protected function getQREncrypter(): \Illuminate\Encryption\Encrypter
    {
        $key = config('security.qr_encryption.key');
        $cipher = config('security.qr_encryption.cipher');
        
        // Decode base64 key if needed
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        
        return new \Illuminate\Encryption\Encrypter($key, $cipher);
    }
}
