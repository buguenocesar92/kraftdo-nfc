<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcToken;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class QrCodeController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    /**
     * Generate QR code for a token
     */
    public function generate(Request $request, NfcToken $token)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'size' => 'sometimes|integer|min:100|max:1000',
                'format' => 'sometimes|in:png,svg',
                'margin' => 'sometimes|integer|min:0|max:10',
                'color' => 'sometimes|array|size:3',
                'color.*' => 'integer|min:0|max:255',
                'backgroundColor' => 'sometimes|array|size:3',
                'backgroundColor.*' => 'integer|min:0|max:255',
                'errorCorrection' => 'sometimes|in:L,M,Q,H',
                'type' => 'sometimes|in:dataurl,raw,cached',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid parameters',
                    'errors' => $validator->errors()
                ], 422);
            }

            $options = $validator->validated();
            $type = $options['type'] ?? 'dataurl';
            unset($options['type']);

            // Track generation
            $this->qrCodeService->trackGeneration($token, $options['format'] ?? 'png');

            // Generate QR code based on type
            switch ($type) {
                case 'raw':
                    $qrCode = $this->qrCodeService->generateQrCode($token, $options);
                    $format = $options['format'] ?? 'png';
                    
                    return response($qrCode)
                        ->header('Content-Type', 'image/' . $format)
                        ->header('Content-Disposition', 'inline; filename="token-' . $token->id . '-qr.' . $format . '"');

                case 'cached':
                    $qrCode = $this->qrCodeService->generateQrCodeCached($token, $options);
                    $format = $options['format'] ?? 'png';
                    
                    return response($qrCode)
                        ->header('Content-Type', 'image/' . $format)
                        ->header('Cache-Control', 'public, max-age=3600')
                        ->header('Content-Disposition', 'inline; filename="token-' . $token->id . '-qr.' . $format . '"');

                case 'dataurl':
                default:
                    $dataUrl = $this->qrCodeService->generateQrCodeDataUrlCached($token, $options);
                    
                    return response()->json([
                        'data' => [
                            'qr_code' => $dataUrl,
                            'url' => $this->qrCodeService->getTokenUrl($token),
                            'token_id' => $token->id,
                            'options' => $options,
                        ],
                        'message' => 'QR code generated successfully'
                    ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate QR code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate multiple QR code formats
     */
    public function generateMultiple(NfcToken $token)
    {
        try {
            $qrCodes = $this->qrCodeService->generateMultipleFormats($token);
            
            // Track generation for each format
            foreach ($qrCodes as $format => $dataUrl) {
                $this->qrCodeService->trackGeneration($token, 'png');
            }

            return response()->json([
                'data' => [
                    'qr_codes' => $qrCodes,
                    'url' => $this->qrCodeService->getTokenUrl($token),
                    'token_id' => $token->id,
                ],
                'message' => 'Multiple QR codes generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate QR codes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate branded QR code
     */
    public function generateBranded(Request $request, NfcToken $token)
    {
        try {
            $validator = Validator::make($request->all(), [
                'size' => 'sometimes|integer|min:200|max:800',
                'margin' => 'sometimes|integer|min:2|max:8',
                'color' => 'sometimes|array|size:3',
                'color.*' => 'integer|min:0|max:255',
                'backgroundColor' => 'sometimes|array|size:3',
                'backgroundColor.*' => 'integer|min:0|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid branding parameters',
                    'errors' => $validator->errors()
                ], 422);
            }

            $brandingOptions = $validator->validated();
            $qrCode = $this->qrCodeService->generateBrandedQrCode($token, $brandingOptions);
            
            $this->qrCodeService->trackGeneration($token, 'png');

            return response($qrCode)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'inline; filename="token-' . $token->id . '-branded-qr.png"');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate branded QR code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate print-ready QR code
     */
    public function generatePrint(NfcToken $token)
    {
        try {
            $qrCode = $this->qrCodeService->generatePrintQrCode($token);
            
            $this->qrCodeService->trackGeneration($token, 'png');

            return response($qrCode)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="token-' . $token->id . '-print-qr.png"');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate print QR code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get QR code analytics
     */
    public function analytics(NfcToken $token)
    {
        try {
            $analytics = $this->qrCodeService->getQrCodeAnalytics($token);

            return response()->json([
                'data' => [
                    'token_id' => $token->id,
                    'analytics' => $analytics,
                    'url' => $this->qrCodeService->getTokenUrl($token),
                ],
                'message' => 'QR code analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve QR code analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear QR code cache
     */
    public function clearCache(NfcToken $token)
    {
        try {
            $this->qrCodeService->clearCache($token);

            return response()->json([
                'data' => [
                    'token_id' => $token->id,
                ],
                'message' => 'QR code cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to clear QR code cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get QR code info
     */
    public function info(NfcToken $token)
    {
        try {
            return response()->json([
                'data' => [
                    'token_id' => $token->id,
                    'token_name' => $token->name,
                    'content_type' => $token->content_type,
                    'url' => $this->qrCodeService->getTokenUrl($token),
                    'qr_endpoints' => [
                        'generate' => url("/api/tokens/{$token->id}/qr"),
                        'multiple' => url("/api/tokens/{$token->id}/qr/multiple"),
                        'info' => url("/api/tokens/{$token->id}/qr/info"),
                    ],
                    'supported_formats' => ['png', 'svg'],
                    'supported_sizes' => ['min' => 100, 'max' => 1000],
                    'error_correction_levels' => ['L', 'M', 'Q', 'H'],
                ],
                'message' => 'QR code information retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve QR code information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}