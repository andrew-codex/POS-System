@extends('layouts.app')
@section('title', 'Settings - Add Staff')
@section('content')

<link rel="stylesheet" href="{{ asset('css/Staff/create_staff.css') }}">
<div class="content">
    <div class="header">
        <div>
            <a href="{{ route('settings.index') }}" class="btn ">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="title-section">
            <h2>Add New Staff Member</h2>
        </div>
    </div>

    <div class="form-container mt-4 bg-light-gray p-4 rounded shadow-sm border">
        <form id="create-staff-form" action="{{ route('staff.store') }}" method="POST">
          @csrf
            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="name" id="floatingName" placeholder="Full Name" required>
                <label for="floatingName" class="text-muted">Full Name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="floatingInput" placeholder="name@example.com" required>
                <label for="floatingInput" class="text-muted">Email address</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="phone" id="floatingPhone" placeholder="x9123456789" required>
                <label for="floatingPhone" class="text-muted">Phone number</label>
            </div>

            <div class="form-floating mb-3">
                <select class="form-select" aria-label="Default select example" name="role" id="floatingRole">
                    <option class="text-muted" selected name="role">Select Role </option>
                    <option  value="admin">Administrator</option>
                    <option value="cashier">Cashier</option>
                </select>
            </div>


            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Password" 
                 oninput="passwordMatch()" required>
                <label for="floatingPassword" class="text-muted">Password</label>
              <i class="bi bi-eye-slash toggle-eye" onclick="togglePassword('floatingPassword', this)"></i>
            </div>
            <div class="form-floating mt-3">
                <input type="password" class="form-control" name="password_confirmation" id="floatingConfirmPassword" placeholder="Confirm Password" oninput="passwordMatch()" required>
                <label for="floatingConfirmPassword" class="text-muted">Confirm Password</label>
              <i class="bi bi-eye-slash toggle-eye" onclick="togglePassword('floatingConfirmPassword', this)"></i>
            </div>
            <div id="password-match-message" class="mt-1"></div>
    

            <div class="d-flex justify-content-end mt-4">
                <button id="submitBtn" type="button" class="btn btn-primary px-4" onclick="confirmCreate('create-staff-form')">Create
                    Staff Member</button>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('/Js/create_staff.js')}}"></script>
@endsection