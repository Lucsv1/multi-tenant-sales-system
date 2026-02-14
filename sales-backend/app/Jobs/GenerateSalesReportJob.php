<?php

namespace App\Jobs;

use App\Application\Sale\Service\SalesReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateSalesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public int $tenantId,
        public int $userId,
        public array $filters = [],
        public ?string $email = null
    ) {
        $this->onQueue('reports');
    }

    public function handle(SalesReportService $reportService): void
    {
        $reportData = $reportService->generateReport($this->tenantId, $this->filters);

        $pdf = $reportService->generatePdf($reportData);

        if ($this->email) {
            $reportService->sendEmail($this->email, $pdf, $reportData);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Falha ao gerar relatório de vendas', [
            'tenant_id' => $this->tenantId,
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
