<div id="sidebar">
    <div class="sidebar-brand">
        <div class="logo">
            <h3>
                <img src="{{ isset($settings['system_logo']) ? asset($settings['system_logo']) : asset('images/default-logo.jpg') }}" 
                     width="55" height="60">
            </h3>
        </div>
        <span class="full">{{ $settings['system_name'] ?? 'POS System' }}</span>
        <span class="mini">PS</span>
    </div>

    <ul>
        @if(in_array('view_dashboard', $rolePermissions))
        <li><a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        @endif

        @if($showProductsMenu)
        <li class="nav-item">
            <a href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#productsSubmenu" 
               class="nav-link d-flex align-items-center {{ (request()->is('*inventory/products*') || request()->is('*inventory/categories*') || request()->is('*inventory/stocks*')) ? '' : 'collapsed' }}" 
               aria-expanded="{{ (request()->is('*inventory/products*') || request()->is('*inventory/categories*') || request()->is('*inventory/stocks*')) ? 'true' : 'false' }}">
                <i class="bi bi-kanban"></i><span class="ms-2">Manage Products</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="productsSubmenu" class="collapse nav flex-column ms-4 {{ (request()->is('*inventory/products*') || request()->is('*inventory/categories*') || request()->is('*inventory/stocks*')) ? 'show' : '' }}">
                @if(in_array('view_products', $rolePermissions))
                <li><a href="{{ route('inventory.products') }}" class="nav-link {{ request()->is('*inventory/products*') ? 'active' : '' }}"><i class="bi bi-box"></i> <span>Products</span></a></li>
                @endif
                @if(in_array('view_categories', $rolePermissions))
                <li><a href="{{ route('inventory.categories') }}" class="nav-link {{ request()->is('*inventory/categories*') ? 'active' : '' }}"><i class="bi bi-tags"></i> <span>Categories</span></a></li>
                @endif
                @if(in_array('view_stock', $rolePermissions))
                <li><a href="{{ route('inventory.stock') }}" class="nav-link {{ request()->is('*inventory/stocks*') ? 'active' : '' }}"><i class="bi bi-stack"></i> <span>Stock</span></a></li>
                @endif
            </ul>
        </li>
        @endif

        @if(in_array('view_cart', $rolePermissions))
        <li><a href="{{ route('pos.cart') }}" class="{{ request()->is('cart*') ? 'active' : '' }}"><i class="bi bi-cart"></i><span>Cart</span></a></li>
        @endif

        @if(in_array('view_sales', $rolePermissions))
        <li><a href="{{ route('pos.sales') }}" class="{{ request()->is('sales*') ? 'active' : '' }}"><i class="bi bi-cart-check"></i><span>Sales</span></a></li>
        @endif

        @if(in_array('view_reports', $rolePermissions))
        <li><a href="{{ route('pos.reports') }}" class="{{ request()->is('pos/reports*') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i><span>Reports</span></a></li>
        @endif

        @if(in_array('manage_settings', $rolePermissions))
        <li><a href="{{ route('settings.index') }}" class="{{ request()->is('settings*') ? 'active' : '' }}"><i class="bi bi-gear"></i><span>Settings</span></a></li>
        @endif

        <li class="logout-link mt-auto">
            <form action="{{ route('auth.logout') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit" 
                        class="nav-link btn btn-link d-flex align-items-center p-0 m-0 text-start w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span>Logout</span>
                </button>
            </form>
        </li>
    </ul>
</div>

@push('scripts')
<script src="{{ asset('/Js/sidebar.js') }}"></script>
@endpush
