    @extends('layouts.app')

    @section('title', 'Products')
    @section('content')

    <link rel="stylesheet" href="{{asset('/css/Inventory/products.css')}}">
    <script src="{{asset('/js/products_page.js')}}"></script>
    <div class="content">
        <div class="header">
            <div>
                <h2 class="title">Products</h2>
                <p class="subtitle">Manage your products here</p>
            </div>
            <div>
                <a class="btn-primary" href="{{ route('products.create') }}">
                    <i class="bi bi-plus"></i>Add Product
                </a>
            </div>
        </div>

        <div class="table-header">
            <form action="{{ route('inventory.products') }}" method="GET">
                <div class="search-bar">
                    <input type="text" name="search" class="form-control" placeholder="Search products..."
                        value="{{ request('search') }}">

                    <div class="filter-bar">
                        <label for="category-filter"></label>
                        <select name="category" id="category-filter" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if(request('search'))
                    <a href="{{ route('inventory.products') }}" class="btn btn-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-content">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Barcode</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>

                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->product_description }}</td>
                        <td>{{ $product->product_price }}</td>
                        <td>{{ $product->product_barcode }}</td>
                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                        <td>

                            <a href="{{ route('products.edit', ['id' => $product->id]) }}" class="btn-edit"
                                role="button">
                                <i class="bi bi-pencil"></i>Edit
                            </a>


                            <form id="delete-form-{{ $product->id }}"
                                action="{{ route('products.destroy', ['id' => $product->id]) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-form-{{ $product->id }}')"
                                    class="btn-delete">
                                    <i class="bi bi-trash"></i>Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-links">
            <div class="result-links">
                @if($products->total() > 0)
                <span>
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }}
                    products
                </span>
                @else
                <span class="result-links no-products">No products found.</span>
                @endif
            </div>
            <div>
                {{ $products->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
            </div>
        </div>
    </div>
    @endsection