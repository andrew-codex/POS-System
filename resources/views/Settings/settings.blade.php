@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Settings/settings.css') }}">

@php
    $activeTab = request()->get('tab', 'general'); // default is general
@endphp

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button"
            role="tab">General Settings</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'users' ? 'active' : '' }}" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button"
            role="tab">User Management</button>
    </li>
    <li>
        <button class="nav-link {{ $activeTab === 'roles' ? 'active' : '' }}" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">
            Role Permissions
        </button>
    </li>

    <li>
        <button class="nav-link {{ $activeTab === 'audit-logs' ? 'active' : '' }}" id="audit-logs-tab" data-bs-toggle="tab" data-bs-target="#audit-logs" type="button" role="tab">
            Audit Logs
        </button>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    
    <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }} p-3" id="general" role="tabpanel">
        <div class="container ">
            <h3 class="fw-light mb-3">General Settings</h3>
            @include('settings.sections.generalSettings')
        </div>

    </div>


    <div class="tab-pane fade {{ $activeTab === 'users' ? 'show active' : '' }} p-3" id="users" role="tabpanel">
        <header class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-light">User & Access Management</h3>
                <p class="text-muted">Manage user roles, permissions.</p>
            </div>
            <div>
                <a class="btn btn-primary" href="{{ route('staff.create') }}">
                    <i class="bi bi-plus"></i> Add User
                </a>
            </div>
        </header>
        <main>
            @include('settings.sections.staff')
        </main>
    </div>

    <div class="tab-pane fade {{ $activeTab === 'roles' ? 'show active' : '' }} p-3" id="roles" role="tabpanel">
        <h4 class="mb-3">Role Permissions</h4>
        @foreach($roles as $role)
        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ ucfirst($role) }} Permissions</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.permissions.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        @foreach($allPermissions as $permission)
                        @php

                        $rolePerms = $rolePermissionsMap[$role] ?? [];


                        $rolePerms = array_map(fn($p) => strtolower(trim($p)), $rolePerms);
                        $normalizedPermission = strtolower(trim($permission));


                        $checked = in_array($normalizedPermission, $rolePerms);
                        @endphp

                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="permissions[]"
                                    value="{{ $permission }}" id="perm_{{ $role }}_{{ $permission }}"
                                    {{ $checked ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $role }}_{{ $permission }}">
                                    {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="save-btn text-end">
                        <button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
                    </div>

                </form>
            </div>
        </div>
        @endforeach
    </div>
    <div class="tab-pane fade {{ $activeTab === 'audit-logs' ? 'show active' : '' }} p-3" id="audit-logs" role="tabpanel">
        @include('settings.sections.audit_logs')
    </div>

</div>
<script src="{{asset('/Js/settings.js')}}"></script>
@endsection