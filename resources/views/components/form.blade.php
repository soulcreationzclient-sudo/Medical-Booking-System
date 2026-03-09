@props([
    'fields' => [],
    'action' => '',
    'method' => 'POST',
    'model' => null,
    'submit' => 'Save',
    'showReset' => false,
])

<div class="card shadow-sm form-animate">
    <div class="card-body">

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div class="row">
                @foreach($fields as $name => $config)
                    @php
                        $type = $config['type'] ?? 'text';
                        $label = $config['label'] ?? ucfirst(str_replace('_',' ',$name));
                        $value = old($name, $config['value'] ?? data_get($model, $name, $config['default'] ?? ''));
                        $col = $config['col'] ?? 'col-md-6';
                    @endphp

                    <div class="{{ $col }} mb-3">

                        {{-- INPUT --}}
                        @if(in_array($type, ['text','email','number','tel','password']))
                            <label class="form-label fw-semibold">{{ $label }}</label>
                            <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
                                   class="form-control @error($name) border-danger @enderror">

                        {{-- TEXTAREA --}}
                        @elseif($type === 'textarea')
                            <label class="form-label fw-semibold">{{ $label }}</label>
                            <textarea name="{{ $name }}" rows="3"
                                      class="form-control @error($name) border-danger @enderror">{{ $value }}</textarea>

                        {{-- SELECT --}}
                        @elseif($type === 'select')
                            <label class="form-label fw-semibold">{{ $label }}</label>
                            <select name="{{ $name }}"
                                    class="form-select @error($name) border-danger @enderror">
                                @foreach(($config['options'] ?? []) as $k => $text)
                                    <option value="{{ $k }}" {{ (string)$value === (string)$k ? 'selected' : '' }}>
                                        {{ $text }}
                                    </option>
                                @endforeach
                            </select>

                        {{-- CHECKBOX --}}
                        @elseif($type === 'checkbox')
                            <div class="form-check mt-4">
                                <input type="checkbox" name="{{ $name }}" value="1"
                                       class="form-check-input @error($name) border-danger @enderror"
                                       {{ old($name, data_get($model, $name)) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">{{ $label }}</label>
                            </div>

                        {{-- RADIO --}}
                        @elseif($type === 'radio')
                            <label class="form-label fw-semibold d-block">{{ $label }}</label>
                            <div class="d-flex gap-3">
                                @foreach(($config['options'] ?? []) as $k => $text)
                                    <div class="form-check">
                                        <input type="radio" name="{{ $name }}" value="{{ $k }}"
                                               class="form-check-input @error($name) border-danger @enderror"
                                               {{ (string)$value === (string)$k ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $text }}</label>
                                    </div>
                                @endforeach
                            </div>

                        {{-- FILE --}}
                        @elseif($type === 'file')
                            <label class="form-label fw-semibold">{{ $label }}</label>
                            <input type="file" name="{{ $name }}"
                                   class="form-control @error($name) border-danger @enderror">
                        @endif

                        @error($name)
                            <div class="text-danger small mt-1 shake">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                @endforeach
            </div>

            {{-- BUTTONS --}}
            <div class="d-flex justify-content-end mt-4">
                @if($showReset)
                    <button type="reset" class="btn btn-secondary btn-outline-secondary me-2">Reset</button>
                @endif
                <button type="submit" class="btn btn-primary px-4">{{ $submit }}</button>
            </div>

        </form>
    </div>
</div>
