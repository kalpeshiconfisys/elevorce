@extends('admin.layouts.app')

@section('title', 'admin Dashboard')
@section('company', $data['company'])
@section('content')

    <div class="container-fluid mt-3">
        <div class="dashboard-wrapper">
            <div class="row">
                <div class="col">
                    <div class="welcome-section">
                        <h1 class="fw-bold" id="greeting"></h1>
                        <p>{{ date('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="dashboard-section">
                        <div class="dashboard-container">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                <div>
                                    <h3 class="dashboard-heading">Company Information</h3>
                                    <p class="dashboard-subtitle">Basic details about your company</p>
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <a href="{{ route('company.edit', trim(base64_encode($data['company']->id), '=')) }}"
                                        class="icon-btn icon-btn-edit me-2" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button type="button" class="icon-btn icon-btn-delete" title="Delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteCompanyModal{{ $data['company']->id }}">
                                        <i class="fa fa-trash-alt" aria-hidden="true"></i>
                                    </button>
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteCompanyModal{{ $data['company']->id }}" tabindex="-1"
                                        aria-labelledby="deleteCompanyModalLabel{{ $data['company']->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this company?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('company.destroy', base64_encode($data['company']->id)) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Delete Modal -->
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-12 col-md-6 col-lg-3 ">
                                    <div class="p-3 border rounded bg-light box-shodow-dashboard"
                                        style="background-color: #f3f4f6  !important;">
                                        <strong>Owner Name:</strong> {{ $data['company']->name }}
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="p-3 border rounded bg-light box-shodow-dashboard"
                                        style="background-color: #f3f4f6  !important;">
                                        <strong>Email:</strong> {{ $data['company']->email }}
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="p-3 border rounded bg-light box-shodow-dashboard"
                                        style="background-color: #f3f4f6  !important;">
                                        <strong>Phone:</strong> {{ $data['company']->number }}
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="p-3 border rounded bg-light box-shodow-dashboard"
                                        style="background-color: #f3f4f6  !important;">
                                        <strong>Address:</strong> {{ $data['company']->company_address ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Highlights -->

            <div class="row mt-3">
                <div class="col-12">
                    <div class="dashboard-section">
                        <div class="dashboard-container">
                            <div>
                                <h3 class="dashboard-heading">Company highlights</h3>
                                <p class="dashboard-subtitle">Here are some highlights of the companies you manage:</p>
                            </div>
                            <div class="row g-4">

                                <!-- Total Sites -->
                                <div class="col-lg-3 col-md-6 col-12 ">
                                    <a href="{{ url('admin/site/' . trim(base64_encode($data['company']->id), '=')) }}" class="text-decoration-none">
                                        <div
                                            class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center h-100 box-shodow-dashboard">
                                            <div class="icon-circle bg-warning text-white mb-3">
                                                <i class="fa fa-map-marker-alt"></i>
                                            </div>
                                            <div class="text-center">
                                                <span class="counter fw-bold fs-4"
                                                    data-target="{{ $data['sites'] ?? 0 }}">{{ $data['sites'] }}</span>
                                                <span class="d-block">Total Sites</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Site Manager -->
                                <div class="col-lg-3 col-md-6 col-12  ">
                                    <a @if (!empty($data['sitesData'])) href="{{ url('admin/site-manager-list/'.trim(base64_encode(0), '=')) }}?hide_site={{ trim(base64_encode($data['sitesData']->id), '=') ?? 0 }}"
                                        @else
                                                href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#noSiteModal" @endif
                                        class="text-decoration-none">
                                        <div
                                            class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center h-100 box-shodow-dashboard">
                                            <div class="icon-circle bg-danger text-white mb-3">
                                                <i class="fa fa-user-shield"></i>
                                            </div>
                                            <div class="text-center">
                                                <span class="counter fw-bold fs-4"
                                                    data-target="{{ $data['siteManagers'] ?? 0 }}">{{ $data['siteManagers'] }}</span>
                                                <span class="d-block">Total Site Manager</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Employees -->
                                <div class="col-lg-3 col-md-6 col-12 ">
                                    <a @if (!empty($data['sitesData'])) href="{{ url('admin/employee-list/'. trim(base64_encode(0), '=')) }}?hide_site={{ trim(base64_encode($data['sitesData']->id), '=') ?? 0 }}"
                                @else
                               href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#noSiteModal" @endif
                                        class="text-decoration-none">
                                        <div
                                            class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center h-100 box-shodow-dashboard">
                                            <div class="icon-circle bg-success text-white mb-3">
                                                <i class="fa fa-users"></i>
                                            </div>
                                            <div class="text-center">
                                                <span class="counter fw-bold fs-4"
                                                    data-target="{{ $data['employees'] ?? 0 }}">{{ $data['employees'] }}</span>
                                                <span class="d-block">Total Employees</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Leave Types -->
                                <div class="col-lg-3 col-md-6 col-12 ">
                                    <a href="{{ url('admin/leave-type/list/' . trim(base64_encode($data['company']->id), '=')) }}"
                                        class="text-decoration-none">
                                        <div
                                            class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center h-100 box-shodow-dashboard ">
                                            <div class="icon-circle bg-secondary text-white mb-3">
                                                <i class="fa fa-leaf"></i>
                                            </div>
                                            <div class="text-center">
                                                <span class="counter fw-bold fs-4"
                                                    data-target="{{ $data['leaveTypes'] ?? 0 }}">{{ $data['leaveTypes'] }}</span>
                                                <span class="d-block">Leave Type</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-xl-6">
                    <div class="dashboard-section">
                        <div class="dashboard-container">
                            <div>
                                <h3 class="dashboard-heading">To-Do’s</h3>
                                <p class="dashboard-subtitle">Manage and maintain your company through this panel</p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mt-3">Visa Expiry List</h5>
                                    {{-- @if ($data['visaCount'] > 5) --}}
                                        <a href="{{ url('admin/visa-expiry-list/' . trim(base64_encode($data['company']->id), '=')) }}"
                                            class="view-more-attractive">
                                            View More
                                            <span class="arrow">→</span>
                                        </a>
                                    {{-- @endif --}}
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle text-center">
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
                                            @forelse ($data['visasExpiringSoon'] as $key => $visa)
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
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        🎉 No visas expiring soon
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="dashboard-container">
                        <h5 class="mb-3 fw-bold">🏆 Top Performing</h5>
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table align-middle table-hover mb-0 table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>Site</th>
                                        <th>Managers</th>
                                        <th>Employees</th>
                                        <th>Growth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data['companies'] as $i)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-warning ">
                                                    {{ $i->site_count }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    {{ $i->site_manager_count }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ $i->employee_count }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    {{ $i->growth_percentage }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                No companies found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <!-- No Site Modal -->
                <div class="modal fade" id="noSiteModal" tabindex="-1" aria-labelledby="noSiteModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">No Site Found</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Please add a site first before managing site managers, employees, or shifts.
                            </div>
                            <div class="modal-footer">
                                <a href="{{ url('admin/site/' . $data['company']->id) }}" class="btn btn-primary">Add
                                    Site</a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->

                @if (isset($is_leave_type) && $is_leave_type == 0)
                    <div class="modal fade show" id="leaveTypeModal" tabindex="-1"
                        aria-labelledby="leaveTypeModalLabel" aria-hidden="true" style="display: block;"
                        data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content position-relative">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="leaveTypeModalLabel">Leave Setup Required</h5>
                                </div>
                                <div class="modal-body">
                                    You need to add leave types before continuing.Your company has no Leave Types yet. Add
                                    them now to unlock all features.
                                </div>
                                <div class="modal-footer">
                                    <a href="{{ url('admin/leave-type/list/' . $data['company']->id) }}"
                                        class="btn btn-primary">+ Add Leave Type</a>
                                </div>

                                <!-- Button in corner -->
                                {{-- <a href="{{ url('admin/shift/list/' . $data['company']->id) }}"
                               class="btn btn-sm btn-success position-absolute"
                               style="top:10px; right:10px;">
                                + Add Shift
                            </a> --}}
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    @endsection

    @push('script')
        <script>
            $(document).ready(function() {
                @if (isset($is_leave_type) && $is_leave_type == 0)
                    // Bootstrap 5 modal open permanently
                    var myModal = new bootstrap.Modal(document.getElementById('leaveTypeModal'), {
                        backdrop: 'static',
                        keyboard: false
                    });
                    myModal.show();
                @endif
            });


            // Set time zone to Asia/Kolkata
            const now = new Date().toLocaleString("en-US", {
                timeZone: "Asia/Kolkata"
            });
            const hours = new Date(now).getHours();

            let greeting = "";
            var user = "{{ $data['company']->name }}";
            if (hours < 12) {
                greeting = "Good morning, " + user;
            } else if (hours < 17) {
                greeting = "Good afternoon, " + user;
            } else {
                greeting = "Good evening, " + user;
            }

            document.getElementById("greeting").textContent = greeting;




            document.querySelectorAll('.counter').forEach(counter => {
                const target = +counter.getAttribute('data-target');
                let count = 0;
                const speed = target / 50;
                const update = () => {
                    if (count < target) {
                        count += speed;
                        counter.innerText = Math.ceil(count);
                        requestAnimationFrame(update);
                    } else {
                        counter.innerText = target;
                    }
                };
                update();
            });
        </script>
    @endpush
