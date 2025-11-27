@extends('admin.layouts.app')

@section('title', 'Company Details')

@section('content')
    <div class="container-fluid">
        

        <div class="row">
            <div class="col-8 m-auto">
                <div style="border-bottom:1px solid #e0e2e6;">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('company.index') }}" class="d-inline-flex align-items-center text-decoration-none">
                            <span class="breadcrumb-icon me-2">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            Company Details
                        </a>
                    </div>
                </div>
                <div class="employee-section-header d-flex align-items-center justify-content-between"
                    style="border-bottom:1px solid #e0e2e6;">
                    <div>
                        <h3>{{ $company->name }}</h3>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('company.edit', $company->id) }}" class="icon-btn icon-btn-edit me-2"
                            title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        {{-- <form action="{{ route('company.destroy', $company->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-btn icon-btn-delete" title="Delete">
                                <i class="fa fa-trash-alt" aria-hidden="true"></i>
                            </button>
                        </form> --}}
                        <!-- Delete Trigger Button -->
                        <button type="button" class="icon-btn icon-btn-delete" title="Delete" data-bs-toggle="modal"
                            data-bs-target="#deleteCompanyModal{{ $company->id }}">
                            <i class="fa fa-trash-alt" aria-hidden="true"></i>
                        </button>

                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="deleteCompanyModal{{ $company->id }}" tabindex="-1"
                            aria-labelledby="deleteCompanyModalLabel{{ $company->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteCompanyModalLabel{{ $company->id }}">Confirm
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
                                        <form action="{{ route('company.destroy', $company->id) }}" method="POST"
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
                                    <td class="fw-bold" style="width:180px; border:none;">Title</td>
                                    <td style="border:none;">{{ $company->company_name }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Company Address</td>
                                    <td style="border:none;">{{ $company->company_address }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Email</td>
                                    <td style="border:none;">
                                        <a href="{{ $company->email }}" target="_blank">{{ $company->email }}</a>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Published on</td>
                                    <td style="border:none;">
                                        {{ \Carbon\Carbon::parse($company->created_at)->format('d/m/Y') }}</td>
                                </tr>
                                {{-- <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Cover Image</td>
                                    <td style="border:none;">
                                        <img src="{{ $company->image }}" alt="Cover"
                                            style="width: 80px; height: 80px; object-fit: cover; background: #f5f5f5;">
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Cover logo</td>
                                    <td style="border:none;">
                                        <img src="{{ $company->company_logo }}" alt="Cover"
                                            style="width: 80px; height: 80px; object-fit: cover; background: #f5f5f5;">
                                    </td>
                                </tr>

                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Cover Image</td>
                                    <td style="border:none;">
                                        <img src="{{ $company->company_cover_image }}" alt="Cover"
                                            style="width: 80px; height: 80px; object-fit: cover; background: #f5f5f5;">
                                    </td>
                                </tr> --}}
                                {{-- Cover Image --}}
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Cover Image</td>
                                    <td style="border:none;">
                                        @if ($company->image)
                                            {{ basename($company->image) }}
                                            <button class="image_view ms-2" data-bs-toggle="modal"
                                                data-bs-target="#viewImageModal" data-image="{{ asset($company->image) }}">
                                                View
                                            </button>
                                        @else
                                            <span class="text-muted">No image available</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Cover Logo --}}
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Cover Logo</td>
                                    <td style="border:none;">
                                        @if ($company->company_logo)
                                            {{ basename($company->company_logo) }}
                                            <button class="image_view ms-2" data-bs-toggle="modal"
                                                data-bs-target="#viewImageModal"
                                                data-image="{{ asset($company->company_logo) }}">
                                                View
                                            </button>
                                        @else
                                            <span class="text-muted">No logo available</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Company Cover Image --}}
                                <tr class="border-bottom">
                                    <td class="fw-bold" style="border:none;">Cover Image</td>
                                    <td style="border:none;">
                                        @if ($company->company_cover_image)
                                            {{ basename($company->company_cover_image) }}
                                            <button class="image_view ms-2" data-bs-toggle="modal"
                                                data-bs-target="#viewImageModal"
                                                data-image="{{ asset($company->company_cover_image) }}">
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
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="" id="previewImage" class="img-fluid rounded shadow">
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
