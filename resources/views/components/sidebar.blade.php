<div id="sidebar">
    <div class="sidebar-brand">
        <div class="logo">
            <img src="{{ isset($settings['system_logo']) ? asset($settings['system_logo']) : asset('images/logo.jpg') }}" 
                 width="35" height="35" style="object-fit: contain;">
        </div>
        <span class="full ms-2">{{ $settings['system_name'] ?? 'POS System' }}</span>
    </div>

    <ul class="nav-list grow">
        @if(in_array('view_dashboard', $rolePermissions))
        <li>
            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i><span>Dashboard</span>
            </a>
        </li>
        @endif

        @if($showProductsMenu)
        <li class="nav-item">
            <a href="#productsSubmenu" data-bs-toggle="collapse" 
               class="d-flex align-items-center {{ (request()->is('*inventory/*')) ? '' : 'collapsed' }}" 
               aria-expanded="{{ (request()->is('*inventory/*')) ? 'true' : 'false' }}">
                <i class="bi bi-kanban"></i>
                <span class="ms-1">Manage Products</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="productsSubmenu" class="collapse {{ (request()->is('*inventory/*')) ? 'show' : '' }}">
                @if(in_array('view_products', $rolePermissions))
                <li>
                    <a href="{{ route('inventory.products') }}" class="{{ request()->is('*products*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam" style="font-size: 0.85rem;"></i>
                        <span>Products</span>
                    </a>
                </li>
                @endif
                @if(in_array('view_categories', $rolePermissions))
                <li>
                    <a href="{{ route('inventory.categories') }}" class="{{ request()->is('*categories*') ? 'active' : '' }}">
                        <i class="bi bi-tags" style="font-size: 0.85rem;"></i>
                        <span>Categories</span>
                    </a>
                </li>
                @endif
                @if(in_array('view_stock', $rolePermissions))
                <li>
                    <a href="{{ route('inventory.stock') }}" class="{{ request()->is('*stocks*') ? 'active' : '' }}">
                        <i class="bi bi-stack" style="font-size: 0.85rem;"></i>
                        <span>Stock</span>
                    </a>
                </li>
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

        <li class="logout-link">
            <form action="{{ route('auth.logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="button" id="logout-trigger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </li>
    </ul>
</div>

@push('scripts')
<script src="{{ asset('Js/sidebar.js') }}"></script>
@endpush