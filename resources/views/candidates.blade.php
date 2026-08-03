@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">Candidates</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">Candidates</li></ol></div></div></div>

<section class="section"><div class="container">
  <form method="GET" class="inner-box" style="margin-bottom:25px;"><div class="row"><div class="col-md-4"><input class="form-control" name="skill" value="{{ $filters['skill'] ?? '' }}" placeholder="Skill, e.g. electrician"></div><div class="col-md-4"><input class="form-control" name="location" value="{{ $filters['location'] ?? '' }}" placeholder="Location"></div><div class="col-md-2"><select name="min_rating" class="form-control"><option value="">Any rating</option>@foreach([3,4,5] as $rating)<option value="{{ $rating }}" @selected((string)($filters['min_rating'] ?? '') === (string)$rating)>{{ $rating }}+ stars</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-common" style="width:100%;" type="submit"><i class="ti-search"></i> Find workers</button></div></div></form>
  <div class="row">
  @forelse ($candidates as $candidate)
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="inner-box text-center" style="min-height:250px;margin-bottom:25px;">
        <img src="{{ $candidate->user->avatar ? asset('storage/' . $candidate->user->avatar) : asset('assets/img/jobs/avatar.jpg') }}" alt="{{ $candidate->user->name }}" style="height:75px;width:75px;border-radius:50%;object-fit:cover;">
        <h4 style="margin:15px 0 5px;">{{ $candidate->user->name }}</h4>
        <p class="text-muted">{{ $candidate->user->location ?: 'Location not specified' }}</p>
        <p>{{ $candidate->skills ?: 'Professional profile in progress.' }}</p>
        @if ($candidate->experience_years)<span class="label label-default">{{ $candidate->experience_years }} experience</span>@endif
        <p style="margin:12px 0 0;"><i class="ti-star" style="color:#f5a623;"></i> {{ number_format((float)($candidate->user_ratings_received_avg_score ?? 0), 1) }} rating</p>
      </div>
    </div>
  @empty
    <div class="col-xs-12"><div class="alert alert-info text-center">No candidate profiles are available yet.</div></div>
  @endforelse
</div>@if ($candidates->hasPages())<div class="text-center">{{ $candidates->links() }}</div>@endif</div></section>

@include('partials.footer')
@endsection
