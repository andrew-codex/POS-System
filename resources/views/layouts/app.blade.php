<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">


    <style>
    body {
        font-family: 'Inter', sans-serif;
        font-weight: 400;
        background: #f8f9fc;
        margin: 0;
        padding-top: 56px;
        overflow-x: hidden;
    }

    h1,
    h2,
    h3,
    .navbar-brand {
        font-weight: 500;
    }

    small,
    .small-text {
        font-weight: 300;
    }


    #sidebar {
        width: 250px;
        height: 100vh;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        position: fixed;
        top: 0;
        left: 0;
        transition: 0.3s;
        padding-top: 56px;
    }

    #sidebar.collapsed {
        width: 80px;
    }

    .sidebar-brand {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 56px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        padding-left: 20px;
        font-weight: 700;
        font-size: 1.25rem;
        color: #1e293b;
        transition: 0.3s;
        z-index: 1050;
    }

    #sidebar.collapsed .sidebar-brand {
        width: 80px;
        justify-content: center;
        padding-left: 0;
    }

    .sidebar-brand .mini {
        display: none;
    }

    #sidebar.collapsed .sidebar-brand .full {
        display: none;
    }

    #sidebar.collapsed .sidebar-brand .mini {
        display: block;
    }

    .navbar-custom {
        background: #ffffff;
        height: 56px;
        position: fixed;
        top: 0;
        left: 250px;
        right: 0;
        z-index: 1000;
        border-bottom: 1px solid #e2e8f0;
        display: flex;

        align-items: center;
        transition: 0.3s;
    }

    #sidebar.collapsed~.navbar-custom {
        left: 80px;
    }


    .navbar-center {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-btn {
        background: transparent;
        border: none;
        font-size: 1.6rem;
        cursor: pointer;
        color: #5a5a5aff;
        border-radius: 8px;
        padding-left: 2rem;


    }

    .navbar-brand {
        color: #1e293b;
        font-size: 1rem;
        user-select: none;
    }

    #sidebar ul {
        list-style: none;
        padding-left: 0;
        margin-top: 20px;
    }

    #sidebar ul li a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        gap: 12px;
        color: #475569;
        font-size: 15px;
        text-decoration: none;
        transition: 0.2s;
        border-radius: 8px;
        margin: 3px 10px;
    }

    #sidebar ul .logout-link {
     display: flex;
        align-items: center;
        padding: 12px 20px;
        gap: 12px;
        color: #475569;
        font-size: 15px;
        text-decoration: none;
        transition: 0.2s;
        border-radius: 8px;
        margin: 3px 10px;
    }

    #sidebar ul li a:hover,
    #sidebar ul li a.active {
        background: #f1f5f9;
        color: #000;
        font-weight: 600;
    }

    #sidebar ul .logout-link:hover,
    #sidebar ul .logout-link button:hover {
        background: #f1f5f9;
        color: #000;
        font-weight: 600;
    } 

    #sidebar.collapsed ul li a span {
        display: none;
    }

    #sidebar.collapsed ul li a {
        justify-content: center;
    }


    #content {
        margin-left: 250px;
        padding: 25px;
        transition: 0.3s;
    }

    #content.expanded {
        margin-left: 80px;
    }

    .required {
        color: red;
        margin-left: .2em;
    }

    #sidebar.collapsed ul .collapse {
        position: absolute;
        left: 80px;
        top: auto !important;
        background: #ffffff;
        padding: 10px;
        border-radius: 10px;
        width: 180px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        z-index: 9999;
        transform: translateY(-12px);
    }


    #sidebar.collapsed ul .collapse.show {
        display: block !important;
    }


    #sidebar.collapsed ul .collapse .nav-link {
        padding-left: 10px !important;
    }


    #sidebar.collapsed .nav-link .ms-auto {
        display: none;
    }

    .sidebar-brand .logo{
        padding: 5px;
    }
    </style>
</head>

<body>
    @include('components.sidebar')
    <nav class="navbar-custom">
        <div class="navbar-center">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="bi bi-layout-sidebar-inset-reverse"></i>
            </button>
            <span class="navbar-brand">POS System</span>
        </div>
        <div class="time-zone ms-auto me-3">
            <small class="text-info">{{ now()->setTimezone('Asia/Manila')->format('F j, g:i A') }}</small>
        </div>
    </nav>

    <div id="content">
        @yield('content')
    </div>

    <script>
    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        let content = document.getElementById("content");

        sidebar.classList.toggle("collapsed");
        content.classList.toggle("expanded");


        if (sidebar.classList.contains("collapsed")) {


            document.querySelectorAll("#sidebar .collapse.show").forEach(submenu => {
                let bs = bootstrap.Collapse.getInstance(submenu);

                if (!bs) {
                    bs = new bootstrap.Collapse(submenu, {
                        toggle: false
                    });
                }

                bs.hide();
            });


            document.querySelectorAll('#sidebar [data-bs-toggle="collapse"]').forEach(trigger => {
                trigger.classList.add("collapsed");
                trigger.setAttribute("aria-expanded", "false");
            });
        }
    }

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
    toastr.error("{{ session('error') }}");
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error(@json($error));
        @endforeach
    @endif
    </script>
</body>

</html>









