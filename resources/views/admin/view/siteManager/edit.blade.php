@extends('admin.layouts.sub_app')

@section('title', 'Edit Site Manager')

@section('content')

    <div class="container-fluid">
        <!-- Main Form Content -->
        <div class="container form-container col-lg-5 col-md-7 col-sm-10 mx-auto">
            <!-- Breadcrumb -->
            <div class="breadcrumb-bar mb-3">
                <a href="{{ url('admin/site-manager-list/' . $employer_id) }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Site Manager List
                </a>
            </div>
            <h2 class="company_title">Edit Site Manager</h2>
            <!-- Site Manager Details -->
            <div class="form-section mt-2">
                <form action="{{ url('admin/site-manager/update/' . $siteManager->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Name -->
                        <div class="col-12 mb-3">
                            <label for="name" class="text-dark form-label required">Name</label>
                            <input type="text" class="form-control" name="name" id="name"
                                value="{{ old('name', $siteManager->name) }}" required>
                        </div>

                        <!-- Email -->
                        <div class="col-12 mb-3">
                            <label for="email" class="text-dark form-label required">Email</label>
                            <input type="email" class="form-control" name="email" id="email"
                                value="{{ old('email', $siteManager->email) }}" required>
                        </div>

                        <!-- Number -->
                        <div class="col-12 mb-3">
                            <label for="number" class="text-dark form-label required">Mobile Number</label>
                            <input type="text" class="form-control" name="number" id="number"
                                value="{{ old('number', $siteManager->number) }}" required>
                        </div>




                        <!-- Profile Image -->
                        <div class="col-12 mb-3">
                            <label for="image" class="text-dark form-label"> Image</label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            @if ($siteManager->image)
                                <div class="mt-2">
                                    <img src="{{ $siteManager->image }}" alt="Profile Image" width="80" class="rounded">
                                </div>
                            @endif
                        </div>

                        <!-- Address -->
                        <div class="col-12 mb-3">
                            <label for="address" class="text-dark form-label required">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $siteManager->address) }}</textarea>
                        </div>




                        <!-- Hidden Employer ID -->
                        <input type="hidden" name="employer_id" value="{{ $employer_id }}">
                        <input type="hidden" name="site_id" value="{{ $siteManager->site_id }}">
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 justify-content-center mb-5">
                        <a href="{{ url('admin/site-manager-list/' . $employer_id) }}" class="btn btn-cancel">Cancel</a>
                        <button type="submit" class="btn btn-submit">Update Site Manager</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
