<div class="sidebar">
    <div class="sidenav__logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo/temp_logo.png') }}" alt="Logo" class="text-center pr-15 pl-15" width="full" />
        </a>
        <span class="sidebar-close" id="sidebarClose"
            style="display: none; font-size: 26px; cursor: pointer;">&times;</span>
    </div>

    <div class="sidenav-menu">
        {{-- <ul>
            <li>
            <a href="{{url('admin/welcome')}}"
            class="sidenav-menu__link {{ request()->routeIs('welcome') ? 'active' : '' }}">
                <div class="d-flex align-items-center">
                    <i class="fa-regular fa-hand"></i>
                    Welcome
                </div>
            </a>
        </li>
        </ul> --}}
    </div>
    <div class="sidenav-menu">
        <h4 class="h5">DashBoard</h4>
        <ul>
            <li>
                <a href="{{ url('admin/dashboard') }}"
                    class="sidenav-menu__link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-gauge"></i>
                        Dashboard
                    </div>
                </a>
            </li>
            {{-- <li>
                <a href="{{ url('admin/profile') }}"
                    class="sidenav-menu__link {{ request()->routeIs('admin/profile') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <i class="fa-regular fa-user"></i>
                        My Profile
                    </div>
                </a>
            </li> --}}
        </ul>
    </div>
    <div class="sidenav-menu">
        <h4 class="h5">My Company</h4>
        <ul>
            <li>
                <a id="companyMenu"
                    class="sidenav-menu__link company-link  {{ request()->routeIs('company.index') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <i class="fa-regular fa-building"></i>
                        Company List
                    </div>
                </a>
            </li>
        </ul>

    </div>
</div>


<!-- Submenu Flyout for Company -->
<div id="submenuCompany" class="submenu-panel" style="display:none;">
    <div class="submenu-header">
        <span class="back-arrow" id="closeCompany">&lsaquo;</span>
        Company
    </div>
    <ul>
        <li><a href="{{route('company.create')}}" class="sidenav-menu__link">Add company</a></li>
        @php
            $company = \App\Models\User::all();
        @endphp
        @foreach ($company as $item)
            <li>
                <a href="{{ url('admin/company-dashboard/' .  trim(base64_encode($item->id), '=')) }}" class="sidenav-menu__link">
                    {{ $item->name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    function openSubmenu(id) {
        $('.submenu-panel').removeClass('open close').hide();
        $(id).show().addClass('open');
    }

    function closeSubmenu(id) {
        $(id).removeClass('open').addClass('close');
        setTimeout(function() {
            $(id).hide().removeClass('close');
        }, 300); // matches animation duration
    }

    $(function() {
        // Handle People menu click
        $('#peopleMenu').on('click', function() {
            $('.submenu-panel').hide();
            $('#submenuPeople').show();
            $('.menu-item').removeClass('active');
            $(this).addClass('active');
        });

        // Handle Company menu click
        $('#companyMenu').on('click', function() {
            $('.submenu-panel').hide();
            $('#submenuCompany').show();
            $('.menu-item').removeClass('active');
            $(this).addClass('active');
        });

        // Close submenu when back arrow is clicked
        // $('#closePeople').on('click', function() {
        //     $('#submenuPeople').hide();
        //     $('.menu-item').removeClass('active');
        // });
        // $('#closeCompany').on('click', function() {
        //     $('#submenuCompany').hide();
        //     $('.menu-item').removeClass('active');
        // });
        $('#companyMenu').on('click', function() {
            openSubmenu('#submenuCompany');
            $('.menu-item').removeClass('active');
            $(this).addClass('active');
        });

        $('#closeCompany').on('click', function() {
            closeSubmenu('#submenuCompany');
            $('.menu-item').removeClass('active');
        });


        // Optional: handle other menu items
        $('#welcome, #dashboard, #myProfile').on('click', function() {
            $('.submenu-panel').hide();
            $('.menu-item').removeClass('active');
            $(this).addClass('active');
        });

        // Sidebar toggle for mobile
        $('#sidebarToggle').on('click', function() {
            $('.sidebar').toggleClass('open');
            $('body').toggleClass('sidebar-open');

        });
        // Optional: close sidebar when clicking overlay
        $(document).on('click', '.sidebar-overlay', function() {
            $('.sidebar').removeClass('open');
            $('body').removeClass('sidebar-open');
        });

        $('#sidebarClose').on('click', function() {
            $('.sidebar').removeClass('open');
            $('body').removeClass('sidebar-open');
            $('.sidebar-overlay').hide();
        });

        document.querySelectorAll('.sidenav-menu__link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidenav-menu__link').forEach(l => l.classList
                    .remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>
