<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FixDnsCommand extends Command
{
    protected $signature = 'fix:dns';
    
    protected $description = 'Fix DNS issue untuk siforter.usbypkp.ac.id';

    public function handle(): int
    {
        $this->info("=== FIX DNS UNTUK SIFORTER ===\n");
        
        $domain = 'siforter.usbypkp.ac.id';
        $ip = '52.76.206.75';
        
        // Cek apakah sudah ada di /etc/hosts
        $hostsFile = '/etc/hosts';
        $hostsContent = file_get_contents($hostsFile);
        
        if (strpos($hostsContent, $domain) !== false) {
            $this->warn("⚠️  {$domain} sudah ada di /etc/hosts");
            $this->info("   Isi: " . trim(shell_exec("grep '{$domain}' {$hostsFile}")));
            $this->newLine();
            $this->info("Menguji koneksi...");
        } else {
            $this->info("Menambahkan {$domain} ke /etc/hosts...");
            
            $line = "{$ip}\t{$domain}\n";
            file_put_contents($hostsFile, $line, FILE_APPEND | LOCK_EX);
            
            $this->info("✅ Berhasil ditambahkan!");
            $this->info("   {$ip} -> {$domain}");
        }
        
        // Test koneksi
        $this->newLine();
        $this->info("Testing koneksi ke foto server...");
        
        $testUrls = [
            'https://siforter.usbypkp.ac.id/uploads/univsanggabuana/fotomhs/thumb/1112197004.jpg',
            'https://siforter.usbypkp.ac.id/uploads/univsanggabuana/fotomhs/thumb/1211237204.jpg',
        ];
        
        foreach ($testUrls as $url) {
            $this->info("\n   Testing: " . basename($url));
            try {
                $response = Http::withoutVerifying()
                    ->timeout(30)
                    ->head($url);
                
                if ($response->successful()) {
                    $size = $response->header('Content-Length');
                    $this->info("   ✅ OK! (Size: " . round($size / 1024, 2) . " KB)");
                } else {
                    $this->error("   ❌ HTTP {$response->status()}");
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("=== SELESAI ===");
        $this->info("\nJika masih gagal, coba restart PHP-FPM:");
        $this->info("   sudo systemctl restart php8.4-fpm");
        
        return 0;
    }
}
