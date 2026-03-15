@extends('layouts.app')

@section('title', 'Egg Production')

@section('content')
@php
    $sort = $sort ?? 'date';
    $direction = $direction ?? 'desc';
@endphp
<div class="page-header">
    <h1 class="page-title">Egg Production Records</h1>
    <p class="page-subtitle">Track daily egg production from your layer batches</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('egg-productions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Production Record
        </a>
        <a href="{{ route('egg-productions.bulk-import') }}" class="btn btn-outline-primary ms-2">
            <i class="fas fa-file-import me-2"></i>Bulk Import
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
                        <th><a href="{{ $sortUrl('batch') }}" class="text-decoration-none text-dark">Batch</a><i class="fas{{ $sortIcon('batch') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('farm') }}" class="text-decoration-none text-dark">Farm</a><i class="fas{{ $sortIcon('farm') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('eggs_collected') }}" class="text-decoration-none text-dark">Eggs Collected</a><i class="fas{{ $sortIcon('eggs_collected') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('cracked_or_damaged') }}" class="text-decoration-none text-dark">Cracked/Damaged</a><i class="fas{{ $sortIcon('cracked_or_damaged') }} ms-1"></i></th>
                        <th><a href="{{ $sortUrl('eggs_used_internal') }}" class="text-decoration-none text-dark">Used Internal</a><i class="fas{{ $sortIcon('eggs_used_internal') }} ms-1"></i></th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="egg-productions-tbody">
                    @forelse($productions as $production)
                    <tr data-id="{{ $production->id }}">
                        <td>{{ $production->date->format('M d, Y') }}</td>
                        <td>{{ $production->birdBatch->batch_code ?? 'N/A' }}</td>
                        <td>{{ $production->birdBatch->farm->name ?? 'N/A' }}</td>
                        <td><strong>{{ number_format($production->eggs_collected) }}</strong></td>
                        <td>{{ number_format($production->cracked_or_damaged) }}</td>
                        <td>{{ number_format($production->eggs_used_internal) }}</td>
                        <td>
                            <span class="badge bg-success">
                                {{ number_format($production->eggs_collected - $production->cracked_or_damaged - $production->eggs_used_internal) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('egg-productions.show', $production) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('egg-productions.edit', $production) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('egg-productions.destroy', $production) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr id="egg-productions-empty">
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No egg production records found</p>
                            <a href="{{ route('egg-productions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add First Record
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex flex-wrap align-items-center gap-2" id="egg-productions-footer">
            @if($productions->hasPages())
            <div>{{ $productions->links() }}</div>
            <div class="ms-2">
                <button type="button" class="btn btn-outline-primary" id="egg-productions-show-all" title="Load all records into this page">
                    <i class="fas fa-list me-2"></i>Show all
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script>
(function() {
    var dataUrl = @json(route('egg-productions.data'));
    var baseUrl = @json(url('egg-productions'));
    var destroyUrl = baseUrl + '/';
    var csrfToken = @json(csrf_token());

    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        var d = new Date(dateStr);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function formatNum(n) { return Number(n).toLocaleString(); }

    function buildRow(p) {
        var batchCode = (p.bird_batch && p.bird_batch.batch_code) ? p.bird_batch.batch_code : 'N/A';
        var farmName = (p.bird_batch && p.bird_batch.farm && p.bird_batch.farm.name) ? p.bird_batch.farm.name : 'N/A';
        var available = (p.eggs_collected || 0) - (p.cracked_or_damaged || 0) - (p.eggs_used_internal || 0);
        var showUrl = baseUrl + '/' + p.id;
        var editUrl = baseUrl + '/' + p.id + '/edit';
        var delUrl = destroyUrl + p.id;
        return '<tr data-id="' + p.id + '">' +
            '<td>' + formatDate(p.date) + '</td>' +
            '<td>' + batchCode + '</td>' +
            '<td>' + farmName + '</td>' +
            '<td><strong>' + formatNum(p.eggs_collected) + '</strong></td>' +
            '<td>' + formatNum(p.cracked_or_damaged) + '</td>' +
            '<td>' + formatNum(p.eggs_used_internal) + '</td>' +
            '<td><span class="badge bg-success">' + formatNum(available) + '</span></td>' +
            '<td>' +
                '<a href="' + showUrl + '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ' +
                '<a href="' + editUrl + '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ' +
                '<form action="' + delUrl + '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this record?\');">' +
                    '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                    '<input type="hidden" name="_method" value="DELETE">' +
                    '<button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>' +
                '</form>' +
            '</td></tr>';
    }

    $('#egg-productions-show-all').on('click', function() {
        var $btn = $(this);
        var $footer = $('#egg-productions-footer');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading…');

        $.getJSON(dataUrl)
            .done(function(data) {
                var $tbody = $('#egg-productions-tbody');
                $('#egg-productions-empty').remove();
                if (Array.isArray(data) && data.length > 0) {
                    var html = data.map(buildRow).join('');
                    $tbody.html(html);
                }
                $footer.addClass('d-none');
            })
            .fail(function() {
                $btn.prop('disabled', false).html('<i class="fas fa-list me-2"></i>Show all');
                alert('Failed to load records. Please try again.');
            });
    });
})();
</script>
@endpush
@endsection
