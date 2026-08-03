@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">My Applications</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">My Applications</li></ol></div></div></div>

<section class="section"><div class="container"><div class="row">
  <div class="col-md-3 col-sm-4"><div class="right-sideabr"><div class="inner-box"><h4>Candidate Menu</h4><ul class="lest item"><li><a href="{{ route('candidate.dashboard') }}">Dashboard</a></li><li><a href="{{ route('jobs.index') }}">Browse Jobs</a></li><li><a href="{{ route('saved-jobs') }}">Saved Jobs</a></li><li class="active"><a href="{{ route('my-applications') }}">My Applications</a></li><li><a href="{{ route('profile') }}">Profile</a></li></ul></div></div></div>
  <div class="col-md-9 col-sm-8"><div class="inner-box"><h3>Application History</h3><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Job</th><th>Company</th><th>Applied</th><th>CV</th><th>Status</th><th>Action</th></tr></thead><tbody>
    @forelse ($applications as $application)
      <tr><td><a href="{{ route('jobs.show', $application->job->slug) }}">{{ $application->job->title }}</a></td><td>{{ $application->job->company }}</td><td>{{ $application->created_at->format('d M Y') }}</td><td>@if ($application->cv_path)<a class="btn btn-default btn-xs" href="{{ route('applications.cv.download', $application) }}"><i class="ti-download"></i> Download</a>@else <span class="text-muted">Not available</span>@endif</td><td>{!! $application->status_badge !!}<br><small class="text-muted">{{ $application->status_message }}</small>@if($application->employer_notes)<br><small><strong>Employer note:</strong> {{ $application->employer_notes }}</small>@endif</td><td>@if($application->status === 'hired')<button class="btn btn-default btn-xs" type="button" data-toggle="collapse" data-target="#rate-{{ $application->id }}"><i class="ti-star"></i> Rate employer</button>@else <a class="btn btn-default btn-xs" href="{{ route('notifications.index') }}"><i class="ti-bell"></i> Alerts</a>@endif</td></tr>
      @if($application->status === 'hired')<tr id="rate-{{ $application->id }}" class="collapse"><td colspan="6"><form method="POST" action="{{ route('ratings.store', $application) }}" class="form-inline">@csrf <strong>Rate {{ $application->job->company }}:</strong> <select class="form-control input-sm" name="score">@foreach([5,4,3,2,1] as $score)<option value="{{ $score }}">{{ $score }} stars</option>@endforeach</select> <input class="form-control input-sm" name="comment" maxlength="1000" placeholder="Optional feedback"> <button class="btn btn-common btn-sm">Save rating</button></form></td></tr>@endif
    @empty
      <tr><td colspan="6" class="text-center">You have not submitted any applications yet.</td></tr>
    @endforelse
  </tbody></table></div>@if ($applications->hasPages())<div class="text-center">{{ $applications->links() }}</div>@endif</div></div>
</div></div></section>

@include('partials.footer')
@endsection
