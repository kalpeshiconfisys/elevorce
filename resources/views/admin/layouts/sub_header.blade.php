<div class="container header-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('home') }}" class="logo-link">
                <img src="{{ asset('images/logo/temp_logo.png') }}" alt="Company Logo" class="logo-img">
            </a>
        </div>
        <div class="power-icon-wrapper">
            <a href="{{ url('admin/logout') }}"><i class="fas fa-power-off power-icon"></i></a>
        </div>
    </div>
</div>
