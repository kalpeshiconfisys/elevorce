@extends('admin.layouts.app')

@section('title', 'Shift List')

@section('content')
    <div class="container">
        <!-- Data Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="card-title text-dark">Shift List</h2>
                        <a href="{{ url('admin/shift/create/'.$employer_id) }}"
                            class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width:35px; height:35px;">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped align-middle" style="min-width: 1000px;">
                                <thead class="table-light ">
                                    <tr>
                                        <th class="text-dark">#</th>
                                        <th class="text-dark">Shift Name</th>
                                        <th class="text-dark">Start Time</th>
                                        <th class="text-dark">End Time</th>
                                        <th class="text-dark">Site</th>
                                        <th class="text-dark">Company</th>
                                        <th class="text-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($shifts as $key => $i)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $i->name }}</td>
                                            <td>{{ $i->start_time }}</td>
                                            <td>{{ $i->end_time }}</td>
                                            <td>{{ $i->site_name ?? '-' }}</td>
                                            <td>{{ $i->company_name }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ url('admin/shift/show/' . $i->id .'/'.$employer_id) }}" class=" me-2"
                                                        title="View">
                                                        <i class="hover-effect fa-solid fa-circle-arrow-right text-secondary"></i>
                                                    </a>
                                                    <a href="{{ url('admin/shift/edit/' . $i->id .'/'.$employer_id) }}" class=" me-2"
                                                        title="Edit">
                                                        <i class="hover-effect fas fa-pencil text-secondary"></i>
                                                    </a>
                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-link text-danger p-0 m-0 "
                                                        title="Delete" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $i->id }}">
                                                        <i class="hover-effect-delete fas fa-trash-alt text-secondary"></i>
                                                    </button>

                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="deleteModal{{ $i->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="deleteModalLabel{{ $i->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="deleteModalLabel{{ $i->id }}">Confirm Delete</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to delete this shift?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <form action="{{ url('admin/shift/delete/' . $i->id . '/' . $employer_id) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No shifts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
