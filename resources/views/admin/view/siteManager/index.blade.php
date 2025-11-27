@extends('admin.layouts.app')

@section('title', 'Site Manager List')
@php
    $company = \App\Models\User::find($allSites[0]->employer_id);
    $data = [
        'company' => $company,
    ];
@endphp

@section('company', $data['company'])
@section('content')
    <div class="container">
        {{-- Page Title & Breadcrumb  --}}
        <div class="row">
            <div class="col-12 ">
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
                            <h2 class="card-title text-dark">Site Manager List</h2>
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
                                    // Get current site ID from URL or default to 0
                                    $currentSiteId = request()->segment(count(request()->segments())) ?? 0;

                                    // Encode the first site manager's site_id if exists, otherwise use currentSiteId
                                $firstSiteId =
                                    !empty($siteManagers) && isset($siteManagers[0]->site_id)
                                        ? trim(base64_encode($siteManagers[0]->site_id), '=')
                                            : $currentSiteId;
                                @endphp
                                <li>
                                    <a href="{{ url('admin/site-manager-list/'.trim(base64_encode(0), '=').'') }}?hide_site={{ $firstSiteId }}"
                                        class="site-item @if ($employer_id == 0) active @endif">
                                        <i class="fa-solid fa-map-marker-alt me-2 text-secondary"></i> All
                                    </a>
                                </li>
                                @foreach ($allSites as $site)
                                    <li>
                                        <a href="{{ url('admin/site-manager-list/' . trim(base64_encode($site->id), '=')) }}"
                                            class="site-item @if ($site->id == $employer_id) active @endif">
                                            <i class="fa-solid fa-map-marker-alt me-2 text-secondary"></i>
                                            {{ $site->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                    {{-- <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="card-title text-dark"></h2>
                        <a href="{{ url('admin/site-manager/create/' . $employer_id) }}"
                            class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width:35px; height:35px;">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div> --}}

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped align-middle" style="min-width: 1000px;">
                                <thead class="table-light ">
                                    <tr>
                                        <th class="text-dark">#</th>
                                        <th class="text-dark">Site</th>
                                        <th class="text-dark">Name</th>
                                        <th class="text-dark">Email</th>
                                        <th class="text-dark">Number</th>
                                        <th class="text-dark">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siteManagers as $key => $emp)
                                        <tr>
                                            <td>{{ $siteManagers->firstItem() + $key }}</td>
                                            <td>{{ Str::limit($emp->site->name ?? 'N/A', 20) }}</td>
                                            <td>{{ $emp->name }}</td>
                                            <td>{{ $emp->email }}</td>
                                            {{-- <td>
                                            <img src="{{ asset($emp->image) }}" alt="Logo"
                                                style="width: 80px; height: 80px; object-fit: cover; background: #f5f5f5;"
                                                class="rounded-circle border shadow">
                                        </td> --}}
                                            <td>{{ $emp->number }}</td>
                                            <td style="border:none;">
                                                @if ($emp->status == 1)
                                                    <span class="badge rounded-pill bg-success px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- <td>
                                            @if ($emp->status == 1)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger ">Inactive</span>
                                            @endif
                                        </td> --}}
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ url('admin/site-manager/show/' . $emp->id . '/' . $employer_id) }}"
                                                        class=" me-2" title="View">
                                                        <i
                                                            class="hover-effect fa-solid fa-circle-arrow-right text-secondary"></i>
                                                    </a>
                                                    <a href="{{ url('admin/site-manager/edit/' . $emp->id . '/' . $employer_id) }}"
                                                        class=" me-2" title="Edit">
                                                        <i class="hover-effect fas fa-pencil text-secondary"></i>
                                                    </a>
                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-link text-danger p-0 m-0 "
                                                        title="Delete" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $emp->id }}">
                                                        <i class="hover-effect-delete fas fa-trash-alt text-secondary"></i>
                                                    </button>

                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="deleteModal{{ $emp->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="deleteModalLabel{{ $emp->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="deleteModalLabel{{ $emp->id }}">Confirm
                                                                        Delete</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to delete this?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <form
                                                                        action="{{ url('admin/site-manager/delete/' . $emp->id . '/' . $emp->site_id) }}"
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
                                            <td colspan="7" class="text-center">No Site Manager found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-1">
                            {{ $siteManagers->appends(['type' => $redirectType])->onEachSide(1)->links('pagination::bootstrap-4') }}
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
