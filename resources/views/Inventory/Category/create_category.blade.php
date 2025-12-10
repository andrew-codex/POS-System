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
                <label>Category Name <span class="required">*</span></label>
                <input type="text" name="category_name" required>
            </div>

            <div class="form-group">
                <label>Description <span class="required">*</span></label>
                <input type="text" name="category_description" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="confirmCreate('create-category-form')">Create Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

