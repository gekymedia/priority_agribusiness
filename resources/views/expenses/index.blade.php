@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
@php
    $sort = $sort ?? 'date';
    $direction = $direction ?? 'desc';
@endphp
<div class="page-header">
    <h1 class="page-title">Expenses</h1>
    <p class="page-subtitle">Track all farm expenses</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary me-2">
            <i class="fas fa-plus me-2"></i>Add Expense
        </a>
        <a href="{{ route('expenses.bulk-add') }}" class="btn btn-outline-primary me-2">
            <i class="fas fa-file-import me-2"></i>Bulk Add Expenses
        </a>
        <a href="{{ route('expense-categories.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-tags me-2"></i>Manage Categories
        </a>
    </div>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @php
                            $sortUrl = fn ($col) => request()->fullUrlWithQuery(['sort' => $col, 'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc', 'page' => null]);
                            $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? ' fa-sort-up' : ' fa-sort-down') : ' fa-sort text-muted';
                        @endphp
                        <th><a href="{{ $sortUrl('date') }}" class="text-decoration-none text-dark">Date</a><i class="fas{{ $sortIcon('date') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('farm') }}" class="text-decoration-none text-dark">Farm</a><i class="fas{{ $sortIcon('farm') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('batch') }}" class="text-decoration-none text-dark">Batch</a><i class="fas{{ $sortIcon('batch') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('category') }}" class="text-decoration-none text-dark">Category</a><i class="fas{{ $sortIcon('category') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('description') }}" class="text-decoration-none text-dark">Description</a><i class="fas{{ $sortIcon('description') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('amount') }}" class="text-decoration-none text-dark">Amount</a><i class="fas{{ $sortIcon('amount') }} ms-1"></i></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Group rows by date and apply a consistent light background per date-group.
                        $currentDateKey = null;
                        $groupIndex = -1;
                        $todayKey = now()->toDateString(); // Y-m-d
                        $yesterdayKey = now()->subDay()->toDateString(); // Y-m-d
                        $rowBgColors = [
                            // Keep alpha very low to avoid affecting text readability.
                            'rgba(99, 102, 241, 0.06)',  // indigo
                            'rgba(56, 189, 248, 0.07)',  // sky
                            'rgba(16, 185, 129, 0.06)',  // emerald
                            'rgba(245, 158, 11, 0.07)',  // amber
                            'rgba(239, 68, 68, 0.05)',   // red
                            'rgba(168, 85, 247, 0.06)',  // violet
                        ];
                        $rowBgCount = count($rowBgColors);
                    @endphp
                    @forelse($expenses as $expense)
                        @php
                            $dateKey = $expense->date?->format('Y-m-d');
                            if ($dateKey !== $currentDateKey) {
                                $currentDateKey = $dateKey;
                                $groupIndex++;
                            }
                            // Explicit relative coloring for readability, everything else gets a deterministic
                            // "random" color based on date.
                            if ($dateKey === $todayKey) {
                                $rowBg = 'rgba(245, 158, 11, 0.18)'; // very light yellow
                            } elseif ($dateKey === $yesterdayKey) {
                                $rowBg = 'rgba(59, 130, 246, 0.16)'; // very light blue
                            } else {
                                $colorIndex = abs(crc32((string) $dateKey)) % max(1, $rowBgCount);
                                $rowBg = $rowBgColors[$colorIndex] ?? 'transparent';
                            }
                        @endphp
                    <tr style="background-color: {{ $rowBg }};">
                        <td>{{ $expense->date->format('M d, Y') }}</td>
                        <td>{{ $expense->farm->name ?? 'N/A' }}</td>
                        <td>{{ $expense->birdBatch->batch_code ?? 'General' }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $expense->expenseCategory?->name ?? ($expense->getRawOriginal('category') ?: 'Uncategorized') }}
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($expense->description ?? 'N/A', 30) }}</td>
                        <td><strong class="text-danger">₵{{ number_format($expense->amount, 2) }}</strong></td>
                        <td>
                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No expenses recorded</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add First Expense
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
