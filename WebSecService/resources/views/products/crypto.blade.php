@extends('layouts.master') {{-- or 'layouts.master' if that’s your path --}}

{{-- Page title --}}
@section('title', 'Crypto Tool')

{{-- Page header (inside your <h1>@yield('header')</h1>) --}}
@section('header', 'Encrypt / Decrypt / Hash')

{{-- Main content --}}
@section('content')
  @if ($errors->any())
    <div class="mb-4 text-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>• {{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('crypto.handle') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label for="text" class="form-label">Text</label>
      <textarea id="text" name="text" rows="4" class="form-control">{{ old('text', $text) }}</textarea>
    </div>

    <div class="mb-3">
      <label for="mode" class="form-label">Mode</label>
      <select id="mode" name="mode" class="form-select">
        <option value="" disabled {{ old('mode', $mode) ? '' : 'selected' }}>— Select —</option>
        <option value="encrypt" {{ old('mode', $mode) === 'encrypt' ? 'selected' : '' }}>Encrypt</option>
        <option value="decrypt" {{ old('mode', $mode) === 'decrypt' ? 'selected' : '' }}>Decrypt</option>
        <option value="hash"    {{ old('mode', $mode) === 'hash'    ? 'selected' : '' }}>Hash</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Process</button>
  </form>

  @if (! is_null($result))
    <div class="mt-4">
      <h2>Result ({{ ucfirst($mode) }})</h2>
      <textarea class="form-control" rows="4" readonly>{{ $result }}</textarea>
    </div>
  @endif
@endsection
