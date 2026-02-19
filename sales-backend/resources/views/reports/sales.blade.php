<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Vendas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Vendas</h1>
        <p>Gerado em: {{ $generated_at }}</p>
    </div>

    <div class="summary">
        <h3>Resumo</h3>
        <div class="summary-item">
            <strong>Total de Vendas:</strong> {{ $summary['total_sales'] }}
        </div>
        <div class="summary-item">
            <strong>Faturamento Total:</strong> R$ {{ number_format($summary['total_revenue'], 2, ',', '.') }}
        </div>
        <div class="summary-item">
            <strong>Vendas Canceladas:</strong> {{ $summary['cancelled_sales'] }}
        </div>
        <div class="summary-item">
            <strong>Ticket Médio:</strong> R$ {{ number_format($summary['average_ticket'], 2, ',', '.') }}
        </div>
    </div>

    @if(count($filters) > 0)
    <div class="filters">
        <h4>Filtros Aplicados:</h4>
        <ul>
            @foreach($filters as $key => $value)
                <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h3>Detalhamento das Vendas</h3>
    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->sale_number }}</td>
                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                <td>{{ $sale->customer->name ?? 'Sem cliente' }}</td>
                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                <td>{{ ucfirst($sale->status) }}</td>
                <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestão de Vendas - Relatório automático</p>
    </div>
</body>
</html>
