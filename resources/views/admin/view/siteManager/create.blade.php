@extends('admin.layouts.sub_app')

@section('title', 'Add Site Manager')


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
        <div class="container form-container col-lg-5 col-md-7 col-sm-10 mx-auto">
            <!-- Breadcrumb -->
            <div class="breadcrumb-bar mb-3">
                <a href="{{ url('admin/site-manager-list/' . $site->id) }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Site Manager List
                </a>
            </div>
            <h2 class="company_title">Add Site Manager</h2>
            <!-- Site Manager Details -->
            <div class="form-section mt-2">
                <form action="{{ url('admin/site-manager/add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Name -->
                        <div class="col-12 mb-3">
                            <label for="name" class="text-dark form-label required">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <!-- Email -->
                        <div class="col-12 mb-3">
                            <label for="email" class="text-dark form-label required">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>

                        <!-- Number -->
                        <div class="col-12 mb-3">
                            <label for="number" class="text-dark form-label required">Mobile Number</label>
                            <input type="text" class="form-control" name="number" id="number" required>
                        </div>

                        <!-- Password -->
                        <div class="col-12 mb-3">
                            <label for="password" class="text-dark form-label required">Password</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>

                        <!-- Image -->
                        <div class="col-12 mb-3">
                            <label for="image" class="text-dark form-label required">Image</label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*"
                                required>
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


                        <!-- Address -->
                        <div class="col-12 mb-3">
                            <label for="address" class="text-dark form-label required">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
                        </div>


                        <!-- Hidden Employer ID -->
                        <input type="hidden" name="employer_id" value="{{ $site->employer_id }}">
                        <input type="hidden" name="site_id" value="{{ $site->id }}">
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 justify-content-center mb-5">
                        <a href="{{ url('admin/site-manager-list/' . $site->id) }}" class="btn btn-cancel">Cancel</a>
                        <button type="submit" class="btn btn-submit">Add Site Manager</button>
                    </div>
                </form>
            </div>
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
