@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">Employers</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">Employers</li></ol></div></div></div>

<section class="section"><div class="container"><div class="row">
  @forelse ($employers as $employer)
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="inner-box text-center" style="min-height:265px;margin-bottom:25px;">
        <img src="{{ $employer->profile?->company_logo ? asset('storage/' . $employer->profile->company_logo) : ($employer->avatar ? asset('storage/' . $employer->avatar) : asset('assets/img/clients/img1.png')) }}" alt="{{ $employer->name }}" style="height:70px;max-width:110px;object-fit:contain;">
        <h4 style="margin:15px 0 5px;">{{ $employer->profile?->company_name ?: $employer->name }}</h4>
        <p class="text-muted">{{ $employer->profile?->industry ?: 'Employer' }}</p>
        <p>{{ Str::limit($employer->profile?->company_description ?: $employer->bio ?: 'Company profile in progress.', 125) }}</p>
        <a class="btn btn-common btn-sm" href="{{ route('jobs.index', ['search' => $employer->profile?->company_name ?: $employer->name]) }}">{{ $employer->active_jobs_count }} Open {{ Str::plural('Job', $employer->active_jobs_count) }}</a>
      </div>
    </div>
  @empty
    <div class="col-xs-12"><div class="alert alert-info text-center">No company profiles are available yet.</div></div>
  @endforelse
</div>@if ($employers->hasPages())<div class="text-center">{{ $employers->links() }}</div>@endif</div></section>

@include('partials.footer')
@endsection
