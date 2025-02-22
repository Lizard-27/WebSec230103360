@extends('layouts.master')

@section('title', 'Multiplication Tables')

@section('content')
  <div class="row">
    @foreach(range(1, 10) as $j)
      <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
        <div class="card">
          <div class="card-header text-center">
            {{ $j }} Multiplication Table
          </div>
          <div class="card-body p-2">
            <table class="table table-sm mb-0">
              @foreach(range(1, 10) as $i)
                <tr>
                  <td>{{ $i }} * {{ $j }}</td>
                  <td>= {{ $i * $j }}</td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
