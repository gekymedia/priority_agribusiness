@extends('layouts.app')

@section('title', 'System Logs')

@section('content')
<div class="page-header">
    <h1 class="page-title">System Logs</h1>
    <p class="page-subtitle">Monitor Laravel application logs and debug issues</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-auto">
        <div class="agri-card py-3 px-4">
            <div class="text-muted small">Log Size</div>
            <div class="fw-bold">{{ $logStats['size'] ?? '0 MB' }}</div>
        </div>
    </div>
    <div class="col-auto">
        <div class="agri-card py-3 px-4">
            <div class="text-muted small">Last Updated</div>
            <div class="fw-bold">{{ $logStats['last_modified'] ?? 'Never' }}</div>
        </div>
    </div>
    <div class="col-auto">
        <div class="agri-card py-3 px-4">
            <div class="text-muted small">Log Files</div>
            <div class="fw-bold">{{ count($logFiles) }}</div>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="agri-card mb-4">
    <div class="agri-card-body">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-6">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="position-relative flex-grow-1" style="min-width: 180px;">
                        <input type="text" id="logSearch" placeholder="Search logs..." class="form-control form-control-sm ps-4">
                        <span class="position-absolute start-0 top-50 translate-middle-y ms-2"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <select id="levelFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="all">All Levels</option>
                        <option value="emergency">Emergency</option>
                        <option value="alert">Alert</option>
                        <option value="critical">Critical</option>
                        <option value="error">Error</option>
                        <option value="warning">Warning</option>
                        <option value="notice">Notice</option>
                        <option value="info">Info</option>
                        <option value="debug">Debug</option>
                    </select>
                    <select id="logFileSelect" class="form-select form-select-sm" style="width: auto;">
                        <option value="latest">Latest Log File</option>
                        @foreach ($logFiles as $logFile)
                            @php
                                $filePath = storage_path('logs/' . $logFile);
                                $fileSize = \Illuminate\Support\Facades\File::exists($filePath) ? $formatBytes(\Illuminate\Support\Facades\File::size($filePath)) : '0 MB';
                                $isSelected = $selectedLogFile === $logFile || ($selectedLogFile === 'latest' && $loop->first);
                            @endphp
                            <option value="{{ $logFile }}" {{ $isSelected ? 'selected' : '' }}>{{ $logFile }} ({{ $fileSize }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <button id="refreshLogs" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt me-1"></i> Refresh</button>
                    <button id="clearLogs" class="btn btn-sm btn-danger"><i class="fas fa-trash me-1"></i> Clear Current</button>
                    <button id="clearAllLogs" class="btn btn-sm btn-outline-danger"><i class="fas fa-broom me-1"></i> Clear All</button>
                    <a id="downloadLogs" href="#" class="btn btn-sm btn-success"><i class="fas fa-download me-1"></i> Download</a>
                    <a id="downloadAllLogs" href="{{ route('logs.download-all') }}" class="btn btn-sm btn-outline-success"><i class="fas fa-file-archive me-1"></i> Download All</a>
                </div>
            </div>
        </div>
        <div class="row mt-4 pt-3 border-top g-2">
            <div class="col-6 col-md-3 text-center">
                <div class="h5 mb-0 text-danger">{{ $logStats['counts']['error'] ?? 0 }}</div>
                <div class="text-muted small">Errors</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="h5 mb-0 text-warning">{{ $logStats['counts']['warning'] ?? 0 }}</div>
                <div class="text-muted small">Warnings</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="h5 mb-0 text-info">{{ $logStats['counts']['info'] ?? 0 }}</div>
                <div class="text-muted small">Info</div>
            </div>
            <div class="col-6 col-md-3 text-center">
                <div class="h5 mb-0 text-secondary">{{ $logStats['counts']['debug'] ?? 0 }}</div>
                <div class="text-muted small">Debug</div>
            </div>
        </div>
    </div>
</div>

<!-- Log entries -->
<div class="agri-card">
    <div class="agri-card-header d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0">Recent Log Entries @if(isset($selectedLogFile) && $selectedLogFile !== 'latest')<small class="text-white-50">- {{ $selectedLogFile }}</small>@endif</h5>
        <div class="d-flex align-items-center gap-3">
            <span class="small opacity-90" id="logCount">{{ count($logs) }} entries</span>
            <button id="toggleAll" class="btn btn-sm btn-light"><i class="fas fa-expand me-1"></i><span>Expand All</span></button>
        </div>
    </div>
    <div class="agri-card-body p-0">
        <div class="overflow-auto" id="logsContainer" style="max-height: calc(100vh - 420px); min-height: 360px;">
            @if (empty($logs))
                <div class="text-center p-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">No log entries in selected file</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($logs as $index => $log)
                        <div class="list-group-item log-entry" data-level="{{ $log['level'] }}" data-date="{{ $log['date'] }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="log-level-badge level-{{ $log['level'] }} badge">{{ $log['level'] }}</span>
                                    <span class="text-muted small font-monospace">{{ $log['timestamp'] }}</span>
                                    @if(isset($log['env']))<span class="badge bg-secondary">{{ $log['env'] }}</span>@endif
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-log-btn" data-log="{{ json_encode($log) }}" title="Copy"><i class="fas fa-copy"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary expand-log-btn" data-target="log-details-{{ $index }}" title="Expand"><i class="fas fa-chevron-down"></i></button>
                                </div>
                            </div>
                            <p class="mb-2 log-message text-break small">{{ $log['message'] }}</p>
                            <div id="log-details-{{ $index }}" class="log-details collapse mt-3">
                                @if(isset($log['context']) && !empty($log['context']))
                                    <div class="mb-3">
                                        <h6 class="text-muted small mb-2">Context</h6>
                                        <pre class="bg-light p-3 rounded small overflow-auto mb-0"><code>{{ json_encode($log['context'], JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif
                                @if(isset($log['stack_trace']) && !empty($log['stack_trace']))
                                    <div class="mb-3">
                                        <h6 class="text-muted small mb-2">Stack Trace</h6>
                                        <pre class="bg-light p-3 rounded small overflow-auto mb-0" style="max-height: 200px;"><code class="text-dark">{{ $log['stack_trace'] }}</code></pre>
                                    </div>
                                @endif
                                @if(isset($log['extra']) && !empty($log['extra']))
                                    <div class="mb-3">
                                        <h6 class="text-muted small mb-2">Additional</h6>
                                        <pre class="bg-light p-3 rounded small mb-0"><code>{{ json_encode($log['extra'], JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif
                            </div>
                            @if(isset($log['file']) || isset($log['user_id']) || isset($log['ip']))
                                <div class="mt-2 pt-2 border-top small text-muted">
                                    @if(isset($log['file']))<span class="font-monospace">{{ $log['file'] }}:{{ $log['line'] ?? 'N/A' }}</span>@endif
                                    @if(isset($log['user_id']))<span class="ms-2">User ID: {{ $log['user_id'] }}</span>@endif
                                    @if(isset($log['ip']))<span class="ms-2">IP: {{ $log['ip'] }}</span>@endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @if(isset($pagination) && $pagination['total'] > 0 && $pagination['last_page'] > 1)
        <div class="agri-card-body border-top d-flex justify-content-between align-items-center flex-wrap">
            <span class="text-muted small">Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total'] }}</span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    @if($pagination['current_page'] > 1)
                        <li class="page-item"><a class="page-link" href="?page={{ $pagination['current_page'] - 1 }}&log_file={{ $selectedLogFile }}">Previous</a></li>
                    @endif
                    @for($i = 1; $i <= $pagination['last_page']; $i++)
                        <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                            @if($i == $pagination['current_page'])
                                <span class="page-link">{{ $i }}</span>
                            @else
                                <a class="page-link" href="?page={{ $i }}&log_file={{ $selectedLogFile }}">{{ $i }}</a>
                            @endif
                        </li>
                    @endfor
                    @if($pagination['current_page'] < $pagination['last_page'])
                        <li class="page-item"><a class="page-link" href="?page={{ $pagination['current_page'] + 1 }}&log_file={{ $selectedLogFile }}">Next</a></li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
</div>

<!-- Clear Current Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Clear Log File</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="mb-0" id="clearCurrentFileText">Clear current log file? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmClear">Clear Log File</button>
            </div>
        </div>
    </div>
</div>
<!-- Clear All Modal -->
<div class="modal fade" id="clearAllLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Clear All Logs</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="mb-0">Clear all {{ count($logFiles) }} log files? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmClearAll">Clear All</button>
            </div>
        </div>
    </div>
</div>

<style>
.log-level-badge.level-emergency,.log-level-badge.level-critical{background-color:#dc2626!important;}
.log-level-badge.level-alert{background-color:#ea580c!important;}
.log-level-badge.level-error{background-color:#ef4444!important;}
.log-level-badge.level-warning{background-color:#f59e0b!important;}
.log-level-badge.level-notice{background-color:#3b82f6!important;}
.log-level-badge.level-info{background-color:#10b981!important;}
.log-level-badge.level-debug{background-color:#6b7280!important;}
#logsContainer::-webkit-scrollbar{width:6px;}
#logsContainer::-webkit-scrollbar-track{background:#f1f5f9;}
#logsContainer::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px;}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var logFileSelect = document.getElementById('logFileSelect');
    var downloadLogs = document.getElementById('downloadLogs');
    function setDownloadHref() {
        var v = logFileSelect ? logFileSelect.value : 'latest';
        if (downloadLogs) downloadLogs.href = '{{ route("logs.download") }}?log_file=' + encodeURIComponent(v);
    }
    setDownloadHref();
    if (logFileSelect) logFileSelect.addEventListener('change', setDownloadHref);

    var searchInput = document.getElementById('logSearch');
    var levelFilter = document.getElementById('levelFilter');
    function filterLogs() {
        var searchTerm = (searchInput && searchInput.value || '').toLowerCase();
        var level = levelFilter ? levelFilter.value : 'all';
        document.querySelectorAll('.log-entry').forEach(function(entry) {
            var entryLevel = entry.getAttribute('data-level');
            var msgEl = entry.querySelector('.log-message');
            var message = msgEl ? msgEl.textContent.toLowerCase() : '';
            var show = (searchTerm === '' || message.indexOf(searchTerm) !== -1) && (level === 'all' || entryLevel === level);
            entry.style.display = show ? 'block' : 'none';
        });
        var visible = document.querySelectorAll('.log-entry[style="display: block"]').length;
        var countEl = document.getElementById('logCount');
        if (countEl) countEl.textContent = visible + ' entries';
    }
    if (searchInput) searchInput.addEventListener('input', filterLogs);
    if (levelFilter) levelFilter.addEventListener('change', filterLogs);

    if (logFileSelect) logFileSelect.addEventListener('change', function() {
        window.location.href = '{{ route("logs.index") }}?log_file=' + encodeURIComponent(this.value);
    });

    document.querySelectorAll('.expand-log-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var target = document.getElementById(targetId);
            var icon = this.querySelector('i');
            if (target) {
                var collapse = new bootstrap.Collapse(target, { toggle: true });
                if (target.classList.contains('show')) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
                else { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
            }
        });
    });

    var toggleAll = document.getElementById('toggleAll');
    if (toggleAll) toggleAll.addEventListener('click', function() {
        var icon = this.querySelector('i');
        var expand = icon.classList.contains('fa-expand');
        document.querySelectorAll('.log-details').forEach(function(d) {
            new bootstrap.Collapse(d, { toggle: true });
        });
        document.querySelectorAll('.expand-log-btn i').forEach(function(i) {
            i.classList.toggle('fa-chevron-down', !expand);
            i.classList.toggle('fa-chevron-up', expand);
        });
        icon.classList.toggle('fa-expand', !expand);
        icon.classList.toggle('fa-compress', expand);
        this.querySelector('span').textContent = expand ? 'Collapse All' : 'Expand All';
    });
    if (toggleAll) {
        var span = toggleAll.querySelector('span');
        if (!span) { span = document.createElement('span'); span.textContent = ' Expand All'; toggleAll.appendChild(span); }
    }

    document.querySelectorAll('.copy-log-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            try {
                var log = JSON.parse(this.getAttribute('data-log'));
                var text = '[' + log.timestamp + '] ' + (log.level || '').toUpperCase() + ': ' + log.message + '\n\n' +
                    (log.context ? 'Context: ' + JSON.stringify(log.context, null, 2) + '\n\n' : '') +
                    (log.stack_trace ? 'Stack: ' + log.stack_trace + '\n\n' : '') +
                    (log.extra && Object.keys(log.extra).length ? 'Extra: ' + JSON.stringify(log.extra, null, 2) : '');
                navigator.clipboard.writeText(text).then(function() { alert('Copied to clipboard'); });
            } catch (e) { alert('Copy failed'); }
        });
    });

    document.getElementById('refreshLogs').addEventListener('click', function() { window.location.reload(); });

    document.getElementById('clearLogs').addEventListener('click', function() {
        var v = logFileSelect ? logFileSelect.value : 'latest';
        document.getElementById('clearCurrentFileText').textContent = 'Clear ' + (v === 'latest' ? 'latest log file' : v) + '? This cannot be undone.';
        new bootstrap.Modal(document.getElementById('clearLogsModal')).show();
    });
    document.getElementById('confirmClear').addEventListener('click', function() {
        var currentFile = logFileSelect ? logFileSelect.value : 'latest';
        fetch('{{ route("logs.clear") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ log_file: currentFile })
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) { bootstrap.Modal.getInstance(document.getElementById('clearLogsModal')).hide(); window.location.reload(); }
            else alert(data.message || 'Failed');
        }).catch(function() { alert('Failed to clear'); });
    });

    document.getElementById('clearAllLogs').addEventListener('click', function() { new bootstrap.Modal(document.getElementById('clearAllLogsModal')).show(); });
    document.getElementById('confirmClearAll').addEventListener('click', function() {
        fetch('{{ route("logs.clear-all") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) { bootstrap.Modal.getInstance(document.getElementById('clearAllLogsModal')).hide(); window.location.reload(); }
            else alert(data.message || 'Failed');
        }).catch(function() { alert('Failed'); });
    });

    filterLogs();
});
</script>
@endpush
@endsection
