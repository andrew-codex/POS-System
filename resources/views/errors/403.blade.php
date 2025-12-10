@extends('layouts.app')

@section('title', 'Unauthorized')

@section('content')
<style>
    .unauthorized-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 80vh;
        text-align: center;
        padding: 2rem;
        background: #f8fafc; 
    }

    .unauthorized-card {
        background: #ffffff;
        padding: 3rem 2rem;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 90%;
        transition: transform 0.3s ease;
    }

    .unauthorized-card:hover {
        transform: translateY(-5px);
    }

    .unauthorized-card h1 {
        font-size: 3rem;
        color: #ef4444; 
        margin-bottom: 1rem;
    }

    .unauthorized-card p {
        font-size: 1.1rem;
        color: #475569;
        margin-bottom: 2rem;
    }

    .unauthorized-card .btn {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        border-radius: 8px;
        transition: 0.3s;
    }

    .unauthorized-card .btn:hover {
        transform: translateY(-2px);
    }
</style>

<div class="unauthorized-container">
    <div class="unauthorized-card">
        <h1>403</h1>
        <h2>Unauthorized</h2>
        <p>Sorry, you do not have permission to access this page.</p>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Go Back</a>
    </div>
</div>
@endsection
