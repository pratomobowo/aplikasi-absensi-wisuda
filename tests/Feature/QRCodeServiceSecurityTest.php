<?php

namespace Tests\Feature;

use App\Services\QRCodeService;
use Illuminate\Support\Str;
use Tests\TestCase;

class QRCodeServiceSecurityTest extends TestCase
{
    public function test_encrypt_qr_data_generates_non_json_v2_token(): void
    {
        $service = app(QRCodeService::class);

        $token = $service->encryptQRData([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]);

        $this->assertIsString($token);
        $this->assertNull(json_decode($token, true));
        $this->assertNotSame(JSON_ERROR_NONE, json_last_error());
    }

    public function test_decrypt_qr_data_decodes_v2_token(): void
    {
        $service = app(QRCodeService::class);

        $token = $service->encryptQRData([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]);

        $data = $service->decryptQRData($token);

        $this->assertSame(123, $data['ticket_id']);
        $this->assertSame('mahasiswa', $data['role']);
        $this->assertSame(456, $data['event_id']);
        $this->assertSame(2, $data['version']);
        $this->assertFalse($data['_legacy']);
    }

    public function test_decrypt_qr_data_accepts_legacy_json_temporarily(): void
    {
        $service = app(QRCodeService::class);

        $data = $service->decryptQRData(json_encode([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]));

        $this->assertSame(123, $data['ticket_id']);
        $this->assertSame('mahasiswa', $data['role']);
        $this->assertSame(456, $data['event_id']);
        $this->assertTrue($data['_legacy']);
    }

    public function test_decrypt_qr_data_rejects_tampered_token(): void
    {
        $service = app(QRCodeService::class);
        $token = $service->encryptQRData([
            'ticket_id' => 123,
            'role' => 'mahasiswa',
            'event_id' => 456,
        ]);

        $tampered = Str::replaceLast(substr($token, -1), substr($token, -1) === 'a' ? 'b' : 'a', $token);

        $this->assertNull($service->decryptQRData($tampered));
    }
}
