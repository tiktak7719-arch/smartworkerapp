@extends('layouts.app')

@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-wrapper">
          <h2 class="product-title">Edit Job</h2>
          <ol class="breadcrumb">
            <li><a href="{{ route('home') }}"><i class="ti-home"></i> Home</a></li>
            <li><a href="{{ route('manage-jobs') }}">Manage Jobs</a></li>
            <li class="current">Edit Job</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="content">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-sm-4">
        <div class="right-sideabr">
          <div class="inner-box">
            <h4>Employer Menu</h4>
            <ul class="lest item">
              <li><a href="{{ route('post-job') }}"><i class="ti-plus"></i> Post New Job</a></li>
              <li class="active"><a href="{{ route('manage-jobs') }}"><i class="ti-list"></i> My Jobs</a></li>
              <li><a href="{{ route('manage-applications') }}"><i class="ti-email"></i> Applications</a></li>
              <li><a href="{{ route('company.profile') }}"><i class="ti-user"></i> Company Profile</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-9 col-sm-8">
        <div class="inner-box">
          <h3>Edit: {{ $job->title }}</h3>

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('jobs.update', $job->slug) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="title">Job Title <span class="text-danger">*</span></label>
                  <input id="title" type="text" name="title" class="form-control" value="{{ old('title', $job->title) }}" required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="company">Company <span class="text-danger">*</span></label>
                  <input id="company" type="text" name="company" class="form-control" value="{{ old('company', $job->company) }}" required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="category_id">Category</label>
                  <select id="category_id" name="category_id" class="form-control">
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}" @selected((string) old('category_id', $job->category_id) === (string) $category->id)>
                        {{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="job_type">Job Type <span class="text-danger">*</span></label>
                  <select id="job_type" name="job_type" class="form-control" required>
                    @foreach (['full-time' => 'Full Time', 'part-time' => 'Part Time', 'remote' => 'Remote', 'freelance' => 'Freelance', 'internship' => 'Internship'] as $value => $label)
                      <option value="{{ $value }}" @selected(old('job_type', $job->job_type) === $value)>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="location">Location</label>
                  <input id="location" type="text" name="location" class="form-control" value="{{ old('location', $job->location) }}">
                  <input id="job-latitude" type="hidden" name="latitude" value="{{ old('latitude', $job->latitude) }}">
                  <input id="job-longitude" type="hidden" name="longitude" value="{{ old('longitude', $job->longitude) }}">
                  <button id="job-location-button" type="button" class="btn btn-default btn-xs" style="margin-top:6px;"><i class="ti-location-pin"></i> Update coordinates from this device</button>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="salary_min">Pay Range</label>
                  <div class="row">
                    <div class="col-xs-6"><input id="salary_min" type="number" min="0" step="1" name="salary_min" class="form-control" value="{{ old('salary_min', $job->salary_min) }}" placeholder="Minimum"></div>
                    <div class="col-xs-6"><input id="salary_max" type="number" min="0" step="1" name="salary_max" class="form-control" value="{{ old('salary_max', $job->salary_max) }}" placeholder="Maximum"></div>
                  </div>
                  <input id="salary_display" type="text" name="salary_display" class="form-control" style="margin-top:8px;" value="{{ old('salary_display', $job->salary_display) }}" placeholder="e.g. Rs. 80,000 - 120,000">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="description">Job Description <span class="text-danger">*</span></label>
              <textarea id="description" name="description" class="form-control" rows="8" required>{{ old('description', $job->description) }}</textarea>
            </div>
            <div class="form-group">
              <label for="requirements">Requirements</label>
              <textarea id="requirements" name="requirements" class="form-control" rows="4">{{ old('requirements', $job->requirements) }}</textarea>
            </div>
            <div class="form-group">
              <label for="benefits">Benefits</label>
              <textarea id="benefits" name="benefits" class="form-control" rows="4">{{ old('benefits', $job->benefits) }}</textarea>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="application_email">Application Email</label>
                  <input id="application_email" type="email" name="application_email" class="form-control" value="{{ old('application_email', $job->application_email) }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="application_url">Application URL</label>
                  <input id="application_url" type="url" name="application_url" class="form-control" value="{{ old('application_url', $job->application_url) }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="website">Company Website</label>
                  <input id="website" type="url" name="website" class="form-control" value="{{ old('website', $job->website) }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="closing_date">Closing Date</label>
                  <input id="closing_date" type="date" name="closing_date" class="form-control" value="{{ old('closing_date', $job->closing_date?->format('Y-m-d')) }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="tagline">Company Tagline</label>
                  <input id="tagline" type="text" name="tagline" class="form-control" value="{{ old('tagline', $job->tagline) }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="tags">Tags</label>
                  <input id="tags" type="text" name="tags" class="form-control" value="{{ old('tags', $job->tags) }}" placeholder="Laravel, PHP, Remote">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="company_logo">Company Logo</label>
              @if ($job->company_logo)
                <div style="margin: 0 0 8px;">
                  <img src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company }}" style="height: 48px; max-width: 160px; object-fit: contain;">
                </div>
              @endif
              <input id="company_logo" type="file" name="company_logo" accept="image/*" class="form-control">
              <small class="text-muted">Optional. JPG, PNG, GIF, or WebP — maximum 2 MB.</small>
            </div>

            <div class="checkbox">
              <input type="hidden" name="is_active" value="0">
              <label>
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $job->is_active))>
                Keep this job listing active
              </label>
            </div>

            <div style="margin-top: 24px;">
              <button type="submit" class="btn btn-common"><i class="ti-save"></i> Save Changes</button>
              <a href="{{ route('manage-jobs') }}" class="btn btn-default">Cancel</a>
            </div>
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
    var button = document.getElementById('job-location-button'); if (!button || !navigator.geolocation) return;
    button.addEventListener('click', function () {
      navigator.geolocation.getCurrentPosition(function (position) {
        document.getElementById('job-latitude').value = position.coords.latitude.toFixed(7);
        document.getElementById('job-longitude').value = position.coords.longitude.toFixed(7);
        button.innerHTML = '<i class="ti-check"></i> Coordinates ready to save';
      });
    });
  }());
</script>
@endpush
