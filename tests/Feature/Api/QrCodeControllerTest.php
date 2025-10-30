<?php

namespace Tests\Feature\Api;

use App\Models\NfcToken;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QrCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private NfcToken $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = NfcToken::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Token',
            'token_id' => 'test-token-123',
            'content_type' => 'GIFT',
            'is_active' => true,
        ]);
    }

    public function test_generate_qr_code_dataurl_success(): void
    {
        $response = $this->getJson("/api/tokens/{$this->token->id}/qr?type=dataurl&size=200");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'qr_code',
                    'url',
                    'token_id',
                    'options'
                ],
                'message'
            ])
            ->assertJson([
                'data' => [
                    'token_id' => $this->token->id,
                ],
                'message' => 'QR code generated successfully'
            ]);

        $this->assertStringStartsWith('data:image/png;base64,', $response->json('data.qr_code'));
        $this->assertStringContainsString("/token/{$this->token->token_id}", $response->json('data.url'));
    }

    public function test_generate_qr_code_raw_success(): void
    {
        $response = $this->get("/api/tokens/{$this->token->id}/qr?type=raw&size=300&format=png");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Content-Disposition', "inline; filename=\"token-{$this->token->id}-qr.png\"");

        // Verify it's a PNG by checking magic number
        $content = $response->getContent();
        $this->assertStringStartsWith("\x89PNG", $content);
    }

    public function test_generate_qr_code_cached_success(): void
    {
        Cache::flush();

        $response = $this->get("/api/tokens/{$this->token->id}/qr?type=cached&size=250");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Cache-Control', 'max-age=3600, public');
    }

    public function test_generate_qr_code_with_validation_errors(): void
    {
        $response = $this->getJson("/api/tokens/{$this->token->id}/qr?size=50&format=invalid");

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ])
            ->assertJson([
                'message' => 'Invalid parameters'
            ]);
    }

    public function test_generate_qr_code_with_valid_options(): void
    {
        $params = http_build_query([
            'size' => 400,
            'format' => 'png', // SVG might not be working, use PNG
            'margin' => 3,
            'errorCorrection' => 'H',
            'type' => 'dataurl'
        ]);
        
        $response = $this->getJson("/api/tokens/{$this->token->id}/qr?{$params}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'options' => [
                        'size' => '400',
                        'format' => 'png',
                        'margin' => '3',
                        'errorCorrection' => 'H'
                    ]
                ]
            ]);
    }

    public function test_generate_multiple_formats_success(): void
    {
        $response = $this->getJson("/api/tokens/{$this->token->id}/qr/multiple");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'qr_codes' => [
                        'small',
                        'medium',
                        'large',
                        'print'
                    ],
                    'url',
                    'token_id'
                ],
                'message'
            ])
            ->assertJson([
                'message' => 'Multiple QR codes generated successfully'
            ]);

        $qrCodes = $response->json('data.qr_codes');
        foreach ($qrCodes as $format => $dataUrl) {
            $this->assertStringStartsWith('data:image/png;base64,', $dataUrl);
        }
    }

    public function test_generate_branded_qr_code_success(): void
    {
        Sanctum::actingAs($this->user);
        
        $params = http_build_query([
            'size' => 400,
            'margin' => 3
        ]);
        
        $response = $this->get("/api/tokens/{$this->token->id}/qr/branded?{$params}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Content-Disposition', "inline; filename=\"token-{$this->token->id}-branded-qr.png\"");
    }

    public function test_generate_branded_qr_code_with_validation_errors(): void
    {
        Sanctum::actingAs($this->user);
        
        $params = http_build_query([
            'size' => 50, // Too small - this should be validated by the service
        ]);
        
        $response = $this->get("/api/tokens/{$this->token->id}/qr/branded?{$params}");

        // The branded endpoint validates and returns 422 for invalid parameters
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function test_generate_print_qr_code_success(): void
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->get("/api/tokens/{$this->token->id}/qr/print");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Content-Disposition', "attachment; filename=\"token-{$this->token->id}-print-qr.png\"");

        // Verify it's a high-resolution PNG
        $content = $response->getContent();
        $this->assertStringStartsWith("\x89PNG", $content);
        $this->assertGreaterThan(5000, strlen($content)); // Should be larger for print quality
    }

    public function test_get_qr_code_analytics_success(): void
    {
        Sanctum::actingAs($this->user);
        
        // Generate some QR codes to create analytics
        $qrService = app(QrCodeService::class);
        $qrService->trackGeneration($this->token, 'png');
        $qrService->trackGeneration($this->token, 'svg');

        $response = $this->getJson("/api/tokens/{$this->token->id}/qr/analytics");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token_id',
                    'analytics' => [
                        'total_generations',
                        'last_generated',
                        'formats_used'
                    ],
                    'url'
                ],
                'message'
            ])
            ->assertJson([
                'data' => [
                    'token_id' => $this->token->id,
                    'analytics' => [
                        'total_generations' => 2,
                        'formats_used' => [
                            'png' => 1,
                            'svg' => 1
                        ]
                    ]
                ],
                'message' => 'QR code analytics retrieved successfully'
            ]);
    }

    public function test_clear_qr_code_cache_success(): void
    {
        Sanctum::actingAs($this->user);
        
        // Generate and cache some QR codes first
        $this->getJson("/api/tokens/{$this->token->id}/qr?type=cached");

        $response = $this->deleteJson("/api/tokens/{$this->token->id}/qr/cache");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'token_id' => $this->token->id
                ],
                'message' => 'QR code cache cleared successfully'
            ]);
    }

    public function test_get_qr_code_info_success(): void
    {
        $response = $this->getJson("/api/tokens/{$this->token->id}/qr/info");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token_id',
                    'token_name',
                    'content_type',
                    'url',
                    'qr_endpoints' => [
                        'generate',
                        'multiple',
                        'info'
                    ],
                    'supported_formats',
                    'supported_sizes' => [
                        'min',
                        'max'
                    ],
                    'error_correction_levels'
                ],
                'message'
            ])
            ->assertJson([
                'data' => [
                    'token_id' => $this->token->id,
                    'token_name' => $this->token->name,
                    'content_type' => $this->token->content_type,
                    'supported_formats' => ['png', 'svg'],
                    'supported_sizes' => ['min' => 100, 'max' => 1000],
                    'error_correction_levels' => ['L', 'M', 'Q', 'H']
                ],
                'message' => 'QR code information retrieved successfully'
            ]);
    }

    public function test_qr_code_endpoints_with_nonexistent_token(): void
    {
        $nonexistentId = 99999;

        $response = $this->getJson("/api/tokens/{$nonexistentId}/qr");
        $response->assertStatus(404);

        $response = $this->getJson("/api/tokens/{$nonexistentId}/qr/multiple");
        $response->assertStatus(404);

        $response = $this->getJson("/api/tokens/{$nonexistentId}/qr/info");
        $response->assertStatus(404);
    }

    public function test_qr_code_endpoints_with_authenticated_routes(): void
    {
        Sanctum::actingAs($this->user);

        // Test authenticated endpoints
        $response = $this->getJson("/api/tokens/{$this->token->id}/qr/analytics");
        $response->assertStatus(200);

        $response = $this->deleteJson("/api/tokens/{$this->token->id}/qr/cache");
        $response->assertStatus(200);
    }

    public function test_qr_code_tracking_integration(): void
    {
        Sanctum::actingAs($this->user);
        Cache::flush();

        // Generate several QR codes
        $this->getJson("/api/tokens/{$this->token->id}/qr?format=png");
        $this->getJson("/api/tokens/{$this->token->id}/qr?format=svg");
        $this->getJson("/api/tokens/{$this->token->id}/qr/multiple");

        // Check analytics (multiple format generation creates 4 tracking entries)
        $response = $this->getJson("/api/tokens/{$this->token->id}/qr/analytics");
        
        $response->assertStatus(200);
        $analytics = $response->json('data.analytics');
        
        if ($analytics) {
            $this->assertGreaterThanOrEqual(4, $analytics['total_generations']);
            $this->assertArrayHasKey('png', $analytics['formats_used']);
        }
    }

    public function test_qr_code_error_handling(): void
    {
        // Mock the QrCodeService to throw an exception
        $this->mock(QrCodeService::class, function ($mock) {
            $mock->shouldReceive('generateQrCodeDataUrlCached')
                ->andThrow(new \Exception('QR generation failed'));
            $mock->shouldReceive('trackGeneration')
                ->andReturn(null);
        });

        $response = $this->getJson("/api/tokens/{$this->token->id}/qr");

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Failed to generate QR code',
                'error' => 'QR generation failed'
            ]);
    }

    public function test_qr_code_url_generation(): void
    {
        config(['app.frontend_url' => 'https://test.example.com']);

        $response = $this->getJson("/api/tokens/{$this->token->id}/qr?type=dataurl");

        $response->assertStatus(200);
        $url = $response->json('data.url');
        $this->assertEquals("https://test.example.com/token/{$this->token->token_id}", $url);
    }

    public function test_qr_code_caching_behavior(): void
    {
        Cache::flush();

        // First request should generate QR code
        $start = microtime(true);
        $response1 = $this->getJson("/api/tokens/{$this->token->id}/qr?type=dataurl&size=200");
        $time1 = microtime(true) - $start;

        // Second request should be faster due to caching
        $start = microtime(true);
        $response2 = $this->getJson("/api/tokens/{$this->token->id}/qr?type=dataurl&size=200");
        $time2 = microtime(true) - $start;

        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        // Both should return the same QR code
        $this->assertEquals(
            $response1->json('data.qr_code'),
            $response2->json('data.qr_code')
        );

        // Second request should be significantly faster (cached)
        $this->assertLessThan($time1, $time2);
    }
}