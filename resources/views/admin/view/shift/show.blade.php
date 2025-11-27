@extends('admin.layouts.app')

@section('title', 'Shift Details')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-8 m-auto">
                <div style="border-bottom:1px solid #e0e2e6;">
                    <div class="breadcrumb-bar">
                        <a href="{{ url('admin/shift/list/' . $employer_id) }}"
                            class="d-inline-flex align-items-center text-decoration-none">
                            <span class="breadcrumb-icon me-2">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            Shift Details
                        </a>
                    </div>
                </div>
                <div class="employee-section-header d-flex align-items-center justify-content-between"
                    style="border-bottom:1px solid #e0e2e6;">
                    <div>
                        <h3>{{ $shift->name }}</h3>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ url('admin/shift/edit/' . $shift->id . '/' . $employer_id) }}"
                            class="icon-btn icon-btn-edit me-2" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <!-- Delete Trigger -->
                        <button type="button" class="icon-btn icon-btn-delete" title="Delete" data-bs-toggle="modal"
                            data-bs-target="#deleteShiftModal{{ $shift->id }}">
                            <i class="fa fa-trash-alt" aria-hidden="true"></i>
                        </button>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteShiftModal{{ $shift->id }}" tabindex="-1"
                            aria-labelledby="deleteShiftModalLabel{{ $shift->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this shift?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ url('admin/shift/delete/' . $shift->id . '/' . $employer_id) }}"
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
            </div>
        </div>

        <!-- Details Card -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-7 col-md-9">
                <div class="card border-1 position-relative" style="border-color:#d3d6db;">
                    <div class="card-header position-absolute" style="border-bottom:none; top:-20px;">
                        <span
                            style="background:#868e96; color:#fff; padding:2px 18px; border-radius:2px;
                        font-size:1rem; font-weight:500;">
                            SHIFT DETAILS
                        </span>
                    </div>
                    <div class="card-body pt-4">
                        <table class="table mb-0">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="width:180px;">Name</td>
                                    <td>{{ $shift->name }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Employer</td>
                                    <td>{{ $shift->employer_id }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Site</td>
                                    <td>{{ $shift->site_id }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Start Time</td>
                                    <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">End Time</td>
                                    <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Status</td>
                                    <td>{{ ucfirst($shift->status) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created At</td>
                                    <td>{{ $shift->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
