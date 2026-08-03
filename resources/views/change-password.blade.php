@extends('layouts.app')

@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="breadcrumb-wrapper">
      <h2 class="product-title">Change Password</h2>
      <ol class="breadcrumb">
        <li><a href="{{ route('home') }}"><i class="ti-home"></i> Home</a></li>
        <li class="current">Change Password</li>
      </ol>
    </div>
  </div>
</div>

<div id="content">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="inner-box">
          <h3>Update Your Password</h3>
          <p class="text-muted">Choose a new password with at least 8 characters.</p>

          @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('password.change') }}">
            @csrf
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input id="current_password" class="form-control" type="password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="form-group">
              <label for="password">New Password</label>
              <input id="password" class="form-control" type="password" name="password" required minlength="8" autocomplete="new-password">
            </div>
            <div class="form-group">
              <label for="password_confirmation">Confirm New Password</label>
              <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-common"><i class="ti-lock"></i> Change Password</button>
            <a href="{{ route('add-resume') }}" class="btn btn-default">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@include('partials.footer')

@endsection
