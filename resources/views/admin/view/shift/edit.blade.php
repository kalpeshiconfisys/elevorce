@extends('admin.layouts.sub_app')

@section('title', 'Edit Shift')

@section('content')

    <div class="container-fluid">
        <!-- Main Form Content -->
        <div class="container form-container col-lg-4 col-md-6 col-sm-10 mx-auto">
            <!-- Breadcrumb -->
            <div class="breadcrumb-bar mb-3">
                <a href="{{ url('admin/shift/list/' . $employer_id) }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Shift List
                </a>
            </div>

            <h2 class="company_title">Edit Shift</h2>

            <!-- Shift Details -->
            <div class="form-section mt-2">
                <form action="{{ url('admin/shift/update/' . $shift->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Shift Name -->
                        <div class="col-12 mb-3">
                            <label for="name" class="text-dark form-label required">Shift Name</label>
                            <input type="text" class="form-control" name="name" id="name"
                                value="{{ old('name', $shift->name) }}" required>
                        </div>

                        <!-- Start Time -->
                        <div class="col-12 mb-3">
                            <label for="start_time" class="text-dark form-label required">Start Time</label>
                            <input type="time" class="form-control" name="start_time" id="start_time"
                                value="{{ old('start_time', $shift->start_time) }}" required>
                        </div>

                        <!-- End Time -->
                        <div class="col-12 mb-3">
                            <label for="end_time" class="text-dark form-label required">End Time</label>
                            <input type="time" class="form-control" name="end_time" id="end_time"
                                value="{{ old('end_time', $shift->end_time) }}" required>
                        </div>

                        <!-- Site -->
                        <div class="col-12 mb-3">
                            <label for="site_id" class="text-dark form-label required">Site</label>
                            <select name="site_id" id="site_id" class="form-control" required>
                                <option value="">-- Select Site --</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}"
                                        {{ old('site_id', $shift->site_id) == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hidden Employer ID -->
                        <input type="hidden" name="employer_id" value="{{ $employer_id }}">
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 justify-content-center mb-5">
                        <a href="{{ url('admin/shift/list/' . $employer_id) }}" class="btn btn-cancel">Cancel</a>
                        <button type="submit" class="btn btn-submit">Update Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
