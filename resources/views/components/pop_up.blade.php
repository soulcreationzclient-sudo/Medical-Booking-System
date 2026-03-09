@props([
    'id',
    'title' => 'Popup',
    'size' => 'md', // sm | md | lg | xl
    'showFooter' => true
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }} modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if($showFooter)
                <div class="modal-footer">
                    {{ $footer ?? '' }}
                </div>
            @endif

        </div>
    </div>
</div>
