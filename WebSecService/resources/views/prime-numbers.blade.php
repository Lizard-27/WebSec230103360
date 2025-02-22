@extends('layouts.master')

@section('title', 'Prime Numbers')

@section('content')
  <div class="card">
    <div class="card-header text-center">
      Prime Numbers
    </div>
    <div class="card-body">
      <?php
        function isPrime($number) {
            if ($number < 2) {
                return false;
            }
            for ($i = 2; $i <= sqrt($number); $i++) {
                if ($number % $i === 0) {
                    return false;
                }
            }
            return true;
        }
      ?>
      <div class="d-flex flex-wrap gap-2">
        @foreach (range(1, 100) as $i)
          @if(isPrime($i))
            <span class="badge bg-primary p-2">{{ $i }}</span>
          @else
            <span class="badge bg-secondary p-2">{{ $i }}</span>
          @endif
        @endforeach
      </div>
    </div>
  </div>
@endsection
