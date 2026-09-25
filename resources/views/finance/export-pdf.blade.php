<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Finance Ledger Export</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 18px;
        }
        .header {
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .brand {
            font-size: 18px;
            font-weight: bold;
            color: #1b5e20;
            margin: 0 0 4px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 6px;
        }
        .meta {
            color: #555;
            line-height: 1.5;
        }
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .summary td {
            width: 33.33%;
            border: 1px solid #dce7dd;
            padding: 10px 12px;
            vertical-align: top;
        }
        .summary .label {
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 4px;
        }
        .summary .value {
            font-size: 14px;
            font-weight: bold;
        }
        .income { color: #2e7d32; }
        .expense { color: #c62828; }
        table.ledger {
            width: 100%;
            border-collapse: collapse;
        }
        table.ledger th {
            background: #e8f5e9;
            color: #1b5e20;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #c8e6c9;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.ledger td {
            padding: 6px 8px;
            border: 1px solid #e5e5e5;
            vertical-align: top;
        }
        table.ledger tr:nth-child(even) td {
            background: #fafafa;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-income {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .badge-expense {
            background: #ffebee;
            color: #c62828;
        }
        .amount {
            white-space: nowrap;
            font-weight: bold;
            text-align: right;
        }
        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }
        .footer {
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="brand">{{ $appName }}</p>
        <p class="title">Finance Ledger</p>
        <div class="meta">
            Export: {{ $typeLabel }}
            &nbsp;|&nbsp;
            Period:
            @if($from || $to)
                {{ $from ? \Illuminate\Support\Carbon::parse($from)->format('M d, Y') : 'Start' }}
                –
                {{ $to ? \Illuminate\Support\Carbon::parse($to)->format('M d, Y') : 'Present' }}
            @else
                All dates
            @endif
            &nbsp;|&nbsp;
            Generated: {{ $generatedAt->format('M d, Y g:i A') }}
            &nbsp;|&nbsp;
            Records: {{ $rows->count() }}
        </div>
    </div>

    <table class="summary">
        <tr>
            @if($type !== 'expense')
                <td>
                    <div class="label">Total Income</div>
                    <div class="value income">GHC {{ number_format($incomeTotal, 2) }}</div>
                </td>
            @endif
            @if($type !== 'income')
                <td>
                    <div class="label">Total Expenses</div>
                    <div class="value expense">GHC {{ number_format($expenseTotal, 2) }}</div>
                </td>
            @endif
            @if($type === 'all')
                <td>
                    <div class="label">Balance</div>
                    <div class="value {{ $balance >= 0 ? 'income' : 'expense' }}">GHC {{ number_format($balance, 2) }}</div>
                </td>
            @endif
        </tr>
    </table>

    @if($rows->isEmpty())
        <div class="empty">No finance records found for the selected filters.</div>
    @else
        <table class="ledger">
            <thead>
                <tr>
                    <th style="width: 11%;">Date</th>
                    <th style="width: 9%;">Type</th>
                    <th style="width: 14%;">Category</th>
                    <th style="width: 34%;">Description</th>
                    <th style="width: 14%;">Source</th>
                    <th style="width: 12%;">Amount</th>
                    <th style="width: 6%;">Bank</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>{{ optional($row->date)->format('M d, Y') ?? '—' }}</td>
                        <td>
                            @if($row->entry_type === 'income')
                                <span class="badge badge-income">Income</span>
                            @else
                                <span class="badge badge-expense">Expense</span>
                            @endif
                        </td>
                        <td>{{ $row->category }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($row->description ?? '—', 120) }}</td>
                        <td>{{ $row->source }}</td>
                        <td class="amount {{ $row->entry_type === 'income' ? 'income' : 'expense' }}">
                            {{ $row->entry_type === 'income' ? '+' : '-' }}GHC {{ number_format($row->amount, 2) }}
                        </td>
                        <td>{{ $row->bank_synced ? 'Synced' : 'Pending' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Priority Agribusiness Finance Ledger export. Amounts are in Ghana Cedis (GHC).
    </div>
</body>
</html>
