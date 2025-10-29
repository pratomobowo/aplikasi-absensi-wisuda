<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

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
     * Generate QR token (plain JSON without encryption)
     *
     * @param array $data
     * @return string JSON token
     */
    public function encryptQRData(array $data): string
    {
        // Add timestamp for reference
        $data['timestamp'] = now()->toIso8601String();
        
        // Return plain JSON
        return json_encode($data);
    }

    /**
     * Decode QR token (plain JSON without encryption)
     *
     * @param string $token
     * @return array|null Decoded data or null if invalid
     */
    public function decryptQRData(string $token): ?array
    {
        try {
            // Decode JSON directly
            $data = json_decode($token, true);
            
            if (!is_array($data)) {
                return null;
            }
            
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
}
