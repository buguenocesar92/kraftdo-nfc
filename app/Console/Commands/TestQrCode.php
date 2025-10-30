<?php

namespace App\Console\Commands;

use App\Models\NfcToken;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

class TestQrCode extends Command
{
    protected $signature = 'test:qr-code {token_id?}';
    protected $description = 'Test QR code generation for a token';

    public function handle(QrCodeService $qrCodeService)
    {
        $tokenId = $this->argument('token_id') ?? NfcToken::first()?->id;
        
        if (!$tokenId) {
            $this->error('No token ID provided and no tokens found in database');
            return 1;
        }

        $token = NfcToken::find($tokenId);
        if (!$token) {
            $this->error("Token with ID {$tokenId} not found");
            return 1;
        }

        $this->info("Testing QR code generation for token: {$token->name} (ID: {$token->id})");
        
        try {
            // Test basic QR code generation
            $this->info('1. Testing basic QR code generation...');
            $qrCode = $qrCodeService->generateQrCode($token);
            $this->info('✅ Basic QR code generated: ' . strlen($qrCode) . ' bytes');
            
            // Test data URL generation
            $this->info('2. Testing data URL generation...');
            $dataUrl = $qrCodeService->generateQrCodeDataUrl($token);
            $this->info('✅ Data URL generated: ' . substr($dataUrl, 0, 50) . '...');
            
            // Test multiple formats
            $this->info('3. Testing multiple formats...');
            $multipleFormats = $qrCodeService->generateMultipleFormats($token);
            $this->info('✅ Multiple formats generated: ' . implode(', ', array_keys($multipleFormats)));
            
            // Test token URL
            $this->info('4. Testing token URL generation...');
            $url = $qrCodeService->getTokenUrl($token);
            $this->info('✅ Token URL: ' . $url);
            
            // Test analytics
            $this->info('5. Testing analytics...');
            $analytics = $qrCodeService->getQrCodeAnalytics($token);
            $this->info('✅ Analytics: ' . json_encode($analytics));
            
            $this->info('🎉 All QR code tests passed successfully!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ QR code generation failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}