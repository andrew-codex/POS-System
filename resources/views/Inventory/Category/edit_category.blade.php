@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/Inventory/create_category.css') }}">
<script src="{{ asset('/js/edit_category.js') }}"></script>

<div class="content">
    <div class="header">
        <div>
            <a href="{{ route('inventory.categories') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="title-section">
            <h2>Edit a Category</h2>
        </div>
    </div>

    <div class="form-container">
        <form id="edit-category-form" action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="category_name">Category Name <span class="required">*</span></label>
                <input type="text" name="category_name" value="{{$category->category_name}}" required>
            </div>

            <div class="form-group">
                <label for="category_description">Description <span class="required">*</span></label>
                <input type="text" name="category_description" value="{{$category->category_description}}" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="confirmEditCategory('edit-category-form')">Update Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

