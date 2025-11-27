@extends('admin.layouts.sub_app')

@section('title', 'Add Leave Type')

@section('content')

    <div class="container-fluid">
        <!-- Main Form Content -->
        <div class="container form-container col-lg-4 col-md-6 col-sm-10 mx-auto">
            <!-- Breadcrumb -->
            <div class="breadcrumb-bar mb-3">
                 <a  href="{{ url('admin/leave-type/list/' . trim(base64_encode($employer_id), '=')) }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Leave Type List
                </a>
            </div>

            <h2 class="company_title">Add Leave Type</h2>


            <div class="form-section mt-2">
                <form action="{{ url('admin/leave-type/add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="name" class="text-dark form-label required">Name</label>
                            <input type="text" class="form-control" name="leave_name" id="name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email" class="form-label required">Count</label>
                            <input type="number" class="form-control" name="count" id="email" required>
                            <input type="hidden" class="form-control" name="employer_id" value="{{ $employer_id}}" id="email" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mb-5">
                        <a href="{{ url('admin/leave-type/list/' . trim(base64_encode($employer_id), '=')) }}" type="button" class="btn btn-cancel">cancel</a>
                        <button type="submit" class="btn btn-submit">Add Leave Type</button>
                    </div>
                </form>
            </div>
            <!-- Action Buttons -->

        </div>



    </div>

@endsection
