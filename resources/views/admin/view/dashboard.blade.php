@extends('admin.layouts.app')

@section('title', 'Super admin Dashboard')

@section('content')
    <div class="dashboard-wrapper p-4">

        <!-- Greeting -->
        <div class="greeting-card shadow-lg rounded-4 p-4 mb-4 text-white" {{-- style="background: linear-gradient(135deg, #4E87EFFF,#3A7DE9, #3556B5FF);"> --}}
            style="background: linear-gradient(136deg, #5a9df3 0%, #316de6 100%)">
            <h1 id="greeting" class="fw-bold mb-2"></h1>
            <p class="date-text mb-0">{{ date('l, d F Y') }}</p>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class=" fw-bold" style="margin: 0 !important;">⚡ Quick Actions</h5>
                <a href="{{ route('company.create') }}" class="view-more-attractive">
                    <i class="fa fa-plus"></i> Add Company
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4  text-center dashboard-container m-0">
            <!-- Total Companies -->
            <div class="col-lg-3  col-md-6 col-sm-12">
                <div class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center box-shodow-dashboard">
                    <div class="icon-circle bg-primary text-white mb-3">
                        <i class="fa fa-building"></i>
                    </div>
                    <h3 class="counter fw-bold" data-target="{{ $data['user'] ?? 0 }}">0</h3>
                    <small class="text-muted">Total Companies</small>
                </div>
            </div>

            <!-- Total Sites -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center box-shodow-dashboard">
                    <div class="icon-circle bg-warning text-white mb-3">
                        <i class="fa fa-map-marker-alt"></i>
                    </div>
                    <h3 class="counter fw-bold" data-target="{{ $data['sites'] ?? 0 }}">0</h3>
                    <small class="text-muted">Total Sites</small>
                </div>
            </div>

            <!-- Total Managers -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center box-shodow-dashboard">
                    <div class="icon-circle bg-danger text-white mb-3">
                        <i class="fa fa-user-shield"></i>
                    </div>
                    <h3 class="counter fw-bold" data-target="{{ $data['managers'] ?? 0 }}">0</h3>
                    <small class="text-muted">Total Site Managers</small>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="stats-card glass-card p-4 rounded-4 d-flex flex-column align-items-center box-shodow-dashboard">
                    <div class="icon-circle bg-success text-white mb-3">
                        <i class="fa fa-users"></i>
                    </div>
                    <h3 class="counter fw-bold" data-target="{{ $data['employees'] ?? 0 }}">0</h3>
                    <small class="text-muted">Total Employees</small>
                </div>
            </div>

        </div>

        <!-- Activities & Companies -->
        <div class="row" style="margin-top: 25px;">
            <div class="col-lg-6">
                <div class="dashboard-container glass-card p-4 rounded-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">🏆 Users Inquiry</h5>
                        @if ($data['totalContactUs'] > 5)
                            <a href="{{ url('admin/contact-us') }}" class="view-more-attractive">
                                View More
                                <span class="arrow">→</span>
                            </a>
                        @endif
                    </div>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table align-middle table-hover table-bordered">
                            <thead class="table-light sticky-top z-0">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Company Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['contactus'] as $key => $i)
                                    <tr data-bs-toggle="modal" data-bs-target="#viewModal{{ $i->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $i->name }}</td>
                                        <td>{{ $i->company_name }}</td>
                                        <td>{{ $i->email }}</td>
                                        <td>{{ $i->subject }}</td>
                                        <td>{{ $i->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="">
                                                <span class="badge"
                                                    style="background: linear-gradient(136deg, #5a9df3 0%, #316de6 100%)" ;>
                                                    <i class="fas fa-eye" style="margin-left: 5px;"></i>

                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="viewModal{{ $i->id }}" tabindex="-1"
                                        aria-labelledby="viewModalLabel{{ $i->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                                                <!-- Header -->
                                                <div class="modal-header bg-gradient text-white"
                                                    style="background: linear-gradient(135deg, #4f46e5, #3b82f6);">
                                                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-black"
                                                        id="viewModalLabel{{ $i->id }}">
                                                        <i class="fas fa-envelope-open-text "></i>
                                                        Inquiry Details - {{ $i->name }}
                                                    </h5>
                                                    <button type="button"
                                                        class="btn-close btn-close-black shadow-sm rounded-circle "
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <!-- Body -->
                                                <div class="modal-body p-4" style="background:#f9fafb;">

                                                    <div class="card shadow-sm border-0 rounded-3">
                                                        <div class="card-body p-4">

                                                            <!-- Sender Info -->
                                                            <div class="mb-3">
                                                                <p class="mb-2 text-secondary">
                                                                    <strong class="text-dark">👤 From:</strong>
                                                                    {{ $i->name }} &lt;{{ $i->email }}&gt;
                                                                </p>
                                                                <p class="mb-2 text-secondary">
                                                                    <strong class="text-dark">🏢 Company:</strong>
                                                                    {{ $i->company_name ?? '—' }}
                                                                </p>
                                                                <p class="mb-2 text-secondary">
                                                                    <strong class="text-dark">📌 Subject:</strong>
                                                                    {{ $i->subject ?? 'No subject' }}
                                                                </p>
                                                            </div>

                                                            <hr class="text-muted">

                                                            <!-- Message -->
                                                            <div class="mb-3">
                                                                <p class="fw-bold text-dark mb-2">📝 Message:</p>
                                                                <p class="text-secondary lh-lg"
                                                                    style="white-space: pre-line;">
                                                                    {{ $i->message ?? 'No message provided.' }}
                                                                </p>
                                                            </div>

                                                            <hr class="text-muted">

                                                            <!-- Footer -->
                                                            <div class="text-end text-muted small">
                                                                📅 Received on:
                                                                <strong>{{ $i->created_at->format('d M Y, h:i A') }}</strong>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No inquiries found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-container glass-card p-4 rounded-4 mb-4">
                    <h5 class="mb-3">🏆 Top 5 Performing Companies</h5>
                    <!-- Responsive Wrapper -->
                    <div class="table-responsive">
                        <table class=" table table-bordered align-middle table-hover  mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Company</th>
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
                                        <td>{{ $i->name }}</td>
                                        <td><span class="badge bg-warning">{{ $i->site_count }}</span></td>
                                        <td><span class="badge bg-danger">{{ $i->site_manager_count }}</span></td>
                                        <td><span class="badge bg-success">{{ $i->employee_count }}</span></td>
                                        <td>
                                            @if ($i->growth_percentage >= 0)
                                                <span class="text-success fw-bold">+{{ $i->growth_percentage }}%</span>
                                            @else
                                                <span class="text-danger fw-bold">{{ $i->growth_percentage }}%</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No companies found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Glass effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .icon-circle {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            border-radius: 50%;
        }

        .stats-card h3 {
            font-weight: bold;
            color: #111;
        }

        .stats-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.15);
            transition: 0.3s;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Greeting
        const now = new Date().toLocaleString("en-US", {
            timeZone: "Asia/Kolkata"
        });
        const hours = new Date(now).getHours();
        let greeting = "";
        const user = "{{ auth()->user()->name }}";
        if (hours < 12) greeting = "🌅 Good Morning, " + user;
        else if (hours < 17) greeting = "🌞 Good Afternoon, " + user;
        else greeting = "🌙 Good Evening, " + user;
        document.getElementById("greeting").textContent = greeting;

        // Animated counters
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

        // Company Growth Chart
        new Chart(document.getElementById('companyChart'), {
            type: 'bar',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                datasets: [{
                    label: "Companies",
                    data: [3, 7, 5, 10, 12, 15],
                    backgroundColor: "rgba(99, 102, 241, 0.8)",
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Employee Distribution Chart
        new Chart(document.getElementById('employeeChart'), {
            type: 'doughnut',
            data: {
                labels: ["Active", "On Leave", "Resigned"],
                datasets: [{
                    data: [120, 30, 15],
                    backgroundColor: ["#22c55e", "#f59e0b", "#ef4444"],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush
