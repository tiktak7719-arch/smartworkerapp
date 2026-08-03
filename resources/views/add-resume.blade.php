@extends('layouts.app')

@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="breadcrumb-wrapper">
      <h2 class="product-title">My Profile</h2>
      <ol class="breadcrumb">
        <li><a href="{{ route('home') }}"><i class="ti-home"></i> Home</a></li>
        <li class="current">My Profile</li>
      </ol>
    </div>
  </div>
</div>

<div id="content">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-sm-4">
        <div class="right-sideabr">
          <div class="inner-box">
            <h4>My Account</h4>
            <ul class="lest item">
              @if ($user->isEmployer() || $user->isAdmin())
                <li><a href="{{ route('manage-jobs') }}"><i class="ti-list"></i> My Jobs</a></li>
                <li><a href="{{ route('manage-applications') }}"><i class="ti-email"></i> Applications</a></li>
              @else
                <li><a href="{{ route('jobs.index') }}"><i class="ti-search"></i> Browse Jobs</a></li>
                <li><a href="{{ route('saved-jobs') }}"><i class="ti-heart"></i> Saved Jobs</a></li>
                <li><a href="{{ route('my-applications') }}"><i class="ti-email"></i> My Applications</a></li>
              @endif
              <li class="active"><a href="{{ route('add-resume') }}"><i class="ti-user"></i> My Profile</a></li>
              <li><a href="{{ route('change-password') }}"><i class="ti-lock"></i> Change Password</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-9 col-sm-8">
        <div class="inner-box">
          <h3>Update Your Profile</h3>
          <p class="text-muted">Account type: <strong>{{ ucfirst($user->role) }}</strong></p>

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

          <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            <h4>Basic Information</h4>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="name">Full Name <span class="text-danger">*</span></label>
                  <input id="name" class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Email Address</label>
                  <input class="form-control" type="email" value="{{ $user->email }}" disabled>
                  <small class="text-muted">Email address cannot be changed here.</small>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input id="phone" class="form-control" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="location">Location</label>
                  <input id="location" class="form-control" type="text" name="location" value="{{ old('location', $user->location) }}">
                  <input id="profile-latitude" type="hidden" name="latitude" value="{{ old('latitude', $user->latitude) }}">
                  <input id="profile-longitude" type="hidden" name="longitude" value="{{ old('longitude', $user->longitude) }}">
                  <button id="profile-location-button" type="button" class="btn btn-default btn-xs" style="margin-top:6px;"><i class="ti-location-pin"></i> {{ __('app.use_my_location') }}</button>
                  <small id="profile-location-status" class="text-muted" style="display:block;margin-top:4px;">{{ __('app.location_saved') }}</small>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="bio">About You</label>
              <textarea id="bio" class="form-control" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
            </div>
            <div class="form-group">
              <label for="avatar">Profile Photo</label>
              @if ($user->avatar)
                <div style="margin-bottom:8px;"><img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="height:50px;width:50px;border-radius:50%;object-fit:cover;"></div>
              @endif
              <input id="avatar" class="form-control" type="file" name="avatar" accept="image/*">
            </div>

            @if ($user->isEmployer() || $user->isAdmin())
              <h4 style="margin-top:28px;">Company Information</h4>
              <div class="row">
                <div class="col-sm-6"><div class="form-group"><label for="company_name">Company Name</label><input id="company_name" class="form-control" type="text" name="company_name" value="{{ old('company_name', $profile->company_name) }}"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="company_website">Company Website</label><input id="company_website" class="form-control" type="url" name="company_website" value="{{ old('company_website', $profile->company_website) }}"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="industry">Industry</label><input id="industry" class="form-control" type="text" name="industry" value="{{ old('industry', $profile->industry) }}"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="company_size">Company Size</label><input id="company_size" class="form-control" type="text" name="company_size" value="{{ old('company_size', $profile->company_size) }}" placeholder="e.g. 11–50 employees"></div></div>
              </div>
              <div class="form-group"><label for="company_description">Company Description</label><textarea id="company_description" class="form-control" name="company_description" rows="4">{{ old('company_description', $profile->company_description) }}</textarea></div>
              <div class="form-group">
                <label for="company_logo">Company Logo</label>
                @if ($profile->company_logo)
                  <div style="margin-bottom:8px;"><img src="{{ asset('storage/' . $profile->company_logo) }}" alt="Company logo" style="height:50px;max-width:160px;object-fit:contain;"></div>
                @endif
                <input id="company_logo" class="form-control" type="file" name="company_logo" accept="image/*">
              </div>
            @else
              <h4 style="margin-top:28px;">Professional Information</h4>
              <div class="row">
                <div class="col-sm-6"><div class="form-group"><label for="skills">Skills</label><input id="skills" class="form-control" type="text" name="skills" value="{{ old('skills', $profile->skills) }}" placeholder="e.g. Laravel, React, Figma"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="experience_years">Experience</label><input id="experience_years" class="form-control" type="text" name="experience_years" value="{{ old('experience_years', $profile->experience_years) }}" placeholder="e.g. 3 years"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="education">Education</label><input id="education" class="form-control" type="text" name="education" value="{{ old('education', $profile->education) }}"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="linkedin">LinkedIn URL</label><input id="linkedin" class="form-control" type="url" name="linkedin" value="{{ old('linkedin', $profile->linkedin) }}"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="github">GitHub URL</label><input id="github" class="form-control" type="url" name="github" value="{{ old('github', $profile->github) }}"></div></div>
                <div class="col-sm-6"><div class="form-group"><label for="portfolio">Portfolio URL</label><input id="portfolio" class="form-control" type="url" name="portfolio" value="{{ old('portfolio', $profile->portfolio) }}"></div></div>
              </div>
              <div class="form-group">
                <label for="resume">Resume (PDF, DOC, or DOCX — maximum 2 MB)</label>
                @if ($profile->resume_path)
                  <p><a href="{{ route('profiles.resume.download', $profile) }}" target="_blank" rel="noopener"><i class="ti-file"></i> {{ $profile->resume_original_name ?: 'View current resume' }}</a></p>
                @endif
                <input id="resume" class="form-control" type="file" name="resume" accept=".pdf,.doc,.docx" data-max-size="2097152">
                <small class="text-muted">After saving, you will see the uploaded file name here.</small>
              </div>
              <div class="checkbox"><input type="hidden" name="job_alerts_enabled" value="0"><label><input type="checkbox" name="job_alerts_enabled" value="1" @checked(old('job_alerts_enabled', $user->job_alerts_enabled))> Send me smart job-match alerts</label></div>
            @endif

            <button type="submit" class="btn btn-common" style="margin-top:12px;"><i class="ti-save"></i> Save Profile</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@include('partials.footer')

@endsection

@push('scripts')
<script>
  (function () {
    var button = document.getElementById('profile-location-button'); if (!button || !navigator.geolocation) return;
    button.addEventListener('click', function () {
      var status = document.getElementById('profile-location-status'); status.textContent = 'Getting your location...';
      navigator.geolocation.getCurrentPosition(function (position) {
        document.getElementById('profile-latitude').value = position.coords.latitude.toFixed(7);
        document.getElementById('profile-longitude').value = position.coords.longitude.toFixed(7);
        status.textContent = 'Coordinates added. Save your profile to use distance matching.';
      }, function () { status.textContent = 'Location permission was not granted. You can still enter a location manually.'; });
    });
  }());
</script>
@endpush
