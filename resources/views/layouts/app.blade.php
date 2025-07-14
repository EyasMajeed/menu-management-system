<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    
    {{-- Bootstrap CSS from cdn.jsdelivr.net (most reliable CDN for Bootstrap 5.3.x) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    {{-- Bootstrap Icons CSS (if you use any Bootstrap Icons) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    {{-- Custom styles for your application --}}
    <style>
        /* General table styling */
        .table-group-divider {
            border-top-color: #cccccc !important;
            border-top-width: 2px !important;
        }
        .custom-row-spacing-table th,
        .custom-row-spacing-table td {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        /* Custom styling for the nav-pills to make them fully rounded and button-like */
        /* This targets the .nav-pills class you're using in your partial */
        .nav-pills .nav-link {
            border: none; /* Remove any default borders */
            border-radius: 9999px; /* Makes the corners fully rounded (pill shape) */
            padding: 0.5rem 0.8rem; /* ADJUSTED: Reduced padding for smaller size */
            margin-right: 1rem; /* Space between the pills */
            background-color: transparent; /* Default transparent background for inactive pills */
            color: #212529; /* Default text color (dark) */
            transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transitions */
            font-weight: normal; /* Ensure text is not bold by default */
            text-decoration: none; /* Remove underline from links */
            font-size: 0.9rem; /* Optionally make text slightly smaller */
        }

        .nav-pills .nav-link:hover {
            background-color: #e9ecef; /* Light background on hover */
            color: #212529;
        }

        .nav-pills .nav-link.active {
            background-color: #212529; /* Dark background for active pill */
            color: #ffffff; /* White text for active pill */
            font-weight: bold; /* Make active text bold */
            /* No border-bottom needed for nav-pills, unlike nav-tabs */
        }

        /* Ensure the nav-pills container itself doesn't have unwanted borders */
        .nav.nav-pills {
            border-bottom: none; /* Just in case a parent style adds it */
        }
    </style>

    @stack('styles') {{-- For any page-specific styles pushed from other views --}}
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{route('menus.index')}}">Blend</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{route('menus.index')}}">All Menus</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{route('orders.index')}}">All Orders</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- NEW: Flash Messages Display Area --}}
    <div class="container mt-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    {{-- END NEW: Flash Messages Display Area --}}

    @yield('content')

    <!-- Bootstrap JS Bundle (includes Popper.js for dropdowns, tooltips etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    @stack('scripts') {{-- For any page-specific JavaScript pushed from other views --}}
</body>

</html>
