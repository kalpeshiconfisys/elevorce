@extends('admin.layouts.app')

@section('title', 'Leave Type Details')

@section('content')
    <div class="container-fluid">


        <div class="row">
            <div class="col-8 m-auto">
                <div style="border-bottom:1px solid #e0e2e6;">
                    <div class="breadcrumb-bar">
                        <a href="{{ url('admin/leave-type/list/' . trim(base64_encode($employer_id), '=')) }}" class="d-inline-flex align-items-center text-decoration-none">
                            <span class="breadcrumb-icon me-2">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            Leave Type Details
                        </a>
                    </div>
                </div>
                <div class="employee-section-header d-flex align-items-center justify-content-between"
                    style="border-bottom:1px solid #e0e2e6;">
                    <div>
                        <h3>{{ $leaveType->leave_name }}</h3>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ url('admin/leave-type/edit/' . trim(base64_encode($leaveType->id), '=') . '/' . trim(base64_encode($employer_id), '=')) }}" class="icon-btn icon-btn-edit me-2"
                            title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <!-- Delete Trigger Button -->
                        <button type="button" class="icon-btn icon-btn-delete" title="Delete" data-bs-toggle="modal"
                            data-bs-target="#deleteCompanyModal{{ $leaveType->id }}">
                            <i class="fa fa-trash-alt" aria-hidden="true"></i>
                        </button>

                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="deleteCompanyModal{{ $leaveType->id }}" tabindex="-1"
                            aria-labelledby="deleteCompanyModalLabel{{ $leaveType->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteCompanyModalLabel{{ $leaveType->id }}">Confirm
                                            Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this company?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ url('admin/leave-type/delete/' . trim(base64_encode($leaveType->id), '=') . '/' . trim(base64_encode($employer_id), '=')) }}" method="POST"
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
                            style="background:#868e96; color:#fff; padding:2px 18px; border-radius:2px; font-size:1rem; font-weight:500; top:18px; left:20px;">COMPANY
                            DETAILS</span>
                    </div>
                    <div class="card-body pt-4">
                        <table class="table mb-0" style="border: none;">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="width:180px; border:none;">Name</td>
                                    <td style="border:none;">{{ $leaveType->leave_name }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;"> Total Count</td>
                                    <td style="border:none;">{{ $leaveType->count }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Time Range</td>
                                    <td style="border:none;">
                                        <a href="#" target="_blank">{{ $leaveType->time_range }}</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                var button = event.relatedTarget; // Button that triggered the modal
                var imageUrl = button.getAttribute('data-image'); // Extract image URL
                var previewImage = document.getElementById('previewImage');
                previewImage.src = imageUrl; // Set modal image source
            });
        });
    </script>
@endpush
