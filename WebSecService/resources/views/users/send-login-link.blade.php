@extends('layouts.master')

@section('title', 'Send Login Link')

@section('content')
<div class="d-flex justify-content-center">
  <div class="card m-4 col-sm-6">
    <div class="card-body">
      <form action="{{ route('send_login_link.send') }}" method="post">
        @csrf
        <div class="form-group">
          @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
              <strong>Error!</strong> {{ $error }}
            </div>
          @endforeach
        </div>
        <div class="form-group mb-2">
          <label for="email" class="form-label">Enter your email to receive a login link:</label>
          <input type="email" class="form-control" placeholder="Email" name="email" required>
        </div>

        <div class="form-group mb-2">
          <button type="submit" class="btn btn-primary">Send Login Link</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
