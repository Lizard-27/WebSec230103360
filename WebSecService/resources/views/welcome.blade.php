@extends('layouts.master')
@section('title', 'Welcome')
@section('content')
<div class="container mt-5">
  <!-- Hero Section -->
  <div class="p-5 mb-4 bg-primary text-white rounded-3 shadow">
    <div class="container-fluid py-5">
      <h1 class="display-4 fw-bold">Welcome to Our Platform!</h1>
      <p class="col-md-8 fs-5">
        Explore a world of quality products, comprehensive student resources, and timely updates—all tailored for you.
      </p>
      <a class="btn btn-light btn-lg" href="{{ route('products_list') }}" role="button">Shop Now</a>
    </div>
  </div>

  <!-- Feature Cards Section -->
  <div class="row text-center">
    <div class="col-md-4">
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Quality Products</h5>
          <p class="card-text">
            Discover our wide range of premium products that combine style and functionality.
          </p>
          <a href="{{ route('products_list') }}" class="btn btn-outline-primary">View Products</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Student Portal</h5>
          <p class="card-text">
            Access your student resources, check grades, and view academic records with ease.
          </p>
          <a href="{{ route('students_list') }}" class="btn btn-outline-primary">Enter Portal</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Latest News</h5>
          <p class="card-text">
            Stay updated with our latest news, offers, and company updates.
          </p>
          <a href="{{ url('/test') }}" class="btn btn-outline-primary">Learn More</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
