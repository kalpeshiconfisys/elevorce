@extends('admin.layouts.app')
@section('title', 'Site List')
@php
    $company = \App\Models\User::find($employer_id);
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
                            @if(isset($sites[0]))
                                <a href="{{ url('admin/company-dashboard/' . trim(base64_encode($sites[0]->employer_id), '=')) }}" class="heading-icon">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                @else
                                <a href="{{ url('admin/company-dashboard/' . trim(base64_encode($employer_id), '=')) }}" class="heading-icon">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            @endif
                            <h2 class="card-title text-dark">Site List</h2>
                        </div>
                        <a href="{{ url('admin/site/create/' . trim(base64_encode($employer_id), '=')) }}"
                            class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width:35px; height:35px;">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped align-middle" style="min-width: 1000px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-dark">#</th>
                                        <th class="text-dark">Site Name</th>
                                        <th class="text-dark">Address</th>
                                        <th class="text-dark">Email</th>
                                        <th class="text-dark">Phone</th>
                                        {{-- <th class="text-dark">Image</th> --}}
                                        <th class="text-dark">Company Owner</th>
                                        <th class="text-dark">Company Name</th>
                                        <th class="text-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sites as $key => $site)
                                        <tr>
                                            <td>{{ $sites->firstItem() + $key }}</td>
                                            <td>{{ $site->name }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($site->address, 50, '...') }}</td>
                                            <td>{{ $site->email }}</td>
                                            <td>{{ $site->phone }}</td>
                                            {{-- <td>
                                                <img src="{{ $site->image }}" alt="Site Image"
                                                    style="width: 40px; height: 40px; object-fit: cover; background: #f5f5f5;"
                                                    class="rounded-circle border">
                                            </td> --}}
                                            <td>{{ $site->employer->name ?? 'N/A' }}</td>
                                            <td>{{ $site->employer->company_name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex">
                                                <a href="{{ url('admin/site-manager-list/' . trim(base64_encode($site->id), '=')  .'?type='.trim(base64_encode(0), '=')) }}"
                                                    class="text-secondary me-2" title="View Site Manager">
                                                    <i class="hover-effect fas fa-user-tie"></i>
                                                </a>
                                                <a href="{{ url('admin/employee-list/' . trim(base64_encode($site->id), '=') .'?type='.trim(base64_encode(0), '=')   ) }}"
                                                    class="text-secondary me-2" title="View Employees">
                                                    <i class="hover-effect fas fa-users"></i>
                                                </a>
                                                <a href="{{ route('site.show', [trim(base64_encode($site->id), '='), trim(base64_encode($employer_id), '=')]) }}"
                                                        class="text-secondary me-2" title="View">
                                                        <i class="hover-effect fa-solid fa-circle-arrow-right"></i>
                                                        </a>
                                                <a href="{{ url('admin/site/edit/' . trim(base64_encode($site->id), '=') . '/' . trim(base64_encode($employer_id), '=')) }}"
                                                    class="text-secondary me-2" title="Edit">
                                                    <i class="hover-effect fas fa-pencil "></i>
                                                </a>

                                                <!-- Delete Button (opens modal) -->
                                                <button type="button" class="btn btn-link text-danger p-0 m-0"
                                                    title="Delete" data-bs-toggle="modal"
                                                    data-bs-target="#deleteSiteModal{{ $site->id }}"
                                                    style="vertical-align: middle;">
                                                    <i class="hover-effect-delete fas fa-trash-alt text-secondary"></i>
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="deleteSiteModal{{ $site->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="deleteSiteModalLabel{{ $site->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteSiteModalLabel{{ $site->id }}">
                                                                    Confirm Delete
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this site?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>

                                                                <!-- Original Form -->
                                                                <form action="{{ route('site.destroy', base64_encode($site->id)) }}"
                                                                    method="POST" style="display:inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="company_id"
                                                                        value="{{ $employer_id }}">
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
                            {{ $sites->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
