@props(['status'])

@if ($status)
    <div class="alert alert-success mb-3" style="background: rgba(76, 175, 80, 0.2); color: var(--porco-normal); border: 1px solid rgba(76, 175, 80, 0.4); border-radius: 14px; padding: 12px;">
        <i class="fas fa-check-circle"></i> {{ $status }}
    </div>
@endif
