@extends('layouts.app1')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h4 class="mb-1">AI Search</h4>
                    <p class="text-muted mb-0">
                        Ask questions about revenue and busiest periods using natural language.
                    </p>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('hospital_admin.ai_search.search') }}" autocomplete="off">
                        @csrf

                        <div class="mb-3">
                            <label for="query" class="form-label fw-semibold">Search Query</label>
                            <input
                                type="text"
                                name="query"
                                id="query"
                                class="form-control form-control-lg"
                                placeholder="Enter your query"
                                value="{{ old('query', $query ?? '') }}"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="off"
                                spellcheck="false"
                                required
                            >
                            @error('query')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($searchResult))
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Search Result</h5>
                    </div>
                    <div class="card-body">
                        <div class="p-3 rounded-3 mb-4" style="background:#eef8f0;border:1px solid #b7dfc1;">
                            <div class="fw-semibold mb-1" style="color:#1f5d2f;">Answer</div>
                            <div style="font-size:16px;color:#1f1f1f;">
                                {{ $searchResult['answer'] ?? 'No answer generated.' }}
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 bg-white">
                                    <div class="text-muted small mb-1">Your Query</div>
                                    <div class="fw-semibold">
                                        {{ $searchResult['original_query'] ?? ($query ?? '') }}
                                    </div>
                                </div>
                            </div>

                            @if(isset($searchResult['result']['metric']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Type</div>
                                        <div class="fw-semibold text-capitalize">
                                            {{ str_replace('_', ' ', $searchResult['result']['metric']) }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($searchResult['result']['start_date']) && isset($searchResult['result']['end_date']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Period</div>
                                        <div class="fw-semibold">
                                            {{ $searchResult['result']['start_date'] }} to {{ $searchResult['result']['end_date'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($searchResult['result']['total_revenue']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Total Revenue</div>
                                        <div class="fw-semibold">
                                            {{ $searchResult['result']['currency'] ?? '' }} {{ number_format((float) $searchResult['result']['total_revenue'], 2) }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($searchResult['result']['paid_entries_count']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Paid Entries</div>
                                        <div class="fw-semibold">
                                            {{ $searchResult['result']['paid_entries_count'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($searchResult['result']['treatment_name']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Treatment</div>
                                        <div class="fw-semibold">
                                            {{ $searchResult['result']['treatment_name'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($searchResult['result']['group_type']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Category</div>
                                        <div class="fw-semibold text-capitalize">
                                            {{ $searchResult['result']['group_type'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($searchResult['result']['period_label']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Busiest Period</div>
                                        <div class="fw-semibold">
                                            {{ $searchResult['result']['period_label'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($searchResult['result']['booking_count']))
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100 bg-white">
                                        <div class="text-muted small mb-1">Booking Count</div>
                                        <div class="fw-semibold">
                                            {{ $searchResult['result']['booking_count'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection