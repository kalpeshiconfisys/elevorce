<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title')</title>
    <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo/temp_logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />


    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">


    {{--  UI --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


    {{-- style --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">


    {{-- fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet" />
    {{-- @stack('styles') --}}

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body>
    <div class="">


        <div class="sidebar-overlay"></div>

        @include('admin.layouts.sub_header')

        <div class="container-fluid mt-3">
            @if (session('success'))
                <div class="alert alert-success custom-alert" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger custom-alert" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        @yield('content')

        @include('admin.layouts.sub_footer')

    </div>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}
    <!-- jQuery (already included in your app, but ensure version >= 3.6) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            // Auto dismiss flash messages after 3 seconds
            setTimeout(function() {
                $('.custom-alert').slideUp(500);
            }, 3000); // 3 seconds
        });
    </script>

    @stack('scripts')

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



</body>

</html>
