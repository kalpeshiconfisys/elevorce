<div class="header">


    <!-- Collapsible Search Bar -->
    {{-- <div class="search-bar-collapsible">
        <span class="material-symbols-outlined">search</span>
        <input type="text" class="search-input" placeholder="Search People">
    </div> --}}

    <div class="d-flex align-items-center justify-content-between w-100">

        <div class="d-flex align-items-center" style="gap:20px;">
            <!-- Sidebar Toggle Button -->
            <button class="sidebar-toggle me-2" id="sidebarToggle">
                {{-- <span>&#9776;</span> --}}
                <span></span>
                <span></span>
                <span></span>
            </button>
            @if (isset($company) && !empty($company))
                <div class="logo-box d-flex align-items-center justify-content-center rounded-3 border bg-light"
                    style="width:50px; height:50px; overflow:hidden;">
                    <img src="{{ $company->company_logo ?? null }}" alt="Logo" class="img-fluid"
                        style="max-height:100%; max-width:100%; object-fit:contain;" />
                </div>
                <h4 class="m-0 fw-semibold text-dark">{{ $company['company_name'] ?? null }}</h4>
            @else
                <div class="logo-box d-flex align-items-center justify-content-center rounded-3 border bg-light"
                    style="width:50px; height:50px; overflow:hidden;">
                    <img src="{{ asset('images/logo/temp_logo.png') }}" alt="Default Logo" class="img-fluid"
                        style="max-height:100%; max-width:100%; object-fit:contain;" />
                </div>
                <h4 class="m-0 fw-semibold text-dark">Elevorce</h4>
            @endif
        </div>

        <!-- Icons -->
        <div class="icon-group">

            <!-- Profile Avatar -->
            <div class="avatar-wrapper">
                <div class="avatar" id="avatarBtn">
                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="User">
                </div>
                <div class="dropdown" id="dropdownMenu">
                    <a href="{{ url('admin/logout') }}"><span class="material-symbols-outlined">logout</span> Logout</a>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="search-bar-dropdown">
    <div class="d-flex align-center">

        <span class="material-symbols-outlined">search</span>
        <input type="text" placeholder="Search People">
    </div>
</div>
<script>
    $(function() {
        // Sidebar open
        $('#sidebarToggle').on('click', function() {
            $('.sidebar').addClass('open');
            $('body').addClass('sidebar-open');
            $('.sidebar-overlay').show();
        });

        // Sidebar close (icon)
        $('.sidebar-close').on('click', function() {
            $('.sidebar').removeClass('open');
            $('body').removeClass('sidebar-open');
            $('.sidebar-overlay').hide();
        });

        // Sidebar close (overlay)
        $('.sidebar-overlay').on('click', function() {
            $('.sidebar').removeClass('open');
            $('body').removeClass('sidebar-open');
            $(this).hide();
        });

        // Show/hide search input    on icon click (mobile)
        $('.search-toggle').on('click', function(e) {
            if ($(window).width() <= 900) {
                e.stopPropagation();
                $('.search-bar-dropdown').slideToggle(200, function() {
                    if ($(this).is(':visible')) {
                        $(this).find('input').focus();
                    }
                });
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-bar-dropdown, .search-toggle').length) {
                $('.search-bar-dropdown').slideUp(200);
            }
        });
    });

    const avatarBtn = document.getElementById('avatarBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');

    avatarBtn.addEventListener('click', () => {
        dropdownMenu.style.display = dropdownMenu.style.display === 'flex' ? 'none' : 'flex';
    });

    // Optional: Close dropdown if clicked outside
    document.addEventListener('click', (e) => {
        if (!avatarBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.style.display = 'none';
        }
    });


</script>
{{-- <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="search_bar dropdown">
                                <span class="search_icon p-3 c-pointer" data-toggle="dropdown">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <div class="dropdown-menu p-0 m-0">
                                    <form>
                                        <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <i class="mdi mdi-bell"></i>
                                    <div class="pulse-css"></div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="list-unstyled">
                                        <li class="media dropdown-item">
                                            <span class="success"><i class="ti-user"></i></span>
                                            <div class="media-body">
                                                <a href="#">
                                                    <p><strong>Martin</strong> has added a <strong>customer</strong> Successfully
                                                    </p>
                                                </a>
                                            </div>
                                            <span class="notify-time">3:20 am</span>
                                        </li>
                                        <li class="media dropdown-item">
                                            <span class="primary"><i class="ti-shopping-cart"></i></span>
                                            <div class="media-body">
                                                <a href="#">
                                                    <p><strong>Jennifer</strong> purchased Light Dashboard 2.0.</p>
                                                </a>
                                            </div>
                                            <span class="notify-time">3:20 am</span>
                                        </li>
                                        <li class="media dropdown-item">
                                            <span class="danger"><i class="ti-bookmark"></i></span>
                                            <div class="media-body">
                                                <a href="#">
                                                    <p><strong>Robin</strong> marked a <strong>ticket</strong> as unsolved.
                                                    </p>
                                                </a>
                                            </div>
                                            <span class="notify-time">3:20 am</span>
                                        </li>
                                        <li class="media dropdown-item">
                                            <span class="primary"><i class="ti-heart"></i></span>
                                            <div class="media-body">
                                                <a href="#">
                                                    <p><strong>David</strong> purchased Light Dashboard 1.0.</p>
                                                </a>
                                            </div>
                                            <span class="notify-time">3:20 am</span>
                                        </li>
                                        <li class="media dropdown-item">
                                            <span class="success"><i class="ti-image"></i></span>
                                            <div class="media-body">
                                                <a href="#">
                                                    <p><strong> James.</strong> has added a<strong>customer</strong> Successfully
                                                    </p>
                                                </a>
                                            </div>
                                            <span class="notify-time">3:20 am</span>
                                        </li>
                                    </ul>
                                    <a class="all-notification" href="#">See all notifications <i
                                            class="ti-arrow-right"></i></a>
                                </div>
                            </li>
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <i class="mdi mdi-account"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ asset('theme/app-profile.html') }}') }}" class="dropdown-item">
                                        <i class="icon-user"></i>

                                        <span class="ml-2">Profile </span>
                                    </a>
                                    <a href="{{ asset('theme/email-inbox.html') }}" class="dropdown-item">
                                        <i class="icon-envelope-open"></i>
                                        <span class="ml-2">Inbox </span>
                                    </a>
                                    <a href="{{ url('admin/logout') }}" class="dropdown-item">
                                        <i class="icon-key"></i>
                                        <span class="ml-2">Logout </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div> --}}
