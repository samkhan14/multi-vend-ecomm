<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vendor Registration - Diginotive</title>
    @php $settings = siteSetting(); @endphp
    <link rel="icon"
        href="{{ $settings && $settings->favicon ? asset('storage/' . $settings->favicon) : asset('assets/images/default-favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owncustom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    @stack('style')
    <style>
        /* Force validation messages to show when they have content */
        .invalid-feedback:not(:empty) {
            display: block !important;
            color: #dc3545 !important;
            font-size: 0.875em !important;
            margin-top: 0.25rem !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendors/plugin/npm/select2@4.0.13/dist/css/select2.min.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendors/plugin/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css') }}" />

    @vite(['resources/js/app.js'])
    @livewireStyles

</head>

<body style="height: 100vh; margin: 0; padding: 0;">
    <main style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">

        @yield('vendor')
    </main>

    @livewireScripts
    <script src="{{ asset('assets/vendors/ajax/libs/jquery/3.6.0/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendors/plugin/npm/select2@4.0.13/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.addEventListener('show-toast', event => {
            Swal.fire({
                toast: true,
                icon: event.detail.type,
                title: event.detail.message,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>

    @if (session('toast'))
        <script>
            Swal.fire({
                toast: true,
                icon: "{{ session('toast.type') }}",
                title: "{{ session('toast.message') }}",
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>
