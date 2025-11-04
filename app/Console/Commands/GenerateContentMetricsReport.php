<?php

namespace App\Console\Commands;

use App\Services\ContentObservabilityService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateContentMetricsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:metrics-report {--period=24h} {--format=json} {--output=console}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate content metrics report for monitoring and analysis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->option('period');
        $format = $this->option('format');
        $output = $this->option('output');

        $this->info("Generating content metrics report for period: {$period}");

        // Get metrics from observability service
        $metrics = ContentObservabilityService::getContentMetrics($period);

        // Add additional system metrics
        $systemMetrics = $this->getSystemMetrics();
        $fullReport = array_merge($metrics, ['system' => $systemMetrics]);

        // Format and output report
        switch ($format) {
            case 'json':
                $reportContent = json_encode($fullReport, JSON_PRETTY_PRINT);
                break;
            case 'table':
                $reportContent = $this->formatAsTable($fullReport);
                break;
            default:
                $reportContent = json_encode($fullReport, JSON_PRETTY_PRINT);
        }

        // Output to destination
        switch ($output) {
            case 'file':
                $filename = 'content-metrics-' . now()->format('Y-m-d-H-i-s') . '.' . $format;
                Storage::disk('local')->put("reports/{$filename}", $reportContent);
                $this->info("Report saved to: storage/app/reports/{$filename}");
                break;
            case 'console':
            default:
                $this->line($reportContent);
                break;
        }

        $this->info('Content metrics report generated successfully!');
    }

    /**
     * Get additional system metrics
     */
    private function getSystemMetrics(): array
    {
        return [
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_load' => sys_getloadavg()[0] ?? 'N/A',
            'disk_usage_percent' => $this->getDiskUsagePercent(),
            'uptime_hours' => $this->getUptimeHours(),
        ];
    }

    /**
     * Format metrics as table for console output
     */
    private function formatAsTable(array $metrics): string
    {
        $output = "\n=== CONTENT METRICS REPORT ===\n";
        $output .= "Period: {$metrics['period']}\n";
        $output .= "Generated: {$metrics['timestamp']}\n\n";

        $output .= "CONTENT OPERATIONS:\n";
        foreach ($metrics['content_operations'] as $operation => $count) {
            $output .= sprintf("  %-15s: %d\n", ucfirst($operation), $count);
        }

        $output .= "\nPERFORMANCE:\n";
        foreach ($metrics['performance'] as $metric => $value) {
            $output .= sprintf("  %-20s: %s\n", str_replace('_', ' ', ucfirst($metric)), $value);
        }

        $output .= "\nCACHE:\n";
        foreach ($metrics['cache'] as $metric => $value) {
            $output .= sprintf("  %-20s: %s\n", str_replace('_', ' ', ucfirst($metric)), $value);
        }

        if (isset($metrics['system'])) {
            $output .= "\nSYSTEM:\n";
            foreach ($metrics['system'] as $metric => $value) {
                $output .= sprintf("  %-20s: %s\n", str_replace('_', ' ', ucfirst($metric)), $value);
            }
        }

        return $output;
    }

    /**
     * Get disk usage percentage
     */
    private function getDiskUsagePercent(): float
    {
        $path = storage_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        
        if ($total && $free) {
            return round((($total - $free) / $total) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Get system uptime in hours (Linux/Unix only)
     */
    private function getUptimeHours(): float
    {
        if (file_exists('/proc/uptime')) {
            $uptime = file_get_contents('/proc/uptime');
            $uptimeSeconds = floatval(explode(' ', $uptime)[0]);
            return round($uptimeSeconds / 3600, 2);
        }
        
        return 0;
    }
}