@extends('layouts.master')

@section('title', 'Even Numbers')

@section('content')
  <div class="card">
    <div class="card-header text-center">Even Numbers</div>
    <div class="card-body ">
      @foreach (range(1, 100) as $i)
        @if($i % 2 == 0)
          <span class="badge bg-primary me-1 mb-1">{{ $i }}</span>
        @else
          <span class="badge bg-secondary me-1 mb-1">{{ $i }}</span>
        @endif
      @endforeach
    </div>
  </div>
@endsection
