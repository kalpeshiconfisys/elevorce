<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo/temp_logo.png') }}">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Tailwind (optional UI) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet" />

</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')
        <!-- Overlay -->
        <div class="sidebar-overlay"></div>
        <div class="main-content">
            @include('admin.layouts.header', ['company' => $data['company'] ?? []] ?? [])
            <!-- Flash Messages -->
            <div class="container-fluid mt-3">
                @if (session('success'))
                    <div class="alert alert-success custom-alert">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger custom-alert">{{ session('error') }}</div>
                @endif
            </div>

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    @include('admin.layouts.footer')


    <!-- jQuery FIRST -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>


    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ✅ Ask Notification Permission at load
        document.addEventListener("DOMContentLoaded", function() {
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }
        });

        // ✅ Pusher setup
        Pusher.logToConsole = true;
        var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            forceTLS: true
        });

        var channel = pusher.subscribe('contact-channel');

        channel.bind('new-contact', function(data) {
            let name = data.name || "Someone";
            let message = name + " sent an inquiry.";

            if (!document.hidden) {
                Swal.fire({
                    title: '📨 New Inquiry!',
                    text: message,
                    icon: 'info',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ✅ જો URLમાં dashboard છે તો refresh કરો
                        if (window.location.pathname === "/admin/dashboard") {
                            location.reload();
                        }
                    }
                });
            } else {
                if (Notification.permission === "granted") {
                    var notification = new Notification("📨 New Inquiry!", {
                        body: message,
                        icon: "/images/logo/logo.png"
                    });

                    notification.onclick = function() {
                        window.focus();
                        this.close();
                    };
                }
            }
        });
    </script>

    <!-- Custom Bootstrap Bundle (if really needed, otherwise remove duplicate) -->
    {{-- <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script> --}}

    <script>
        $(document).ready(function() {
            // DataTable Init
            if ($("#example tbody tr").not(':has(td[colspan])').length > 0) {
                $('#example').DataTable({
                    paging: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    language: {
                        search: "Search:",
                        emptyTable: "No data available"
                    },
                    dom: '<"d-flex align-items-center justify-content-start"f>t<"d-flex justify-content-between"ip>'
                });
            }

            // Auto dismiss flash messages
            setTimeout(function() {
                $('.custom-alert').slideUp(500);
            }, 3000);
        });
    </script>



    <!-- 🔹 Extra scripts pushed from child views -->
    @stack('script')
</body>

</html>
