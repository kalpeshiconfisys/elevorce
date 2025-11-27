@extends('admin.layouts.sub_app')

@section('title', 'Add Site')

@section('content')
    <div class="container-fluid">

        <!-- Main Form Content -->
        <div class="container form-container col-lg-4 col-md-8 col-sm-12 mx-auto">
            <div class="breadcrumb-bar mb-3">
                <a href="{{ url('admin/site/' . trim(base64_encode($employer_id), '=')) }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Site List
                </a>
            </div>
            <h2 class="company_title">Add Site</h2>

            <div class="form-section mt-2">
                <div class="form-section-title">User & Site Details</div>
                <div class="row">
                    <form action="{{ route('site.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="company_id" class="form-control" value="{{ $employer_id }}">

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Site Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Site Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                                    required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                    required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                    required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Site Image</label>
                                <input type="file" name="image" class="form-control" required>
                            </div>

                            <!-- Shift Section -->
                            <div class="col-12  mb-3">
                                <label class="text-dark form-label required">Shifts</label>
                                <div id="shift-wrapper" class="">
                                    <!-- Default First Shift Row -->
                                    <div class="row g-2 mb-2 shift-row">
                                        <div class="col-md-4">
                                            <input type="text" name="shifts[0][name]" class="form-control"
                                                placeholder="Shift Name" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" name="shifts[0][start]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" name="shifts[0][end]" class="form-control" required>
                                        </div>
                                        <div class="col-md-2 ">
                                            <button type="button" class="btn btn-success add-shift" style="margin-top: 4px">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Shift Section -->

                        </div>

                        <div class="d-flex gap-2 justify-content-center mb-5">
                            <a href="{{ url('admin/site/' . $employer_id) }}" class="btn btn-cancel">cancel</a>
                            <button type="submit" class="btn btn-submit">Add site</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        let shiftIndex = 1;

        $(document).ready(function() {
            // Add new shift row
            $(document).on("click", ".add-shift", function() {
                let newRow = `
                <div class="row g-2 mb-2 shift-row">
                    <div class="col-md-4">
                        <input type="text" name="shifts[${shiftIndex}][name]" class="form-control"
                            placeholder="Shift Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="time" name="shifts[${shiftIndex}][start]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <input type="time" name="shifts[${shiftIndex}][end]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-shift" style="margin-top: 4px">x</button>
                    </div>
                </div>
            `;
                $("#shift-wrapper").append(newRow);
                shiftIndex++;
            });

            // Remove shift row
            $(document).on("click", ".remove-shift", function() {
                $(this).closest(".shift-row").remove();
            });
        });
    </script>




