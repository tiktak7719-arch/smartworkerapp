@extends('layouts.app')

@section('content')

      <!-- Page Header Start -->
      <div class="page-header" style="background: url(assets/img/banner1.jpg);">
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <div class="breadcrumb-wrapper">
                <h2 class="product-title">Post A Job</h2>
                <ol class="breadcrumb">
                  <li><a href="{{ url('/') }}"><i class="ti-home"></i> Home</a></li>
                  <li class="current">Post A Job</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Page Header End -->

      <!-- Content section Start -->
      <section id="content">
        <div class="container">
          <div class="row">
            <div class="col-sm-12 col-md-9 col-md-offset-2">

              <fieldset>
                <label>Have an account?</label>
                <div class="field account-sign-in">
                  <p>
                    <a class="btn btn-common btn-sm" href="{{ route('my-account') }}"><i class="ti-key"></i> Sign in</a>
                  </p>
                  <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
                    Sign in to manage your posted jobs and track applications.
                  </div>
                </div>
              </fieldset>

              <div class="page-ads box">
                <form class="form-ad" action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf

                  {{-- Success message --}}
                  @if (session('success'))
                    <div class="alert alert-success" role="alert">
                      <i class="ti-check"></i> {{ session('success') }}
                    </div>
                  @endif

                  {{-- Validation errors --}}
                  @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                      <strong>Please fix the following errors:</strong>
                      <ul style="margin: 8px 0 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif

                  {{-- Job Title --}}
                  <div class="form-group">
                    <label class="control-label">Job Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                  </div>

                  {{-- Location --}}
                  <div class="form-group">
                    <label class="control-label">Location <span class="text-muted">(optional)</span></label>
                    <input id="job-location" type="text" name="location" value="{{ old('location') }}"
                           class="form-control" placeholder="e.g. London">
                    <input id="job-latitude" type="hidden" name="latitude" value="{{ old('latitude') }}">
                    <input id="job-longitude" type="hidden" name="longitude" value="{{ old('longitude') }}">
                    <button id="job-location-button" type="button" class="btn btn-default btn-xs" style="margin-top:6px;"><i class="ti-location-pin"></i> Use current job location</button>
                    <small id="job-location-status" class="text-muted" style="display:block;margin-top:4px;">Coordinates enable distance matching and the map link.</small>
                  </div>

                  {{-- Job Type --}}
                  <div class="form-group">
                    <label class="control-label">Job Type <span class="text-danger">*</span></label>
                    <select name="job_type" class="form-control @error('job_type') is-invalid @enderror" required>
                      <option value="">-- Select Job Type --</option>
                      <option value="full-time"   {{ old('job_type') === 'full-time'   ? 'selected' : '' }}>Full Time</option>
                      <option value="part-time"   {{ old('job_type') === 'part-time'   ? 'selected' : '' }}>Part Time</option>
                      <option value="remote"      {{ old('job_type') === 'remote'      ? 'selected' : '' }}>Remote</option>
                      <option value="freelance"   {{ old('job_type') === 'freelance'   ? 'selected' : '' }}>Freelance</option>
                      <option value="internship"  {{ old('job_type') === 'internship'  ? 'selected' : '' }}>Internship</option>
                    </select>
                    @error('job_type') <span class="text-danger small">{{ $message }}</span> @enderror
                  </div>

                  {{-- Salary --}}
                  <div class="form-group">
                    <label class="control-label">Pay Range <span class="text-muted">(optional)</span></label>
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="number" min="0" step="1" name="salary_min" value="{{ old('salary_min') }}" class="form-control" placeholder="Minimum pay">
                      </div>
                      <div class="col-sm-6">
                        <input type="number" min="0" step="1" name="salary_max" value="{{ old('salary_max') }}" class="form-control" placeholder="Maximum pay">
                      </div>
                    </div>
                    <input type="text" name="salary_display" value="{{ old('salary_display') }}" class="form-control" style="margin-top:8px;" placeholder="Display text, e.g. Rs. 80,000 - 100,000 or Negotiable">
                    <small class="text-muted">Numeric values allow candidates to filter jobs by pay.</small>
                  </div>

                  {{-- Category --}}
                  <div class="form-group">
                    <label class="control-label">Category</label>
                    <select name="category_id" class="form-control">
                      <option value="">-- Select Category --</option>
                      @isset($categories)
                        @foreach ($categories as $cat)
                          <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                          </option>
                        @endforeach
                      @else
                        @foreach (['Finance','IT & Engineering','Education/Training','Art/Design','Sale/Marketing','Healthcare','Science','Food Services'] as $catName)
                          <option value="">{{ $catName }}</option>
                        @endforeach
                      @endisset
                    </select>
                  </div>

                  {{-- Tags --}}
                  <div class="form-group">
                    <label class="control-label">Job Tags <span class="text-muted">(optional)</span></label>
                    <input type="text" name="tags" value="{{ old('tags') }}"
                           class="form-control" placeholder="e.g. PHP, Social Media, Management">
                    <p class="note">Comma-separate tags such as required skills or technologies.</p>
                  </div>

                  {{-- Description --}}
                  <div class="form-group">
                    <label class="control-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" rows="8"
                              class="form-control @error('description') is-invalid @enderror"
                              required>{{ old('description') }}</textarea>
                    @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                  </div>

                  {{-- Application destination --}}
                  <div class="form-group">
                    <label class="control-label">Application Email <span class="text-muted">(optional)</span></label>
                    <input type="email" name="application_email" value="{{ old('application_email') }}"
                           class="form-control" placeholder="hiring@company.com">
                  </div>

                  <div class="form-group">
                    <label class="control-label">Application URL <span class="text-muted">(optional)</span></label>
                    <input type="url" name="application_url" value="{{ old('application_url') }}"
                           class="form-control" placeholder="https://company.com/careers">
                  </div>

                  {{-- Closing Date --}}
                  <div class="form-group">
                    <label class="control-label">Closing Date <span class="text-muted">(optional)</span></label>
                    <input type="date" name="closing_date" value="{{ old('closing_date') }}"
                           class="form-control @error('closing_date') is-invalid @enderror">
                    @error('closing_date') <span class="text-danger small">{{ $message }}</span> @enderror
                    <p class="note">Deadline for new applicants.</p>
                  </div>

                  <div class="divider"><h3>Company Details</h3></div>

                  {{-- Company Name --}}
                  <div class="form-group">
                    <label class="control-label">Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company" value="{{ old('company') }}"
                           class="form-control @error('company') is-invalid @enderror"
                           placeholder="Enter the name of the company" required>
                    @error('company') <span class="text-danger small">{{ $message }}</span> @enderror
                  </div>

                  {{-- Tagline --}}
                  <div class="form-group">
                    <label class="control-label">Company Tagline <span class="text-muted">(optional)</span></label>
                    <input type="text" name="tagline" value="{{ old('tagline') }}"
                           class="form-control" placeholder="Briefly describe your company">
                  </div>

                  <div class="form-group">
                    <label class="control-label">Company Logo <span class="text-muted">(optional, max 2 MB)</span></label>
                    <input type="file" name="company_logo" class="form-control" accept="image/*">
                  </div>

                  <button type="submit" class="btn btn-common">Submit your job</button>
                </form>
              </div>

            </div>
          </div>
        </div>
      </section>
      <!-- Content section End -->

@endsection

@push('scripts')
<script>
  (function () {
    var button = document.getElementById('job-location-button'); if (!button || !navigator.geolocation) return;
    button.addEventListener('click', function () {
      var status = document.getElementById('job-location-status'); status.textContent = 'Getting location...';
      navigator.geolocation.getCurrentPosition(function (position) {
        document.getElementById('job-latitude').value = position.coords.latitude.toFixed(7);
        document.getElementById('job-longitude').value = position.coords.longitude.toFixed(7);
        status.textContent = 'Coordinates added. Submit the job to save them.';
      }, function () { status.textContent = 'Permission was not granted; text location will still be saved.'; });
    });
  }());
</script>
@endpush
