@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">Candidate Dashboard</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">Dashboard</li></ol></div></div></div>

<section class="section"><div class="container">
  <div class="row">
    <div class="col-md-4 col-sm-4"><div class="inner-box text-center"><i class="ti-heart" style="font-size:28px;"></i><h2>{{ $savedCount }}</h2><p>Saved Jobs</p><a href="{{ route('saved-jobs') }}">View saved jobs</a></div></div>
    <div class="col-md-4 col-sm-4"><div class="inner-box text-center"><i class="ti-email" style="font-size:28px;"></i><h2>{{ $applicationCount }}</h2><p>Applications</p><a href="{{ route('my-applications') }}">View application history</a></div></div>
    <div class="col-md-4 col-sm-4"><div class="inner-box text-center"><i class="ti-time" style="font-size:28px;"></i><h2>{{ $pendingApplications }}</h2><p>Pending or Reviewing</p><a href="{{ route('profile') }}">Update profile and resume</a></div></div>
  </div>
  <div class="row" style="margin-top:20px;">
    <div class="col-md-4 col-sm-4"><div class="inner-box text-center"><i class="ti-bell" style="font-size:28px;"></i><h2>{{ $unreadNotifications }}</h2><p>Unread Alerts</p><a href="{{ route('notifications.index') }}">View notifications</a></div></div>
    <div class="col-md-8 col-sm-8">
      <div class="inner-box">
        <h3 style="margin-top:0;">{{ __('app.recommended_jobs') }}</h3>
        <p class="text-muted">Complete your skills and location to improve these scores.</p>

        @forelse ($recommendedJobs as $job)
          <div class="clearfix" style="padding:8px 0;border-top:1px solid #eee;">
            <a href="{{ route('jobs.show', $job->slug) }}"><strong>{{ $job->title }}</strong> at {{ $job->company }}</a>
            <span class="label label-success pull-right">{{ $job->match_score }}% {{ __('app.match_score') }}</span>
            <br>
            <small class="text-muted">{{ $job->location ?: 'Location not provided' }}</small>
          </div>
        @empty
          <p class="text-muted">Add skills and a location in your profile to get job recommendations.</p>
        @endforelse

        @if ($skillSuggestions->isNotEmpty())
          <div style="margin-top:14px;padding-top:10px;border-top:1px solid #eee;">
            <strong><i class="ti-light-bulb"></i> Skills in demand:</strong>
            @foreach ($skillSuggestions as $skill)
              <span class="label label-default" style="margin-left:4px;">{{ $skill }}</span>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="inner-box" style="margin-top:30px;">
    <div style="display:flex;justify-content:space-between;align-items:center;"><h3 style="margin:0;">Recent Applications</h3><a class="btn btn-common btn-sm" href="{{ route('jobs.index') }}">Find Jobs</a></div>
    <div class="table-responsive" style="margin-top:20px;">
      <table class="table table-striped"><thead><tr><th>Job</th><th>Company</th><th>Applied</th><th>Status</th></tr></thead><tbody>
        @forelse ($recentApplications as $application)
          <tr><td><a href="{{ route('jobs.show', $application->job->slug) }}">{{ $application->job->title }}</a></td><td>{{ $application->job->company }}</td><td>{{ $application->created_at->format('d M Y') }}</td><td>{!! $application->status_badge !!}</td></tr>
        @empty
          <tr><td colspan="4" class="text-center">You have not applied to any jobs yet.</td></tr>
        @endforelse
      </tbody></table>
    </div>
  </div>
</div></section>

@include('partials.footer')
@endsection
