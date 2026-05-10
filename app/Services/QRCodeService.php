<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\Encryption\DecryptException;
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
     * Generate an authenticated encrypted QR token.
     */
    public function encryptQRData(array $data): string
    {
        $data['version'] = 2;
        $data['timestamp'] = now()->toIso8601String();

        return Crypt::encryptString(json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * Decode QR token. Supports v2 encrypted tokens and temporary legacy JSON.
     */
    public function decryptQRData(string $token): ?array
    {
        try {
            $decrypted = Crypt::decryptString($token);
            $data = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                return null;
            }

            $data['_legacy'] = false;

            return $data;
        } catch (DecryptException|\JsonException) {
            return $this->decodeLegacyQRData($token);
        } catch (\Throwable) {
            return null;
        }
    }

    private function decodeLegacyQRData(string $token): ?array
    {
        try {
            $data = json_decode($token, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                return null;
            }

            $data['_legacy'] = true;

            return $data;
        } catch (\JsonException) {
            return null;
        }
    }
}
