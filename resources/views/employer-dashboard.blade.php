@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">Employer Dashboard</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">Dashboard</li></ol></div></div></div>

<section class="section"><div class="container">
  <div class="row">
    <div class="col-md-4 col-sm-4"><div class="inner-box text-center"><i class="ti-briefcase" style="font-size:28px;"></i><h2>{{ $totalJobs }}</h2><p>Jobs Posted</p><a href="{{ route('manage-jobs') }}">Manage jobs</a></div></div>
    <div class="col-md-4 col-sm-4"><div class="inner-box text-center"><i class="ti-check-box" style="font-size:28px;"></i><h2>{{ $activeJobs }}</h2><p>Active Jobs</p><a href="{{ route('post-job') }}">Post a job</a></div></div>
    <div class="col-md-4 col-sm-4"><div class="inner-box text-center"><i class="ti-email" style="font-size:28px;"></i><h2>{{ $applicationCount }}</h2><p>Applications Received</p><a href="{{ route('manage-applications') }}">Review applications</a></div></div>
  </div>
  <div class="row" style="margin-top:30px;">
    <div class="col-md-6">
      <div class="inner-box"><h3>Recent Job Listings</h3><ul class="list-group">
        @forelse ($recentJobs as $job)<li class="list-group-item"><a href="{{ route('jobs.edit', $job->slug) }}">{{ $job->title }}</a><span class="pull-right">{{ $job->created_at->diffForHumans() }}</span></li>
        @empty <li class="list-group-item">No jobs posted yet.</li>@endforelse
      </ul></div>
    </div>
    <div class="col-md-6">
      <div class="inner-box"><h3>Recent Applications</h3><ul class="list-group">
        @forelse ($recentApplications as $application)<li class="list-group-item"><strong>{{ $application->applicant->name }}</strong> applied for <a href="{{ route('jobs.edit', $application->job->slug) }}">{{ $application->job->title }}</a></li>
        @empty <li class="list-group-item">No applications received yet.</li>@endforelse
      </ul></div>
    </div>
  </div>
</div></section>

@include('partials.footer')
@endsection
