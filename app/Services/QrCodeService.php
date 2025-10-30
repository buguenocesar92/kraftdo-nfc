<?php

namespace App\Services;

use App\Models\NfcToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeService
{
    /**
     * Generate QR code for a token
     */
    public function generateQrCode(NfcToken $token, array $options = []): string
    {
        $url = $this->getTokenUrl($token);
        
        // Default options
        $defaultOptions = [
            'size' => 300,
            'format' => 'png',
            'margin' => 2,
            'color' => [0, 0, 0],
            'backgroundColor' => [255, 255, 255],
            'errorCorrection' => 'M', // L, M, Q, H
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            // Configure QR options for chillerlan/php-qrcode
            $qrOptions = new QROptions([
                'version'    => 5,
                'outputType' => $options['format'] === 'svg' ? QRCode::OUTPUT_MARKUP_SVG : QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'   => $this->mapErrorCorrectionLevel($options['errorCorrection']),
                'scale'      => max(1, intval($options['size'] / 25)), // Convert size to scale
                'imageBase64' => false,
            ]);

            $qrcode = new QRCode($qrOptions);
            $qrCodeData = $qrcode->render($url);
                
            Log::info('QR code generated', [
                'token_id' => $token->id,
                'url' => $url,
                'options' => $options
            ]);
            
            return $qrCodeData;
        } catch (\Exception $e) {
            Log::error('QR code generation failed', [
                'token_id' => $token->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Failed to generate QR code: ' . $e->getMessage());
        }
    }
    
    /**
     * Map error correction level to chillerlan constants
     */
    private function mapErrorCorrectionLevel(string $level): int
    {
        return match($level) {
            'L' => QRCode::ECC_L,
            'M' => QRCode::ECC_M,
            'Q' => QRCode::ECC_Q, 
            'H' => QRCode::ECC_H,
            default => QRCode::ECC_M,
        };
    }
    
    /**
     * Generate QR code and return as base64 data URL
     */
    public function generateQrCodeDataUrl(NfcToken $token, array $options = []): string
    {
        $qrCode = $this->generateQrCode($token, $options);
        $format = $options['format'] ?? 'png';
        
        return 'data:image/' . $format . ';base64,' . base64_encode($qrCode);
    }
    
    /**
     * Generate QR code with caching
     */
    public function generateQrCodeCached(NfcToken $token, array $options = []): string
    {
        $cacheKey = $this->getCacheKey($token, $options);
        
        return Cache::remember($cacheKey, 3600, function () use ($token, $options) {
            return $this->generateQrCode($token, $options);
        });
    }
    
    /**
     * Generate QR code data URL with caching
     */
    public function generateQrCodeDataUrlCached(NfcToken $token, array $options = []): string
    {
        $cacheKey = $this->getCacheKey($token, $options) . '_dataurl';
        
        return Cache::remember($cacheKey, 3600, function () use ($token, $options) {
            return $this->generateQrCodeDataUrl($token, $options);
        });
    }
    
    /**
     * Get the public URL for a token
     */
    public function getTokenUrl(NfcToken $token): string
    {
        $frontendUrl = config('app.frontend_url', 'http://127.0.0.1:3000');
        return $frontendUrl . '/token/' . $token->token_id;
    }
    
    /**
     * Generate QR code with custom branding
     */
    public function generateBrandedQrCode(NfcToken $token, array $brandingOptions = []): string
    {
        $options = [
            'size' => $brandingOptions['size'] ?? 400,
            'format' => 'png',
            'margin' => $brandingOptions['margin'] ?? 3,
            'color' => $brandingOptions['color'] ?? [0, 0, 0],
            'backgroundColor' => $brandingOptions['backgroundColor'] ?? [255, 255, 255],
            'errorCorrection' => 'H', // High error correction for branded codes
        ];
        
        return $this->generateQrCode($token, $options);
    }
    
    /**
     * Generate QR code for print (high resolution)
     */
    public function generatePrintQrCode(NfcToken $token): string
    {
        $options = [
            'size' => 600, // High resolution for print
            'format' => 'png',
            'margin' => 4,
            'errorCorrection' => 'H',
        ];
        
        return $this->generateQrCode($token, $options);
    }
    
    /**
     * Generate multiple QR codes for different formats
     */
    public function generateMultipleFormats(NfcToken $token): array
    {
        return [
            'small' => $this->generateQrCodeDataUrlCached($token, ['size' => 150]),
            'medium' => $this->generateQrCodeDataUrlCached($token, ['size' => 300]),
            'large' => $this->generateQrCodeDataUrlCached($token, ['size' => 500]),
            'print' => $this->generateQrCodeDataUrlCached($token, ['size' => 600, 'errorCorrection' => 'H']),
        ];
    }
    
    /**
     * Clear QR code cache for a token
     */
    public function clearCache(NfcToken $token): void
    {
        $patterns = [
            $this->getCacheKey($token, []),
            $this->getCacheKey($token, []) . '_dataurl',
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
        
        // Clear all size variations
        foreach (['small', 'medium', 'large', 'print'] as $size) {
            $key = "qr_code_{$token->id}_{$size}";
            Cache::forget($key);
            Cache::forget($key . '_dataurl');
        }
    }
    
    /**
     * Get analytics for QR code usage
     */
    public function getQrCodeAnalytics(NfcToken $token): array
    {
        return [
            'total_generations' => Cache::get("qr_generated_{$token->id}", 0),
            'last_generated' => Cache::get("qr_last_generated_{$token->id}"),
            'formats_used' => Cache::get("qr_formats_{$token->id}", []),
        ];
    }
    
    /**
     * Track QR code generation
     */
    public function trackGeneration(NfcToken $token, string $format = 'png'): void
    {
        $totalKey = "qr_generated_{$token->id}";
        $lastKey = "qr_last_generated_{$token->id}";
        $formatsKey = "qr_formats_{$token->id}";
        
        Cache::increment($totalKey);
        Cache::put($lastKey, now(), 3600 * 24 * 30); // 30 days
        
        $formats = Cache::get($formatsKey, []);
        $formats[$format] = ($formats[$format] ?? 0) + 1;
        Cache::put($formatsKey, $formats, 3600 * 24 * 30);
    }
    
    /**
     * Get cache key for QR code
     */
    private function getCacheKey(NfcToken $token, array $options): string
    {
        $optionsHash = md5(serialize($options));
        return "qr_code_{$token->id}_{$optionsHash}";
    }
    
    /**
     * Validate QR code options
     */
    public function validateOptions(array $options): array
    {
        $validated = [];
        
        // Size validation
        if (isset($options['size'])) {
            $validated['size'] = max(100, min(1000, (int) $options['size']));
        }
        
        // Format validation
        if (isset($options['format'])) {
            $validated['format'] = in_array($options['format'], ['png', 'svg']) ? $options['format'] : 'png';
        }
        
        // Margin validation
        if (isset($options['margin'])) {
            $validated['margin'] = max(0, min(10, (int) $options['margin']));
        }
        
        // Error correction validation
        if (isset($options['errorCorrection'])) {
            $validated['errorCorrection'] = in_array($options['errorCorrection'], ['L', 'M', 'Q', 'H']) 
                ? $options['errorCorrection'] : 'M';
        }
        
        return $validated;
    }
}