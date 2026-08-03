@extends('layouts.app')
@section('content')

@include('partials.header')

<!-- Page Header -->
<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-wrapper">
          <h2 class="product-title">My Account</h2>
          <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="ti-home"></i> Home</a></li>
            <li class="current">My Account</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="content" class="my-account">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 cd-user-modal">

        {{-- Flash messages --}}
        @if (session('success'))
          <div class="alert alert-success"><i class="ti-check"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="alert alert-danger"><i class="ti-close"></i> {{ session('error') }}</div>
        @endif

        @auth
          {{-- ── Logged in state ── --}}
          <div class="my-account-form" style="text-align:center; padding: 30px;">
            <div style="margin-bottom: 20px;">
              <i class="ti-user" style="font-size: 50px; color: #e84646;"></i>
            </div>
            <h3>Hello, {{ auth()->user()->name }}!</h3>
            <p class="text-muted">{{ auth()->user()->email }}</p>
            <p>
              <span class="label label-{{ auth()->user()->isAdmin() ? 'danger' : (auth()->user()->isEmployer() ? 'primary' : 'success') }}">
                {{ ucfirst(auth()->user()->role) }}
              </span>
            </p>

            <div style="margin-top: 25px;">
              @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn btn-common" style="margin: 5px;">
                  <i class="ti-dashboard"></i> Admin Panel
                </a>
              @elseif(auth()->user()->isEmployer())
                <a href="{{ route('manage-jobs') }}" class="btn btn-common" style="margin: 5px;">
                  <i class="ti-briefcase"></i> My Jobs
                </a>
                <a href="{{ route('manage-applications') }}" class="btn btn-common" style="margin: 5px;">
                  <i class="ti-email"></i> Applications
                </a>
              @else
                <a href="{{ route('jobs.index') }}" class="btn btn-common" style="margin: 5px;">
                  <i class="ti-search"></i> Browse Jobs
                </a>
                <a href="{{ route('bookmarked') }}" class="btn btn-common" style="margin: 5px;">
                  <i class="ti-heart"></i> Saved Jobs
                </a>
              @endif
              <a href="{{ route('add-resume') }}" class="btn btn-default" style="margin: 5px;">
                <i class="ti-user"></i> My Profile
              </a>
              <a href="{{ route('change-password') }}" class="btn btn-default" style="margin: 5px;">
                <i class="ti-lock"></i> Change Password
              </a>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
              @csrf
              <button type="submit" class="btn btn-danger">
                <i class="ti-power-off"></i> Logout
              </button>
            </form>
          </div>

        @else
          {{-- ── Guest state: Login / Register tabs ── --}}
          <div class="my-account-form">
            @php($formMode = old('_form', $formMode ?? 'account'))

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul style="margin:0; padding-left:18px;">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            @if ($formMode === 'forgot')
              <h3>Reset Your Password</h3>
              <p class="text-muted">Enter your email address. If an account exists, we will send a reset link.</p>
              <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="hidden" name="_form" value="forgot">
                <div class="form-group">
                  <div class="input-icon"><i class="ti-email"></i><input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus></div>
                </div>
                <button type="submit" class="btn btn-common log-btn">Send Reset Link</button>
                <p style="margin-top:15px;"><a href="{{ route('my-account') }}">Back to login</a></p>
              </form>
            @elseif ($formMode === 'reset')
              <h3>Choose a New Password</h3>
              <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="_form" value="reset">
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                  <div class="input-icon"><i class="ti-email"></i><input type="email" class="form-control" name="email" value="{{ old('email', $resetEmail ?? '') }}" placeholder="Email Address" required autocomplete="email"></div>
                </div>
                <div class="form-group">
                  <div class="input-icon"><i class="ti-lock"></i><input id="reset-password" type="password" class="form-control" name="password" placeholder="New password (minimum 8 characters)" required autocomplete="new-password"></div>
                  <label class="checkbox" style="margin-top:8px;"><input type="checkbox" data-password-toggle="reset-password"> Show password</label>
                </div>
                <div class="form-group">
                  <div class="input-icon"><i class="ti-lock"></i><input type="password" class="form-control" name="password_confirmation" placeholder="Confirm new password" required autocomplete="new-password"></div>
                </div>
                <button type="submit" class="btn btn-common log-btn">Update Password</button>
              </form>
            @else
            @php($selectedForm = old('_form', request('form') === 'register' ? 'register' : 'login'))
            <ul class="cd-switcher">
              <li><a class="{{ $selectedForm === 'login' ? 'selected' : '' }}" href="#0">LOGIN</a></li>
              <li><a class="{{ $selectedForm === 'register' ? 'selected' : '' }}" href="#0">REGISTER</a></li>
            </ul>

            {{-- LOGIN --}}
            <div id="cd-login" class="{{ $selectedForm === 'login' ? 'is-selected' : '' }}">
              <div class="page-login-form">
                <form method="POST" action="{{ route('login') }}">
                  @csrf
                  <input type="hidden" name="_form" value="login">
                  @php($selectedRole = old('role', request('role') === 'employer' ? 'employer' : 'candidate'))
                  <div class="form-group">
                    <div class="input-icon">
                      <i class="ti-email"></i>
                      <input type="email" class="form-control" name="email"
                             value="{{ old('email') }}" placeholder="Email Address" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-icon">
                      <i class="ti-lock"></i>
                      <input id="login-password" type="password" class="form-control" name="password"
                             placeholder="Password" required>
                    </div>
                    <label class="checkbox" style="margin-top:8px;">
                      <input type="checkbox" data-password-toggle="login-password"> Show password
                    </label>
                  </div>
                  <button type="submit" class="btn btn-common log-btn">Login</button>
                  <div class="checkbox-item" style="margin-top:10px;">
                    <label>
                      <input name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
                      Remember Me
                    </label>
                    <span style="float:right;">
                      <a href="{{ route('password.request') }}">Forgot Password?</a>
                    </span>
                  </div>
                </form>
              </div>
            </div>

            {{-- REGISTER --}}
            <div id="cd-signup" class="{{ $selectedForm === 'register' ? 'is-selected' : '' }}">
              <div class="page-login-form register">
                <form method="POST" action="{{ route('register') }}">
                  @csrf
                  <input type="hidden" name="_form" value="register">

                  <div class="form-group">
                    <div class="input-icon">
                      <i class="ti-user"></i>
                      <input type="text" class="form-control" name="name"
                             value="{{ old('name') }}" placeholder="Full Name" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="input-icon">
                      <i class="ti-email"></i>
                      <input type="email" class="form-control" name="email"
                             value="{{ old('email') }}" placeholder="Email Address" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="input-icon">
                      <i class="ti-lock"></i>
                      <input id="register-password" type="password" class="form-control" name="password"
                             placeholder="Password (min 8 characters)" required>
                    </div>
                    <label class="checkbox" style="margin-top:8px;">
                      <input type="checkbox" data-password-toggle="register-password"> Show password
                    </label>
                  </div>

                  <div class="form-group">
                    <div class="input-icon">
                      <i class="ti-lock"></i>
                      <input id="register-password-confirmation" type="password" class="form-control" name="password_confirmation"
                             placeholder="Confirm Password" required>
                    </div>
                  </div>

                  {{-- ROLE SELECTION --}}
                  <div class="form-group">
                    <label style="display:block; margin-bottom:8px; font-weight:600;">
                      I want to: <span class="text-danger">*</span>
                    </label>
                    <div style="display:flex; gap:10px;">
                      <label style="flex:1; border:2px solid {{ $selectedRole === 'candidate' ? '#e84646' : '#ddd' }}; border-radius:6px; padding:12px; text-align:center; cursor:pointer;">
                        <input type="radio" name="role" value="candidate"
                               {{ $selectedRole === 'candidate' ? 'checked' : '' }}
                               style="margin-right:5px;">
                        <i class="ti-search"></i><br>
                        <strong>Find a Job</strong><br>
                        <small class="text-muted">Candidate</small>
                      </label>
                      <label style="flex:1; border:2px solid {{ $selectedRole === 'employer' ? '#e84646' : '#ddd' }}; border-radius:6px; padding:12px; text-align:center; cursor:pointer;">
                        <input type="radio" name="role" value="employer"
                               {{ $selectedRole === 'employer' ? 'checked' : '' }}
                               style="margin-right:5px;">
                        <i class="ti-briefcase"></i><br>
                        <strong>Hire Talent</strong><br>
                        <small class="text-muted">Employer</small>
                      </label>
                    </div>
                    @error('role') <span class="text-danger small">{{ $message }}</span> @enderror
                  </div>

                  <button type="submit" class="btn btn-common log-btn">Create Account</button>
                </form>
              </div>
            </div>

            @endif
          </div>{{-- end my-account-form --}}
        @endauth

      </div>
    </div>
  </div>
</div>

@include('partials.footer')

@endsection

@push('scripts')
<script>
  document.querySelectorAll('[data-password-toggle]').forEach(function (toggle) {
    toggle.addEventListener('change', function () {
      var input = document.getElementById(toggle.dataset.passwordToggle);
      if (input) input.type = toggle.checked ? 'text' : 'password';
    });
  });
</script>
@endpush
