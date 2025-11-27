@extends('admin.layouts.app')

@section('title', 'Site Details')

@php
    $company = \App\Models\User::find($employer_id);
    $data = [
        'company' => $company
    ];
@endphp

@section('company', $data['company'])

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-8 m-auto">
                <div style="border-bottom:1px solid #e0e2e6;">
                    <div class="breadcrumb-bar">
                        <a href="{{ url('admin/site', trim(base64_encode($site->employer_id), '=')) }}"
                            class="d-inline-flex align-items-center text-decoration-none">
                            <span class="breadcrumb-icon me-2">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            Site Details
                        </a>
                    </div>
                </div>
                <div class="employee-section-header d-flex align-items-center justify-content-between"
                    style="border-bottom:1px solid #e0e2e6;">
                    <div>
                        <h3>{{ $site->name }}</h3>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ url('admin/site/edit/' . trim(base64_encode($site->id), '=') . '/' . trim(base64_encode($site->employer_id), '=')) }}"
                            class="icon-btn icon-btn-edit me-2" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        {{-- <form action="{{ route('site.destroy', $site->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure to delete this site?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-btn icon-btn-delete" title="Delete">
                                <i class="fa fa-trash-alt" aria-hidden="true"></i>
                            </button>
                        </form> --}}
                        <!-- Delete Trigger Button -->
                        <button type="button" class="icon-btn icon-btn-delete" title="Delete" data-bs-toggle="modal"
                            data-bs-target="#deleteSiteModal{{ $site->id }}">
                            <i class="fa fa-trash-alt" aria-hidden="true"></i>
                        </button>
                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="deleteSiteModal{{ $site->id }}" tabindex="-1"
                            aria-labelledby="deleteSiteModalLabel{{ $site->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteSiteModalLabel{{ $site->id }}">Confirm Delete
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this site?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('site.destroy', trim(base64_encode($site->id), '=')) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-7 col-md-9">
                <div class="card border-1 position-relative" style="border-color:#d3d6db;">
                    <div class="card-header position-absolute" style="border-bottom:none; top:-20px;">
                        <span
                            style="background:#868e96; color:#fff; padding:2px 18px; border-radius:2px; font-size:1rem; font-weight:500;">SITE
                            DETAILS</span>
                    </div>
                    <div class="card-body pt-4">
                        <table class="table mb-0" style="border: none;">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="width:180px; border:none;">Site Name</td>
                                    <td style="border:none;">{{ $site->name }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Address</td>
                                    <td style="border:none;">{{ $site->address }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Company</td>
                                    <td style="border:none;">{{ $site->employer->company_name ?? 'N/A' }}</td>
                                </tr>
                                {{-- <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Site Cover Image</td>
                                    <td style="border:none;"><img src="{{ $site->image }}" width="100" class="mb-2">
                                    </td>
                                </tr> --}}
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Site Cover Image</td>
                                    <td style="border:none;">
                                        @if ($site->image)
                                            {{ basename($site->image) }}
                                            <button class="image_view ms-2" data-bs-toggle="modal"
                                                data-bs-target="#viewImageModal" data-image="{{ asset($site->image) }}">
                                                View
                                            </button>
                                        @else
                                            <span class="text-muted">No image available</span>
                                        @endif
                                    </td>
                                </tr>
                                {{-- Bootstrap Modal --}}
                                <div class="modal fade" id="viewImageModal" tabindex="-1"
                                    aria-labelledby="viewImageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Image Preview</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="" id="previewImage" class="img-fluid rounded shadow"
                                                    style="max-height:80vh;">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Created At</td>
                                    <td style="border:none;">{{ $site->created_at->format('d M, Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




        <!-- 🔹 Shifts Table -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-7 col-md-9">
                <div class="card border-1 position-relative" style="border-color:#d3d6db;">
                    <div class="card-header position-absolute" style="border-bottom:none; top:-20px;">
                        <span
                            style="background:#868e96; color:#fff; padding:2px 18px; border-radius:2px; font-size:1rem; font-weight:500;">
                            SHIFTS
                        </span>
                    </div>
                    <div class="card-body pt-4">
                        @if ($site->shift->count() > 0)
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Shift Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        {{-- <th class="text-center" style="width: 120px;">Actions</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($site->shift as $index => $shift)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $shift->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                            {{-- <td class="text-center">
                                                <!-- Edit Button -->
                                                <a href="{{ url('admin/shift/edit/' . $shift->id . '/' . $employer_id) }}"
                                                    class=" me-2" title="Edit">
                                                    <i class="hover-effect fas fa-pencil text-secondary"></i>
                                                </a>

                                                <!-- Delete Button -->
                                                <button type="button" class="btn btn-link text-danger p-0 m-0 "
                                                    title="Delete" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $shift->id }}">
                                                    <i class="hover-effect-delete fas fa-trash-alt text-secondary"></i>
                                                </button>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $shift->id }}"
                                                    tabindex="-1" aria-labelledby="deleteModalLabel{{ $shift->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteModalLabel{{ $shift->id }}">Confirm
                                                                    Delete</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this shift?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <form
                                                                    action="{{ url('admin/shift/delete/' . $shift->id . '/' . $employer_id) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Yes,
                                                                        Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted mb-0">No shifts available for this site.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var viewImageModal = document.getElementById('viewImageModal');
            viewImageModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var imageUrl = button.getAttribute('data-image');
                var previewImage = document.getElementById('previewImage');
                previewImage.src = imageUrl;
            });
        });
    </script>
@endpush
