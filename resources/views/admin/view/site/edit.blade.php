@extends('admin.layouts.sub_app')

@section('title', 'Edit Site')

@section('content')
    <div class="container-fluid">
        <div class="container form-container col-lg-4 col-md-6 col-sm-10 mx-auto">
            {{-- Breadcrumb --}}
            <div class="breadcrumb-bar mb-3">
                <a href="{{ url('admin/site/' . trim(base64_encode($employer_id), '=')  ) }}"
                    class="d-inline-flex align-items-center text-decoration-none">
                    <span class="breadcrumb-icon me-2">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    Site List
                </a>
            </div>

            <h2 class="company_title">Edit Site</h2>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <div class="form-section mt-2">
                <div class="form-section-title">User & Site Details</div>
                <div class="row">
                    <form action="{{ route('site.update', base64_encode($site->id)) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <input type="hidden" name="company_id" value="{{ $employer_id }}">

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Site Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $site->name) }}" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Site Address</label>
                                <input type="text" name="address" class="form-control"
                                    value="{{ old('address', $site->address) }}">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $site->phone) }}" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label required">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $site->email) }}" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-dark form-label">Site Image</label><br>
                                @if ($site->image)
                                    <img src="{{ $site->image }}" width="100" class="mb-2">
                                @endif
                                <input type="file" name="image" class="form-control">
                            </div>

                            {{-- 🔹 Shift Section --}}
                            {{-- 🔹 Shift Section --}}
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="text-dark form-label required mb-0">Shifts</label>
                                    @if ($site->shift->count() > 0)
                                        <button type="button" class="btn btn-success  add-shift  extra-btn" style="margin-top: 4px">+</button>
                                    @endif
                                </div>

                                <div id="shift-wrapper">
                                    {{-- Loop Existing Shifts --}}
                                    @forelse ($site->shift as $index => $shift)
                                        <div class="row g-2 mb-2 shift-row">
                                            <input type="hidden" name="shifts[{{ $index }}][id]"
                                                value="{{ $shift->id }}">
                                            <div class="col-md-4">
                                                <input type="text" name="shifts[{{ $index }}][name]"
                                                    class="form-control" value="{{ $shift->name }}"
                                                    placeholder="Shift Name" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="time" name="shifts[{{ $index }}][start]"
                                                    class="form-control" value="{{ $shift->start_time }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="time" name="shifts[{{ $index }}][end]"
                                                    class="form-control" value="{{ $shift->end_time }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger  remove-shift" style="margin-top: 4px">x</button>
                                            </div>
                                        </div>
                                    @empty
                                        {{-- If no shift exist, show default row with + button --}}
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
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-success  add-shift" style="margin-top: 4px">+</button>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- 🔹 Show Add Button ONLY if shifts exist --}}

                            </div>

                            {{-- End Shift Section --}}
                        </div>

                        <div class="d-flex gap-2 justify-content-center mb-5">
                            <a href="{{ url('admin/site/' . base64_encode($employer_id)) }}" class="btn btn-cancel">Cancel</a>
                            <button type="submit" class="btn btn-submit">Update Site</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- 🔹 Script --}}

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    let shiftIndex = {{ $site->shift->count() ?? 1 }};

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
                    <div class="col-md-2 ">
                        <button type="button" class="btn btn-danger  remove-shift" style="margin-top: 4px">x</button>
                    </div>
                </div>
            `;
            $("#shift-wrapper").append(newRow);
            shiftIndex++;
        });


    $(document).on("click", ".remove-shift", function() {
        $(this).closest(".shift-row").remove();


        if ($("#shift-wrapper .shift-row").length === 0) {
            $('.extra-btn').hide();
            let defaultRow = `
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
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success add-shift" style="margin-top: 4px">+</button>
                    </div>
                </div>
            `;
            $("#shift-wrapper").append(defaultRow);
            shiftIndex = 1;
        }
    });

    });
</script>
