@extends('layouts.app')
@section('content')

@include('partials.header')

<!-- Page Header -->
<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-wrapper">
          <h2 class="product-title">Manage Jobs</h2>
          <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="ti-home"></i> Home</a></li>
            <li class="current">Manage Jobs</li>
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
      <div class="col-md-3 col-sm-4 col-xs-12">
        <div class="right-sideabr">
          <div class="inner-box">
            <h4>Employer Menu</h4>
            <ul class="lest item">
              <li><a href="{{ route('post-job') }}"><i class="ti-plus"></i> Post New Job</a></li>
              <li class="active"><a href="{{ route('manage-jobs') }}"><i class="ti-list"></i> My Jobs</a></li>
              <li><a href="{{ route('manage-applications') }}"><i class="ti-email"></i> Applications</a></li>
              <li><a href="{{ route('company.profile') }}"><i class="ti-user"></i> Company Profile</a></li>
              <li><a href="{{ route('change-password') }}"><i class="ti-lock"></i> Change Password</a></li>
            </ul>
          </div>
          @if(Auth::check())
          <div class="inner-box" style="margin-top:20px;">
            <h4>Quick Stats</h4>
            <ul class="lest item">
              <li>Total Jobs: <strong>{{ \App\Models\JobListing::where('user_id', Auth::id())->count() }}</strong></li>
              <li>Active Jobs: <strong>{{ \App\Models\JobListing::where('user_id', Auth::id())->where('is_active',true)->count() }}</strong></li>
              <li>Applications: <strong>{{ \App\Models\JobApplication::whereHas('job', fn($q)=>$q->where('user_id',Auth::id()))->count() }}</strong></li>
            </ul>
          </div>
          @endif
        </div>
      </div>

      {{-- Main Content --}}
      <div class="col-md-9 col-sm-8 col-xs-12">

        @if(!Auth::check())
          <div class="alert alert-warning">
            Please <a href="{{ route('my-account') }}">login</a> to manage your jobs.
          </div>
        @else

          @if(session('success'))
            <div class="alert alert-success"><i class="ti-check"></i> {{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0;">My Job Listings</h3>
            <a href="{{ route('post-job') }}" class="btn btn-common btn-sm">
              <i class="ti-plus"></i> Post New Job
            </a>
          </div>

          @if(isset($jobs) && $jobs->count() > 0)
            @foreach($jobs as $job)
            <div class="job-list" style="position:relative;">
              <div class="thumb">
                @if($job->company_logo)
                  <img src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company }}" style="object-fit:contain;">
                @else
                  <img src="{{ asset('assets/img/jobs/img-1.jpg') }}" alt="{{ $job->company }}">
                @endif
              </div>
              <div class="job-list-content">
                <h4>
                  <a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a>
                  @if($job->is_active)
                    <span class="label label-success" style="margin-left:5px;">Active</span>
                  @else
                    <span class="label label-danger" style="margin-left:5px;">Inactive</span>
                  @endif
                  @if($job->is_featured)
                    <span class="label label-warning" style="margin-left:3px;">Featured</span>
                  @endif
                </h4>
                <p>{{ Str::limit($job->description, 120) }}</p>
                <div class="job-tag">
                  <div class="pull-left">
                    <div class="meta-tag">
                      @if($job->location)
                        <span><i class="ti-location-pin"></i>{{ $job->location }}</span>
                      @endif
                      @if($job->salary_display)
                        <span><i class="ti-money"></i>{{ $job->salary_display }}</span>
                      @endif
                      <span><i class="ti-time"></i>{{ ucfirst(str_replace('-',' ',$job->job_type)) }}</span>
                      <span><i class="ti-email"></i>
                        {{ $job->applications()->count() }} application(s)
                      </span>
                      <span><i class="ti-calendar"></i>Posted {{ $job->created_at->diffForHumans() }}</span>
                    </div>
                  </div>
                  <div class="pull-right" style="display:flex; gap:5px; flex-wrap:wrap; justify-content:flex-end;">
                    <a href="{{ route('jobs.edit', $job->slug) }}" class="btn btn-common btn-sm">
                      <i class="ti-pencil"></i> Edit
                    </a>
                    <a href="{{ route('jobs.matches', $job->slug) }}" class="btn btn-default btn-sm">
                      <i class="ti-target"></i> Smart Matches
                    </a>
                    <form method="POST" action="{{ route('jobs.destroy', $job->slug) }}"
                          onsubmit="return confirm('Delete this job permanently?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm">
                        <i class="ti-trash"></i> Delete
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            @endforeach

            @if($jobs->hasPages())
              <div style="margin-top:20px;">{{ $jobs->links() }}</div>
            @endif

          @else
            <div class="alert alert-info text-center" style="padding:40px;">
              <h4><i class="ti-briefcase"></i> No jobs posted yet</h4>
              <p>Start hiring by posting your first job.</p>
              <a href="{{ route('post-job') }}" class="btn btn-common">
                <i class="ti-plus"></i> Post Your First Job
              </a>
            </div>
          @endif

        @endif

      </div>{{-- end col-md-9 --}}
    </div>
  </div>
</div>

@include('partials.footer')

@endsection
