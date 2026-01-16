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
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            margin: 0;
            padding-top: 56px;
            overflow-x: hidden;
        }

        #sidebar ul, #sidebar ul li {
            list-style: none !important;
            padding: 0;
            margin: 0;
        }

        #sidebar {
            display: flex;
            flex-direction: column; 
            width: 250px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding-top: 56px;
            z-index: 1040;
        }

        #sidebar.collapsed { width: 80px; }

        /* Unified Link & Button Styling */
        #sidebar ul li a, 
        #sidebar ul .logout-link button {
            display: flex;
            align-items: center;
            height: 48px;
            padding: 0 16px;
            gap: 12px;
            color: #64748b;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 4px 12px;
            border: none;
            background: transparent;
            width: calc(100% - 24px); 
            box-sizing: border-box;
            cursor: pointer;
            white-space: nowrap;
        }

        #sidebar ul li a:hover,
        #sidebar ul .logout-link button:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        #sidebar ul li a.active,
        #sidebar ul li a[aria-expanded="true"] {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }

        /* Submenu Fixes */
        #sidebar ul .collapse {
            list-style: none;
            overflow: hidden; /* Prevents overlap during animation */
        }

        #sidebar ul .collapse li a {
            height: 40px;
            margin: 2px 12px 2px 40px; 
            font-size: 14px;
            width: calc(100% - 52px); /* Specifically shorter to stay inside */
        }

        /* Bottom Push for Logout */
        #sidebar ul.nav-list { height: 100%; display: flex; flex-direction: column; }
        #sidebar .logout-link { margin-top: auto; margin-bottom: 20px; }

        /* Collapsed State Overrides */
        #sidebar.collapsed ul li a,
        #sidebar.collapsed ul .logout-link button {
            justify-content: center;
            padding: 0;
            margin: 4px 10px;
            width: calc(100% - 20px);
            gap: 0;
        }

        #sidebar.collapsed .full,
        #sidebar.collapsed span, 
        #sidebar.collapsed .ms-auto,
        #sidebar.collapsed .collapse.show {
            display: none !important;
        }

        /* Layout Elements */
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
        #sidebar.collapsed ~ .navbar-custom { left: 80px; }

        .sidebar-brand {
            position: fixed; top: 0; left: 0; width: 250px; height: 56px;
            background: #ffffff; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; padding: 0 15px; transition: 0.3s; z-index: 1050;
        }
        #sidebar.collapsed .sidebar-brand { width: 80px; justify-content: center; padding: 0; }

        #content { margin-left: 250px; padding: 25px; transition: 0.3s; }
        #content.expanded { margin-left: 80px; }
        
        .toggle-btn { background: transparent; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; padding: 10px 20px; }
    </style>
</head>
<body>
    @include('components.sidebar')

    <nav class="navbar-custom">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="bi bi-layout-sidebar-inset-reverse"></i>
        </button>
        <span class="ms-2 fw-bold">POS System</span>
        <div class="ms-auto me-3 text-muted small">
            {{ now()->setTimezone('Asia/Manila')->format('F j, g:i A') }}
        </div>
    </nav>

    <div id="content">
        @yield('content')
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("expanded");

            // Close submenus if collapsing
            if (sidebar.classList.contains("collapsed")) {
                $('.collapse').collapse('hide');
            }
        }
    </script>
</body>
</html>