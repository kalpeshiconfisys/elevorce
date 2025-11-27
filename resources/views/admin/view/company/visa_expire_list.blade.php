@extends('admin.layouts.app')

@section('title', 'Visa Expiry List')

@php
    $company = \App\Models\User::find($company->id);
    $data = [
        'company' => $company
    ];
@endphp

@section('company', $data['company'])


@section('content')
    <div class="container">
        <!-- Data Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href=" {{ url('admin/company-dashboard/' . trim(base64_encode($company->id), '=')) }}" class="heading-icon me-2">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h2 class="card-title text-dark m-0">Visa Expiry List</h2>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped align-middle" style="min-width: 1000px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Site</th>
                                        <th>Email</th>
                                        <th>Visa Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($visasExpiringSoon as $key => $visa)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $visa->employee->name ?? 'N/A' }}</td>
                                            <td>{{ $visa->employee->site->name ?? 'N/A' }}</td>
                                            <td>{{ $visa->employee->email ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-danger rounded-pill px-3 py-2">
                                                    {{ \Carbon\Carbon::parse($visa->visa_expiry_date)->format('d M, Y') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">🎉 No visas expiring soon</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-end mt-2">
                            {{ $visasExpiringSoon->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
