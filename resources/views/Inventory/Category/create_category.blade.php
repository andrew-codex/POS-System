@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/Inventory/create_category.css') }}">
<script src="{{ asset('/js/create_category.js') }}"></script>

<div class="content">
    <div class="header">
        <div>
            <a href="{{ route('inventory.categories') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="title-section">
            <h2>Create a New Category</h2>
        </div>
    </div>

    <div class="form-container">
        <form id="create-category-form" action="{{ route('categories.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="category_name">Category Name <span class="required">*</span></label>
                <input type="text" name="category_name" id="category_name" required>
            </div>

            <div class="form-group">
                <label for="category_description">Description <span class="required">*</span></label>
                <input type="text" name="category_description" id="category_description" required>
            </div>

            <div class="form-actions">
                <button type="button" id="createCategoryButton" class="btn-primary" onclick="confirmCreate('create-category-form')"> <i class="bi bi-plus"></i> Create Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

