@extends('layouts.app')

@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
    <div class="container">
        <h2>Browse Categories</h2>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <span>Browse Categories</span>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Job Categories</h2>
            <p>Only currently active jobs are counted below.</p>
        </div>

        <div class="row">
            @forelse ($categories as $category)
                <div class="col-md-3 col-sm-4 col-xs-12 f-category">
                    <a href="{{ route('jobs.index', ['category' => $category->slug]) }}">
                        <div class="category-box">
                            <div class="category-icon">
                                <i class="{{ $category->icon ?: 'ti-briefcase' }}"></i>
                            </div>
                            <h3>{{ $category->name }}</h3>
                            <p>{{ $category->active_jobs_count }} {{ Str::plural('Job', $category->active_jobs_count) }}</p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-xs-12">
                    <div class="alert alert-info text-center">No active categories are available yet.</div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            @php($liveJobCount = $categories->sum('active_jobs_count'))
            <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                Browse All {{ $liveJobCount }} {{ Str::plural('Job', $liveJobCount) }}
            </a>
        </div>
    </div>
</section>

@include('partials.footer')

@endsection
