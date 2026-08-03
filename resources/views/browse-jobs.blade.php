@extends('layouts.app')
@section('content')

@include('partials.header')

<!-- Page Header -->
<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-wrapper">
          <h2 class="product-title">Browse Jobs</h2>
          <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="ti-home"></i> Home</a></li>
            <li class="current">Browse Jobs</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Search Bar -->
<div style="background:#f8f9fa; padding: 20px 0; border-bottom:1px solid #e5e5e5;">
  <div class="container">
    <form method="GET" action="{{ route('jobs.index') }}">
      <div class="row">
        <div class="col-md-4 col-sm-6">
          <div class="form-group" style="margin-bottom:0;">
            <input id="job-search" type="text" name="search" class="form-control"
                   placeholder="Job title, keyword, company..."
                   value="{{ $filters['search'] ?? '' }}">
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="form-group" style="margin-bottom:0;">
            <input type="text" name="location" class="form-control"
                   placeholder="Location..."
                   value="{{ $filters['location'] ?? '' }}">
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <select name="job_type" class="form-control">
            <option value="">All Types</option>
            @foreach(['full-time' => 'Full Time','part-time' => 'Part Time','remote' => 'Remote','freelance' => 'Freelance','internship' => 'Internship'] as $val => $label)
              <option value="{{ $val }}" {{ ($filters['job_type'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 col-sm-6">
          <select name="category" class="form-control">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->slug }}" {{ ($filters['category'] ?? '') === $cat->slug ? 'selected' : '' }}>
                {{ $cat->name }} ({{ $cat->active_jobs_count }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1 col-sm-6">
          <button type="submit" class="btn btn-common" style="width:100%;">
            <i class="ti-search"></i>
          </button>
        </div>
      </div>
      <div class="row" style="margin-top:10px;">
        <div class="col-md-3 col-sm-6">
          <input type="number" min="0" step="1" name="min_salary" class="form-control" placeholder="Minimum pay"
                 value="{{ $filters['min_salary'] ?? '' }}" aria-label="Minimum pay">
        </div>
        <div class="col-md-3 col-sm-6">
          <input type="number" min="0" step="1" name="max_salary" class="form-control" placeholder="Maximum pay"
                 value="{{ $filters['max_salary'] ?? '' }}" aria-label="Maximum pay">
        </div>
        <div class="col-md-6 col-sm-12"><small class="text-muted">Pay filters use the numeric minimum and maximum entered when a job is posted.</small></div>
      </div>
      @auth
        @if(auth()->user()->isCandidate())
          <div class="row" style="margin-top:10px;">
            <div class="col-md-3 col-sm-6"><select name="radius" class="form-control"><option value="">Any distance</option>@foreach([10,30,60,120] as $radius)<option value="{{ $radius }}" @selected((string)($filters['radius'] ?? '') === (string)$radius)>Within {{ $radius }} km</option>@endforeach</select></div>
            <div class="col-md-6 col-sm-6"><small class="text-muted">Distance search uses the location coordinates saved in your profile and on each job.</small></div>
          </div>
        @endif
      @endauth
      @if(array_filter($filters ?? []))
        <div style="margin-top:10px;">
          <a href="{{ route('jobs.index') }}" class="btn btn-default btn-sm">
            <i class="ti-close"></i> Clear Filters
          </a>
          <span class="text-muted" style="margin-left:10px;">
            {{ $jobs->total() }} job(s) found
          </span>
        </div>
      @endif
    </form>
  </div>
</div>

<!-- Jobs Section -->
<section class="job-browse section">
  <div class="container">
    <div class="row">

      {{-- Job Listings --}}
      <div class="col-md-9 col-sm-8">

        @php
          $jobImages = [
            asset('assets/img/jobs/img-1.jpg'),
            asset('assets/img/jobs/img-2.jpg'),
            asset('assets/img/jobs/img-3.jpg'),
            asset('assets/img/jobs/img-4.jpg'),
          ];
          $typeColors = [
            'full-time'  => 'full-time',
            'part-time'  => 'part-time',
            'remote'     => 'full-time',
            'freelance'  => 'part-time',
            'internship' => 'part-time',
          ];
        @endphp

        @forelse ($jobs as $i => $job)
        <div class="job-list">
          <div class="thumb">
            <a href="{{ route('jobs.show', $job->slug) }}">
              @if ($job->company_logo)
                <img src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company }}" style="object-fit:contain;">
              @else
                <img src="{{ $jobImages[$i % count($jobImages)] }}" alt="{{ $job->company }}">
              @endif
            </a>
          </div>
          <div class="job-list-content">
            <h4>
              <a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a>
              <span class="{{ $typeColors[$job->job_type] ?? 'full-time' }}">
                {{ ucfirst(str_replace('-', ' ', $job->job_type)) }}
              </span>
              @if ($job->is_featured)
                <span class="label label-warning" style="margin-left:5px;">Featured</span>
              @endif
            </h4>
            <p>{{ Str::limit($job->description, 160) }}</p>
            <div class="job-tag">
              <div class="pull-left">
                <div class="meta-tag">
                  @if ($job->category)
                    <span><a href="{{ route('jobs.index', ['category' => $job->category->slug]) }}">
                      <i class="{{ $job->category->icon ?? 'ti-briefcase' }}"></i>{{ $job->category->name }}
                    </a></span>
                  @endif
                  @if ($job->location)
                    <span><i class="ti-location-pin"></i>{{ $job->location }}</span>
                  @endif
                  @if ($job->salary_display)
                    <span><i class="ti-money"></i>{{ $job->salary_display }}</span>
                  @endif
                  @if ($job->closing_date)
                    <span><i class="ti-calendar"></i>
                      {{ $job->closing_date->isPast() ? 'Closed' : 'Closes ' . $job->closing_date->format('d M Y') }}
                    </span>
                  @endif
                </div>
              </div>
              <div class="pull-right">
                @auth
                  @if (auth()->user()->isCandidate())
                  <form method="POST" action="{{ route('jobs.save', $job->slug) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-default btn-sm" title="Save Job">
                      <i class="ti-heart"></i>
                    </button>
                  </form>
                  @endif
                @endauth
                @if (auth()->check() && auth()->user()->isCandidate())
                  <a href="{{ route('jobs.show', $job->slug) }}#application" class="btn btn-border btn-rm">Apply Now</a>
                @elseif (! auth()->check())
                  <a href="{{ route('my-account', ['form' => 'register', 'role' => 'candidate']) }}" class="btn btn-border btn-rm">Create Account to Apply</a>
                @endif
                <a href="{{ route('jobs.show', $job->slug) }}" class="btn btn-common btn-rm">View Job</a>
              </div>
            </div>
            @if ($job->tags)
              <div style="margin-top:8px;">
                @foreach ($job->tags_array as $tag)
                  <span class="label label-default" style="margin-right:3px;">{{ $tag }}</span>
                @endforeach
              </div>
            @endif
          </div>
        </div>
        @empty
        <div class="alert alert-info text-center" style="margin:30px 0; padding:40px;">
          <h4><i class="ti-briefcase"></i> No jobs found</h4>
          @if(array_filter($filters ?? []))
            <p>No jobs match your search. <a href="{{ route('jobs.index') }}">Clear filters</a> to see all jobs.</p>
          @else
            <p>No jobs have been posted yet.</p>
            @if (auth()->check() && auth()->user()->isEmployer())
              <a href="{{ route('post-job') }}" class="btn btn-common">Post the First Job!</a>
            @else
              <a href="{{ route('my-account', ['form' => 'register', 'role' => 'employer']) }}" class="btn btn-common">Create an Employer Account</a>
            @endif
          @endif
        </div>
        @endforelse

        {{-- Pagination --}}
        @if ($jobs->hasPages())
          <div style="margin-top:20px; text-align:center;">
            {{ $jobs->links() }}
          </div>
        @endif

      </div>{{-- end col-md-9 --}}

      {{-- Sidebar --}}
      <div class="col-md-3 col-sm-4">
        <aside>
          <div class="sidebar">

            {{-- Categories --}}
            <div class="inner-box">
              <h3>Categories</h3>
              <ul class="cat-list">
                @foreach ($categories as $cat)
                <li>
                  <a href="{{ route('jobs.index', ['category' => $cat->slug]) }}"
                     class="{{ ($filters['category'] ?? '') === $cat->slug ? 'active' : '' }}">
                    <i class="{{ $cat->icon ?? 'ti-briefcase' }}"></i>
                    {{ $cat->name }}
                    <span class="num-posts">{{ $cat->active_jobs_count }} Jobs</span>
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            {{-- Job Types --}}
            <div class="inner-box" style="margin-top:20px;">
              <h3>Job Type</h3>
              <ul class="cat-list">
                @foreach(['full-time' => 'Full Time','part-time' => 'Part Time','remote' => 'Remote','freelance' => 'Freelance','internship' => 'Internship'] as $val => $label)
                <li>
                  <a href="{{ route('jobs.index', array_merge($filters ?? [], ['job_type' => $val])) }}"
                     class="{{ ($filters['job_type'] ?? '') === $val ? 'active' : '' }}">
                    {{ $label }}
                  </a>
                </li>
                @endforeach
              </ul>
            </div>

            {{-- Post a Job CTA --}}
            <div class="inner-box" style="margin-top:20px; background:#e84646; color:#fff; border-radius:4px; text-align:center; padding:20px;">
              <h3 style="color:#fff;">Are you hiring?</h3>
              <p style="color:#fff;">Post your job and reach thousands of candidates.</p>
              @if (auth()->check() && auth()->user()->isEmployer())
                <a href="{{ route('post-job') }}" class="btn btn-default" style="margin-top:10px;">
                  <i class="ti-pencil-alt"></i> Post A Job
                </a>
              @else
                <a href="{{ route('my-account', ['form' => 'register', 'role' => 'employer']) }}" class="btn btn-default" style="margin-top:10px;">
                  <i class="ti-user"></i> Create Employer Account
                </a>
              @endif
            </div>

          </div>
        </aside>
      </div>

    </div>
  </div>
</section>

@include('partials.footer')

@endsection

@push('scripts')
<script>
  (function () {
    var search = document.getElementById('job-search');
    if (!search || !('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) return;
    var voiceButton = document.createElement('button');
    voiceButton.type = 'button'; voiceButton.className = 'btn btn-default btn-sm'; voiceButton.style.marginTop = '8px';
    voiceButton.innerHTML = '<i class="ti-microphone"></i> Voice search (Urdu / English)';
    search.parentNode.appendChild(voiceButton);
    voiceButton.addEventListener('click', function () {
      var Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      var recognition = new Recognition(); recognition.lang = document.documentElement.lang === 'ur' ? 'ur-PK' : 'en-PK'; recognition.interimResults = false;
      recognition.onresult = function (event) { search.value = event.results[0][0].transcript; search.form.submit(); };
      recognition.start();
    });
  }());
</script>
@endpush
