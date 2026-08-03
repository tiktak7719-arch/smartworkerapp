@extends('layouts.app')
@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-wrapper">
          <h2 class="product-title">Saved Jobs</h2>
          <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="ti-home"></i> Home</a></li>
            <li class="current">Saved Jobs</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="content">
  <div class="container">
    <div class="row">

      {{-- Sidebar --}}
      <div class="col-md-3 col-sm-4">
        <div class="right-sideabr">
          <div class="inner-box">
            <h4>My Account</h4>
            <ul class="lest item">
              <li><a href="{{ route('jobs.index') }}"><i class="ti-search"></i> Browse Jobs</a></li>
              <li class="active"><a href="{{ route('saved-jobs') }}"><i class="ti-heart"></i> Saved Jobs</a></li>
              <li><a href="{{ route('my-applications') }}"><i class="ti-email"></i> My Applications</a></li>
              <li><a href="{{ route('add-resume') }}"><i class="ti-user"></i> My Profile</a></li>
              <li><a href="{{ route('change-password') }}"><i class="ti-lock"></i> Change Password</a></li>
            </ul>
          </div>
          @auth
          <div class="inner-box" style="margin-top:20px;">
            <h4>Quick Stats</h4>
            <ul class="lest item">
              <li>Saved Jobs: <strong>{{ \App\Models\SavedJob::where('user_id', Auth::id())->count() }}</strong></li>
              <li>Applied Jobs: <strong>{{ \App\Models\JobApplication::where('user_id', Auth::id())->count() }}</strong></li>
            </ul>
          </div>
          @endauth
        </div>
      </div>

      {{-- Main Content --}}
      <div class="col-md-9 col-sm-8">

        @if(!Auth::check())
          <div class="alert alert-warning">
            Please <a href="{{ route('my-account') }}">login</a> to view your saved jobs.
          </div>
        @else

          @if(session('success'))
            <div class="alert alert-success"><i class="ti-check"></i> {{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <h3>Your Saved Jobs</h3>

          @if(isset($savedJobs) && $savedJobs->count() > 0)
            @foreach($savedJobs as $saved)
              @php $job = $saved->job; @endphp
              @if($job)
              <div class="job-list">
                <div class="thumb">
                  <a href="{{ route('jobs.show', $job->slug) }}">
                    @if($job->company_logo)
                      <img src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company }}" style="object-fit:contain;">
                    @else
                      <img src="{{ asset('assets/img/jobs/img-1.jpg') }}" alt="{{ $job->company }}">
                    @endif
                  </a>
                </div>
                <div class="job-list-content">
                  <h4>
                    <a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a>
                    <span class="full-time">{{ ucfirst(str_replace('-',' ',$job->job_type)) }}</span>
                    @if(!$job->is_active)
                      <span class="label label-danger" style="margin-left:5px;">Closed</span>
                    @endif
                  </h4>
                  <p>{{ Str::limit($job->description, 130) }}</p>
                  <div class="job-tag">
                    <div class="pull-left">
                      <div class="meta-tag">
                        @if($job->location)
                          <span><i class="ti-location-pin"></i>{{ $job->location }}</span>
                        @endif
                        @if($job->salary_display)
                          <span><i class="ti-money"></i>{{ $job->salary_display }}</span>
                        @endif
                        <span><i class="ti-time"></i>Saved {{ $saved->created_at->diffForHumans() }}</span>
                      </div>
                    </div>
                    <div class="pull-right" style="display:flex; gap:5px;">
                      <a href="{{ route('jobs.show', $job->slug) }}" class="btn btn-common btn-rm">View Job</a>
                      <form method="POST" action="{{ route('jobs.save', $job->slug) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" title="Remove">
                          <i class="ti-trash"></i>
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              @endif
            @endforeach

            @if($savedJobs->hasPages())
              <div style="margin-top:20px;">{{ $savedJobs->links() }}</div>
            @endif
          @else
            <div class="alert alert-info text-center" style="padding:40px;">
              <h4><i class="ti-heart"></i> No saved jobs yet</h4>
              <p>Click the heart icon on any job to save it here.</p>
              <a href="{{ route('jobs.index') }}" class="btn btn-common">Browse Jobs</a>
            </div>
          @endif

        @endif

      </div>
    </div>
  </div>
</div>

@include('partials.footer')
@endsection
