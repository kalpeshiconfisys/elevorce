@extends('admin.layouts.app')

@section('title', 'Company List')

@section('content')

    <div class="container">
        <!-- Data Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ url('admin/dashboard/') }}" class="heading-icon">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h2 class="card-title text-dark">Company List</h2>
                        </div>


                        <a href="{{ route('company.create') }}"
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
                                        <th class="text-dark">Name</th>
                                        <th class="text-dark">Email</th>
                                        <th class="text-dark">Number</th>
                                        <th class="text-dark">Company Name</th>
                                        <th class="text-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse ($data as $key => $i)
                                        <tr>
                                            <td>{{ $data->firstItem() + $key }}</td>
                                            <td>{{ $i->name }}</td>
                                            <td>{{ $i->email }}</td>
                                            <td>{{ $i->number }}</td>
                                            <td>{{ $i->company_name }}</td>

                                            <td>
                                                <div class="d-flex">

                                                    <a href="{{ url('admin/site/' . $i->id) }}" class=" me-2"
                                                        title="View Site">
                                                        <i class="hover-effect fas fa-map-marker-alt text-secondary"></i>
                                                    </a>
                                                    <a href="{{ route('company.show', $i->id) }}" class=" me-2"
                                                        title="View">

                                                        <i
                                                            class="hover-effect fa-solid fa-circle-arrow-right text-secondary"></i>
                                                    </a>
                                                    <a href="{{ route('company.edit', $i->id) }}" class=" me-2"
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
                                                                        id="deleteModalLabel{{ $i->id }}">Confirm
                                                                        Delete
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to delete this item?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <form action="{{ route('company.destroy', $i->id) }}"
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
                                                </div>


                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No data found.</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-1">
                            {{ $data->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
