
@props([
    'data',
    'columns' => [],
])

<div class="card shadow-sm datatable-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable mb-0">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th class="text-center">{{ $col['label'] }}</th>
                        @endforeach
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($data as $row)
                        <tr>
                            @foreach($columns as $col)
                                <td class="text-center align-middle">
                                    {{ data_get($row, $col['key']) }}
                                </td>
                            @endforeach

                            {{-- ACTION MENU --}}
                            <td class="text-center align-middle">
                                <div class="dropdown">
                                    <button class="btn action-btn" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="bi bi-eye me-2"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="bi bi-pencil me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
