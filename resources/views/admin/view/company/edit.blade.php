@extends('admin.layouts.sub_app')

@section('title', 'Edit Company')

@section('content')
    <div class="container-fluid">
        <!-- Main Form Content -->
        <div class="container form-container col-lg-4 col-md-6 col-sm-10 mx-auto">
            <!-- Breadcrumb -->
            <div class="breadcrumb-bar mb-3">
                <a href="{{ url('admin/company-dashboard/' . trim(base64_encode($employer->id), '=')) }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Company List
                </a>
            </div>

            <!-- Title -->
            <h2 class="company_title">Edit Company</h2>

            <!-- Company Edit Form -->

                <div class="form-section mt-2">
                    <div class="form-section-title">User & Company Details</div>
                    <form action="{{ route('company.update', base64_encode($employer->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required"> Onwer Name</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $employer->name) }}" required >
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required">Email</label>
                                <input type="text" class="form-control" name="email"
                                    value="{{ old('email', $employer->email) }}" required >
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label required">Number</label>
                                <input type="number" class="form-control" name="number"
                                    value="{{ old('number', $employer->number) }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required">Company Name</label>
                                <input type="text" class="form-control" name="company_name"
                                    value="{{ old('company_name', $employer->company_name) }}" required >
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required">Company Address</label>
                                <input type="text" class="form-control" name="company_address"
                                    value="{{ old('company_address', $employer->company_address) }}" required >
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Owner Image</label>
                                <input type="file" class="form-control" name="image"
                                    accept="image/png, image/jpeg, image/jpg, image/gif">
                                @if ($employer->image)
                                    <img src="{{ $employer->image }}" alt="image" width="100" class="mt-2">
                                @endif
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Company Logo</label>
                                <input type="file" class="form-control" name="company_logo"
                                    accept="image/png, image/jpeg, image/jpg, image/gif">
                                @if ($employer->company_logo)
                                    <img src="{{ $employer->company_logo }}" alt="logo" width="100" class="mt-2">
                                @endif
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Company Cover Image</label>
                                <input type="file" class="form-control " name="company_cover_image"
                                    accept="image/png, image/jpeg, image/jpg, image/gif">
                                @if ($employer->company_cover_image)
                                    <img src="{{ $employer->company_cover_image }}" alt="cover image" width="150"
                                        class="mt-2">
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mb-5">
                    <a href="{{ url('admin/company-dashboard/'.base64_encode($employer->id)) }}" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-submit">Update Company</button>
                </div>
                     </form>
                </div>
        </div>
        <!-- Footer Bar -->
    </div>
@endsection
