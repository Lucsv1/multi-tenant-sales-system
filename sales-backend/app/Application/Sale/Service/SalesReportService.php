<?php

namespace App\Application\Sale\Service;

use App\Infra\Sale\Persistence\Eloquent\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SalesReportService
{
    public function generateReport(int $tenantId, array $filters = []): array
    {
        $query = Sale::where('tenant_id', $tenantId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        $sales = $query->with(['customer', 'user', 'items.product'])->get();

        $totalRevenue = $sales->where('status', 'completed')->sum('total');
        $totalSales = $sales->where('status', 'completed')->count();
        $cancelledSales = $sales->where('status', 'cancelled')->count();
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        return [
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'filters' => $filters,
            'sales' => $sales,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_sales' => $totalSales,
                'cancelled_sales' => $cancelledSales,
                'average_ticket' => $averageTicket,
            ],
        ];
    }

    public function generatePdf(array $reportData): string
    {
        $pdf = Pdf::loadView('reports.sales', $reportData);
        
        $filename = 'sales_report_' . time() . '.pdf';
        $path = 'reports/' . $filename;
        
        Storage::put($path, $pdf->output());
        
        return $path;
    }

    public function sendEmail(string $email, string $pdfPath, array $reportData): void
    {
        Mail::send([], [], function ($message) use ($email, $pdfPath, $reportData) {
            $message->to($email)
                ->subject('Relatório de Vendas - ' . $reportData['generated_at'])
                ->html('
                    <h1>Relatório de Vendas</h1>
                    <p>Seu relatório foi gerado com sucesso.</p>
                    <h3>Resumo:</h3>
                    <ul>
                        <li>Total de Vendas: ' . $reportData['summary']['total_sales'] . '</li>
                        <li>Faturamento Total: R$ ' . number_format($reportData['summary']['total_revenue'], 2, ',', '.') . '</li>
                        <li>Ticket Médio: R$ ' . number_format($reportData['summary']['average_ticket'], 2, ',', '.') . '</li>
                    </ul>
                    <p>O relatório está disponível em anexo.</p>
                ')
                ->attach(storage_path('app/private/' . $pdfPath));
        });
    }
}
