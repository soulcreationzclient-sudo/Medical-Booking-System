@if(session('success') || session('error') || session('warning') || session('info'))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">

    <div class="toast align-items-center text-white
        @if(session('success')) bg-success
        @elseif(session('error')) bg-danger
        @elseif(session('warning')) bg-warning text-dark
        @else bg-info
        @endif
        border-0 show" role="alert" aria-live="assertive" aria-atomic="true">

        <div class="d-flex">
            <div class="toast-body">
                @if(session('success'))
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                @elseif(session('error'))
                    <i class="bi bi-x-circle-fill me-2"></i>
                    {{ session('error') }}
                @elseif(session('warning'))
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('warning') }}
                @else
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ session('info') }}
                @endif
            </div>

            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>

</div>
@endif
