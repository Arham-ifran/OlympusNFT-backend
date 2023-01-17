@extends('errors.errors_layout')

@section('title')
    401 - Access Denied
@endsection

@section('error-content')
    <h2>401</h2>
    <p>Access to this resource on the server is denied</p>
    <hr>
    <p class="mt-2">
        {{ $exception->getMessage() }}
    </p>
    <a href="{{ route('admin.dashboard') }}">Back to Dashboard</a>

@endsection
