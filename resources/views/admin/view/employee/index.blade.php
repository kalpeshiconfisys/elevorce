@extends('admin.layouts.app')
@section('title', 'Employee List')
@php
    $company = \App\Models\User::find($allSites[0]->employer_id);
    $data = [
        'company' => $company,
    ];
@endphp
@section('company', $data['company'])
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if ($redirectType == 0)
                                <a href="{{ url('admin/site/' . trim(base64_encode($allSites[0]->employer_id), '=')) }}"
                                    class="heading-icon">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            @else
                                <a href="{{ url('admin/company-dashboard/' . trim(base64_encode($allSites[0]->employer_id), '=')) }}"
                                    class="heading-icon">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            @endif
                            <h2 class="card-title text-dark">Employee List</h2>
                        </div>
                        <div class="site-dropdown">
                            <button class="btn site-toggle" type="button">
                                <span>
                                    <i class="fa-solid fa-building me-2 text-primary"></i>
                                    {{ Str::limit($allSites->where('id', $employer_id)->first()->name ?? 'All', 20) }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-secondary"></i>
                            </button>


                            <ul class="site-menu">
                                @php
                                    $currentSiteId = request()->segment(count(request()->segments())) ?? 0;
                                $firstSiteId =
                                    !empty($employees) && isset($employees[0]->site_id)
                                        ? trim(base64_encode($employees[0]->site_id), '=')
                                                                            : $currentSiteId;
                                @endphp
                                <li>
                                    <a href="{{ url('admin/employee-list/'.trim(base64_encode(0), '=').'?hide_site=' . $firstSiteId) }}"
                                        class="site-item @if ($employer_id == 0) active @endif">
                                        <i class="fa-solid fa-map-marker-alt me-2 text-secondary"></i>
                                        All
                                    </a>
                                </li>

                                @foreach ($allSites as $site)
                                    <li>
                                        <a href="{{ url('admin/employee-list/' . trim(base64_encode($site->id), '=')) }}"
                                            class="site-item @if ($site->id == $employer_id) active @endif">
                                            <i class="fa-solid fa-map-marker-alt me-2 text-secondary"></i>
                                            {{ $site->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped align-middle" style="min-width: 1000px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-dark">#</th>
                                        <th class="text-dark">Site</th>
                                        <th class="text-dark">Name</th>
                                        <th class="text-dark">Email</th>
                                        <th class="text-dark">Number</th>
                                        <th class="text-dark">Status</th>
                                        <th class="text-dark">Visa</th>
                                        <th class="text-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employees as $key => $emp)
                                        <tr>
                                            <td>{{ $employees->firstItem() + $key }}</td>
                                            <td>{{ Str::limit($emp->site->name ?? 'N/A', 20) }}</td>
                                            <td>{{ $emp->name }}</td>
                                            <td>{{ $emp->email }}</td>
                                            <td>{{ $emp->number }}</td>
                                            <td>
                                                @if ($emp->status == 1)
                                                    <span class="badge rounded-pill bg-success px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger px-3 py-2">
                                                        <i class="fas fa-times-circle me-1"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-primary rounded-pill d-flex align-items-center gap-1 px-3 py-1 border-0"
                                                    data-bs-toggle="modal" data-bs-target="#visaModal{{ $emp->id }}">

                                                    <span
                                                        class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-primary"
                                                        style="width: 18px; height: 18px;">
                                                        <i class="fas fa-eye"
                                                            style="font-size: 10px; margin-right: 1px"></i>
                                                    </span>

                                                    <span>View</span>
                                                </button>

                                                <!-- Visa Modal -->
                                                <div class="modal fade" id="visaModal{{ $emp->id }}" tabindex="-1"
                                                    aria-labelledby="visaModalLabel{{ $emp->id }}" aria-hidden="true">
                                                    <div
                                                        class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content visa-modal">
                                                            <!-- Header -->
                                                            <div class="modal-header visa-header border-0">
                                                                <h5 class="modal-title d-flex align-items-center gap-2"
                                                                    id="visaModalLabel{{ $emp->id }}">
                                                                    <i class="fas fa-passport"></i>
                                                                    <span>Visa Details — {{ $emp->name }}</span>
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <!-- Body -->
                                                            <div class="modal-body p-0">
                                                                @if ($emp->visa_details)
                                                                    @php
                                                                        $visa = $emp->visa_details;
                                                                        $status = strtolower($visa->visa_status);
                                                                        $badgeClass = match ($status) {
                                                                            'active' => 'bg-success',
                                                                            'pending' => 'bg-warning text-dark',
                                                                            'expired' => 'bg-danger',
                                                                            'rejected' => 'bg-secondary',
                                                                            default => 'bg-info text-dark',
                                                                        };
                                                                    @endphp
                                                                    <div class="p-3 p-md-4">
                                                                        <div class="card border-0 shadow-sm rounded-3">
                                                                            <div class="card-body p-0">
                                                                                <table
                                                                                    class="table table-modern align-middle mb-0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <th>Visa Status</th>
                                                                                            <td>
                                                                                                <span
                                                                                                    class="badge rounded-pill {{ $badgeClass }} px-3 py-2">
                                                                                                    <i
                                                                                                        class="fas fa-check-circle me-1"></i>
                                                                                                    {{ ucfirst($visa->visa_status) }}
                                                                                                </span>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Visa Number</th>
                                                                                            <td class="text-truncate"
                                                                                                title="{{ $visa->visa_number ?? 'N/A' }}">
                                                                                                {{ $visa->visa_number ?? 'N/A' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Issue Date</th>
                                                                                            <td>{{ $visa->visa_issue_date ?? 'N/A' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Expiry Date</th>
                                                                                            <td>{{ $visa->visa_expiry_date ?? 'N/A' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Share Code</th>
                                                                                            <td class="text-truncate"
                                                                                                title="{{ $visa->share_code ?? 'N/A' }}">
                                                                                                {{ $visa->share_code ?? 'N/A' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Country</th>
                                                                                            <td>{{ $visa->country ?? 'N/A' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <th>Document</th>
                                                                                            <td>
                                                                                                @if ($visa->visa_document)
                                                                                                    <a href="{{ $visa->visa_document }}"
                                                                                                        target="_blank"
                                                                                                        class="btn btn-sm btn-outline-primary">
                                                                                                        <i
                                                                                                            class="fas fa-file-pdf me-1"></i>
                                                                                                        View Document
                                                                                                    </a>
                                                                                                @else
                                                                                                    <span
                                                                                                        class="text-muted">No
                                                                                                        document
                                                                                                        uploaded</span>
                                                                                                @endif
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="p-4">
                                                                        <div class="alert alert-info mb-0">
                                                                            <i class="fas fa-info-circle me-1"></i> No visa
                                                                            details available.
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#shiftModal{{ $emp->id }}">
                                                    <i class="hover-effect fas fa-pencil text-white"></i>
                                                </button>
                                            </td>

                                            <div class="modal fade" id="shiftModal{{ $emp->id }}" tabindex="-1"
                                                aria-labelledby="shiftModalLabel{{ $emp->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="shiftModalLabel{{ $emp->id }}">
                                                                Assign Shift — {{ $emp->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <form
                                                            action="{{ url('admin/employee-update-shift/' . $emp->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="shiftSelect{{ $emp->id }}"
                                                                        class="form-label">
                                                                        Select Shift
                                                                    </label>
                                                                    <select name="shift_id"
                                                                        id="shiftSelect{{ $emp->id }}"
                                                                        class="form-select" required>
                                                                        <option value="" disabled>-- Select Shift --
                                                                        </option>
                                                                        @foreach ($shifts as $shift)
                                                                            <option value="{{ $shift->id }}"
                                                                                {{ $emp->shift_id == $shift->id ? 'selected' : '' }}>
                                                                                {{ $shift->name }}
                                                                                ({{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}
                                                                                -
                                                                                {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">
                                                                    Update
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-muted text-center">No employees found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>



                        {{-- ✅ Custom Pagination --}}
                        <div class="d-flex justify-content-end mt-1">
                            {{ $employees->appends(
                                    array_filter([
                                        'type' => $redirectType,
                                        'hide_site' => request()->query('hide_site'), // 👈 null if not set
                                    ]),
                                )->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".site-toggle").forEach(btn => {
                btn.addEventListener("click", function() {
                    let parent = this.closest(".site-dropdown");
                    parent.classList.toggle("show");
                });
            });

            // Close when clicking outside
            document.addEventListener("click", function(e) {
                document.querySelectorAll(".site-dropdown").forEach(drop => {
                    if (!drop.contains(e.target)) {
                        drop.classList.remove("show");
                    }
                });
            });
        });
    </script>
@endpush
