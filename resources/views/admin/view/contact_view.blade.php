@extends('admin.layouts.app')

@section('title', 'Users Inquiry List')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ url('admin/dashboard') }}" class="heading-icon me-2">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h2 class="card-title text-dark m-0">Users Inquiry List</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped align-middle" style="min-width: 1000px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Company Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($contactUs as $key => $i)
                                        <tr>
                                            <td>{{ $contactUs->firstItem() + $key }}</td>
                                            <td>{{ $i->name }}</td>
                                            <td>{{ $i->company_name }}</td>
                                            <td>{{ $i->email }}</td>
                                            <td>{{ $i->subject }}</td>
                                            <td>
                                                {{-- <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#viewModal{{ $i->id }}">
                                                    <i class="fa-solid fa-eye"></i> View
                                                </button> --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-primary rounded-pill d-flex align-items-center gap-1 px-3 py-1 border-0"
                                                    data-bs-toggle="modal" data-bs-target="#viewModal{{ $i->id }}">
                                                    <span
                                                        class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white text-primary"
                                                        style="width: 18px; height: 18px;">
                                                        <i class="fas fa-eye"
                                                            style="font-size: 10px; margin-right: 1px"></i>
                                                    </span>
                                                    <span>View</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Modal for Viewing Inquiry -->
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

                                        <!-- End Modal -->
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No inquiries found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            {{ $contactUs->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
