@extends('layouts.app')

@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
    <div class="container">
        <h2>Browse Resumes</h2>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <span>Browse Resumes</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="text-center mb-5">
          <h2>Candidate Resumes</h2>
          <p>Find active candidates by skill, location, rating, and distance. Resume files stay private unless you open one.</p>
        </div>

        <form method="GET" action="{{ route('browse-resumes') }}" class="inner-box" style="margin-bottom:25px;">
          <div class="row">
            <div class="col-md-3 col-sm-6"><input class="form-control" name="skill" value="{{ $filters['skill'] ?? '' }}" placeholder="Skill, e.g. electrician"></div>
            <div class="col-md-3 col-sm-6"><input class="form-control" name="location" value="{{ $filters['location'] ?? '' }}" placeholder="Location"></div>
            <div class="col-md-2 col-sm-4"><select name="min_rating" class="form-control"><option value="">Any rating</option>@foreach([3,4,5] as $rating)<option value="{{ $rating }}" @selected((string)($filters['min_rating'] ?? '') === (string)$rating)>{{ $rating }}+ stars</option>@endforeach</select></div>
            <div class="col-md-2 col-sm-4"><select name="radius" class="form-control"><option value="">Any distance</option>@foreach([10,30,60,120] as $radius)<option value="{{ $radius }}" @selected((string)($filters['radius'] ?? '') === (string)$radius)>Within {{ $radius }} km</option>@endforeach</select></div>
            <div class="col-md-2 col-sm-4"><button class="btn btn-common" style="width:100%;" type="submit"><i class="ti-search"></i> Find workers</button></div>
          </div>
          @if ($employerHasLocation = auth()->user()->latitude !== null && auth()->user()->longitude !== null)
            <small class="text-muted" style="display:block;margin-top:10px;">Distance is calculated from the coordinates in your company profile.</small>
          @else
            <small class="text-muted" style="display:block;margin-top:10px;">Save your company coordinates in your profile to use the distance filter.</small>
          @endif
        </form>

        @forelse ($profiles as $profile)
            <div class="job-list resume-listing">
                <div class="thumb">
                    @if ($profile->user->avatar)
                        <img src="{{ asset('storage/' . $profile->user->avatar) }}" alt="{{ $profile->user->name }}">
                    @else
                        <img src="{{ asset('assets/img/jobs/avatar.jpg') }}" alt="{{ $profile->user->name }}">
                    @endif
                </div>

                <div class="job-list-content">
                    <h4>{{ $profile->user->name }}</h4>
                    <p class="mb-2"><i class="ti-star" style="color:#f5a623;"></i> {{ number_format($profile->average_rating, 1) }} rating</p>
                    <p class="mb-2">
                        {{ $profile->skills ?: 'Candidate resume' }}
                    </p>
                    <ul class="mt-2">
                        @if ($profile->user->location)
                            <li><i class="ti-location-pin"></i> {{ $profile->user->location }}</li>
                        @endif
                        @if ($profile->distance_km !== null)
                            <li><i class="ti-map-alt"></i> {{ $profile->distance_km }} km away</li>
                        @endif
                        @if ($profile->experience_years)
                            <li><i class="ti-briefcase"></i> {{ $profile->experience_years }} experience</li>
                        @endif
                        @if ($profile->education)
                            <li><i class="ti-book"></i> {{ $profile->education }}</li>
                        @endif
                    </ul>
                </div>

                <div class="job-tag">
                    <a class="btn btn-primary" href="{{ route('profiles.resume.download', $profile) }}" target="_blank" rel="noopener">
                        View Resume
                    </a>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                No candidate resumes have been uploaded yet.
            </div>
        @endforelse

        @if ($profiles->hasPages())
            <div class="text-center mt-5">
                {{ $profiles->links() }}
            </div>
        @endif
    </div>
</section>

@include('partials.footer')

@endsection
