<link rel="stylesheet" href="{{ asset('css/Settings/audit_logs.css') }}">

<div class="content">
    <div class="header mb-4">
        <div class="title-section">
            <h4 class="mb-4">Audit Logs</h4>
        </div>
    </div>

    <div class="audit-logs-table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Created By</th>
                    <th scope="col">Type of Action</th>
                    <th scope="col">Remarks</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $auditLog)
                <tr>
                    <td>{{ $auditLog->user->name ?? 'N/A' }}</td>
                    <td class="text-uppercase text-muted fw-light">{{ $auditLog->type }}</td>
                    <td>{{ strtolower($auditLog->remarks) }}</td>
                    <td>{{ $auditLog->quantity }}</td>
                    <td>{{ $auditLog->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-links d-flex justify-content-between align-items-center mt-3">
            <div class="result-links">
                @if($auditLogs->total() > 0)
                <span class="text-muted">
                    Showing {{ $auditLogs->firstItem() }} to {{ $auditLogs->lastItem() }} of {{ $auditLogs->total() }} logs
                </span>
                @else
                <span class="text-muted">No logs found.</span>
                @endif
            </div>
            <div>
                {{ $auditLogs->appends(['tab' => 'audit-logs'])->links('pagination::simple-bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

