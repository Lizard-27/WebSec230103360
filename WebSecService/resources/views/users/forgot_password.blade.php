@extends('layouts.master')
@section('title', 'Forgot Password')
@section('content')
<div class="container mt-5">
    <h1>Forgot Password</h1>

    {{-- Display success message if available --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Display errors if validation fails --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('forgot_password.send') }}" method="POST">
        @csrf
        <div class="form-group mb-2">
            <label for="email" class="form-label">Email Address:</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Temporary Password</button>
    </form>
</div>
@endsection
