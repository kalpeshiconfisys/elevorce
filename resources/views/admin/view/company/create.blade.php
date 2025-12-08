@extends('admin.layouts.sub_app')

@section('title', 'Add Company')


<style>
    .select2-container .select2-selection--single {
        height: 50px !important;
        /* adjust height */
        display: flex;
        align-items: center;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 5px 10px;


    }

    .select2-container .select2-selection__rendered {
        font-size: 16px;
        line-height: 48px;
        /* align text vertically */

    }


    .select2-container .select2-selection__arrow {
        height: 43px !important;
    }
</style>

@section('content')

    <div class="container-fluid">
        <!-- Main Form Content -->
        <div class="container form-container col-lg-4 col-md-6 col-sm-10 mx-auto">
            <!-- Breadcrumb -->
            <div class="breadcrumb-bar mb-3">
                <a href="{{ url('admin/dashboard') }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Company List
                </a>
            </div>

            {{-- <h2 class="mb-4">Add company</h2> --}}

            {{-- <div class="company_title"> --}}
            <h2 class="company_title">Add Company</h2>
            {{-- </div> --}}

            <!-- Company Announcement Details -->

            <div class="form-section mt-2">
                <div class="form-section-title">User & Company Details</div>
                <form action="{{ route('company.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="name" class="text-dark form-label required"> Owner Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email" class="form-label required">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password" class="form-label required">Password</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="number" class="form-label required">Number</label>
                            <input type="number" class="form-control" name="number" id="number" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="companyName" class="form-label required">Company Name</label>
                            <input type="text" class="form-control" name="company_name" id="companyName" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="companyAddress" class="form-label required">Company Address</label>
                            <input type="text" class="form-control" name="company_address" id="companyAddress" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="imageUpload" class="form-label required">Owner Image</label>
                            <input type="file" class="form-control" name="image" id="imageUpload" accept="image/jpeg, image/png, image/jpg, image/gif" required>
                        </div>
                         <div class="col-12 mb-3">
                            <label for="country_code" class="form-label required text-dark">Country</label>
                            <select name="country_code" id="country_code" class="form-select select2" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country['id'] }}">
                                        {{ $country['name'] }} ({{ $country['phone_code'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="companyLogo" class="form-label required">Company Logo</label>
                            <input type="file" class="form-control" name="company_logo" id="companyLogo" accept="image/jpeg, image/png, image/jpg, image/gif" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="companyCoverImage" class="form-label required">Company Cover Image</label>
                            <input type="file" class="form-control" name="company_cover_image" id="companyCoverImage" accept="image/jpeg, image/png, image/jpg, image/gif" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mb-5">
                        <a href="{{ url('admin/dashboard') }}" type="button" class="btn btn-cancel">cancel</a>
                        <button type="submit" class="btn btn-submit">Add company</button>
                    </div>
                </form>
            </div>
            <!-- Action Buttons -->

        </div>



    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            $('#country_code').select2({
                placeholder: "Select a country",
                allowClear: true
            });
        });
    </script>
@endpush
