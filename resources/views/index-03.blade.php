@extends('layouts.app')

@section('content')

@include('partials.header')

<section id="slider" style="background:linear-gradient(rgba(0,0,0,.40), rgba(0,0,0,.40)), url('{{ asset('assets/img/slider/slide1.jpg') }}') center/cover; min-height:420px; display:flex; align-items:center;">
  <div class="container text-center" style="color:#fff;">
    <h1 style="font-size:48px;font-weight:800;text-transform:uppercase;">Find Your Next Opportunity</h1>
    <p style="font-size:18px;margin:18px 0 30px;">Discover jobs, connect with employers, and build your career with SmartWorker.</p>
    <form method="GET" action="{{ route('jobs.index') }}" class="row" style="max-width:940px;margin:0 auto;">
      <div class="col-md-5 col-sm-5" style="padding:4px;"><input class="form-control input-lg" name="search" placeholder="Job title, skill, or company"></div>
      <div class="col-md-4 col-sm-4" style="padding:4px;"><input class="form-control input-lg" name="location" placeholder="Location"></div>
      <div class="col-md-3 col-sm-3" style="padding:4px;"><button class="btn btn-common btn-lg" type="submit" style="width:100%;"><i class="ti-search"></i> Search Jobs</button></div>
    </form>
  </div>
</section>

<section class="find-job section">
  <div class="container">
    <h2 class="section-title">Latest Jobs</h2>
    <div class="row">
      <div class="col-md-12">
        @forelse ($latestJobs as $index => $job)
          <div class="job-list">
            <div class="thumb">
              <a href="{{ route('jobs.show', $job->slug) }}">
                <img src="{{ $job->company_logo ? asset('storage/' . $job->company_logo) : asset('assets/img/jobs/img-' . (($index % 4) + 1) . '.jpg') }}" alt="{{ $job->company }}" style="object-fit:contain;">
              </a>
            </div>
            <div class="job-list-content">
              <h4>
                <a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a>
                <span class="{{ in_array($job->job_type, ['part-time', 'freelance', 'internship']) ? 'part-time' : 'full-time' }}">{{ ucfirst(str_replace('-', ' ', $job->job_type)) }}</span>
              </h4>
              <p>{{ Str::limit($job->description, 160) }}</p>
              <div class="job-tag">
                <div class="pull-left"><div class="meta-tag">
                  @if ($job->category)<span><i class="{{ $job->category->icon ?: 'ti-briefcase' }}"></i>{{ $job->category->name }}</span>@endif
                  @if ($job->location)<span><i class="ti-location-pin"></i>{{ $job->location }}</span>@endif
                  @if ($job->salary_display)<span><i class="ti-money"></i>{{ $job->salary_display }}</span>@endif
                </div></div>
                <div class="pull-right"><a href="{{ route('jobs.show', $job->slug) }}" class="btn btn-common btn-rm">View Job</a></div>
              </div>
            </div>
          </div>
        @empty
          <div class="alert alert-info text-center">New opportunities will appear here as soon as employers publish them.</div>
        @endforelse
      </div>
      <div class="col-md-12 text-center"><a href="{{ route('jobs.index') }}" class="btn btn-common">Browse All Jobs</a></div>
    </div>
  </div>
</section>

<section class="category section">
  <div class="container">
    <h2 class="section-title">Browse Categories</h2>
    <div class="row">
      @forelse ($categories as $category)
        <div class="col-md-3 col-sm-4 col-xs-12 f-category">
          <a href="{{ route('jobs.index', ['category' => $category->slug]) }}">
            <div class="icon"><i class="{{ $category->icon ?: 'ti-briefcase' }}"></i></div>
            <h3>{{ $category->name }}</h3>
            <p>{{ $category->active_jobs_count }} {{ Str::plural('job', $category->active_jobs_count) }}</p>
          </a>
        </div>
      @empty
        <div class="col-xs-12 text-center text-muted">Categories will be listed here once created.</div>
      @endforelse
    </div>
  </div>
</section>

<section class="featured-jobs section">
  <div class="container">
    <h2 class="section-title">Featured Jobs</h2>
    <div class="row">
      @forelse ($featuredJobs as $index => $job)
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="featured-item">
            <div class="featured-wrap"><div class="featured-inner">
              <figure class="item-thumb">
                <a class="hover-effect" href="{{ route('jobs.show', $job->slug) }}">
                  <img src="{{ $job->company_logo ? asset('storage/' . $job->company_logo) : asset('assets/img/features/img-' . (($index % 3) + 1) . '.jpg') }}" alt="{{ $job->company }}" style="object-fit:contain;">
                </a>
              </figure>
              <div class="item-body">
                <h3 class="job-title"><a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a></h3>
                <div class="adderess"><i class="ti-location-pin"></i> {{ $job->location ?: 'Location not specified' }}</div>
              </div>
            </div></div>
            <div class="item-foot">
              <span><i class="ti-calendar"></i> {{ $job->created_at->diffForHumans() }}</span>
              <span><i class="ti-time"></i> {{ ucfirst(str_replace('-', ' ', $job->job_type)) }}</span>
              <div class="view-iocn"><a href="{{ route('jobs.show', $job->slug) }}"><i class="ti-arrow-right"></i></a></div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-xs-12"><div class="alert alert-info text-center">There are no featured jobs yet.</div></div>
      @endforelse
    </div>
  </div>
</section>

<section class="counter section" style="background:url('{{ asset('assets/img/bg/counter-bg.jpg') }}') center/cover;">
  <div class="container">
    <div class="row text-center" style="color:#fff;">
      <div class="col-sm-4 col-xs-12"><h2 style="color:#fff;">{{ $totalJobs }}</h2><p>Active Jobs</p></div>
      <div class="col-sm-4 col-xs-12"><h2 style="color:#fff;">{{ $totalEmployers }}</h2><p>Employers</p></div>
      <div class="col-sm-4 col-xs-12"><h2 style="color:#fff;">{{ $totalCandidates }}</h2><p>Candidates</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="section-title">Featured Employers</h2>
    <div class="row">
      @forelse ($featuredEmployers as $employer)
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="inner-box text-center" style="margin-bottom:25px;min-height:210px;">
            @if ($employer->profile?->company_logo)
              <img src="{{ asset('storage/' . $employer->profile->company_logo) }}" alt="{{ $employer->name }}" style="height:65px;max-width:100px;object-fit:contain;">
            @elseif ($employer->avatar)
              <img src="{{ asset('storage/' . $employer->avatar) }}" alt="{{ $employer->name }}" style="height:65px;width:65px;object-fit:cover;border-radius:50%;">
            @else
              <img src="{{ asset('assets/img/clients/img1.png') }}" alt="{{ $employer->name }}" style="height:65px;max-width:100px;object-fit:contain;">
            @endif
            <h4 style="margin:15px 0 5px;">{{ $employer->profile?->company_name ?: $employer->name }}</h4>
            <p class="text-muted">{{ $employer->profile?->industry ?: 'Employer' }}</p>
            <p><strong>{{ $employer->active_jobs_count }}</strong> active {{ Str::plural('job', $employer->active_jobs_count) }}</p>
          </div>
        </div>
      @empty
        <div class="col-xs-12"><div class="alert alert-info text-center">Featured companies will appear here as employers build their profiles.</div></div>
      @endforelse
    </div>
    <div class="text-center"><a href="{{ route('employers.index') }}" class="btn btn-border">Browse Employers</a></div>
  </div>
</section>

<section class="section" style="background:#f7f7f7;">
  <div class="container">
    <h2 class="section-title">Career Advice</h2>
    <div class="row">
      @forelse ($latestPosts as $index => $post)
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="featured-item">
            <div class="featured-wrap"><div class="featured-inner">
              <figure class="item-thumb"><a href="{{ route('blog.show', $post) }}"><img src="{{ $post->cover_image ? asset('storage/' . $post->cover_image) : asset('assets/img/blog/home-items/img' . (($index % 3) + 1) . '.jpg') }}" alt="{{ $post->title }}"></a></figure>
              <div class="item-body">
                <h3 class="job-title"><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                <p>{{ Str::limit($post->excerpt ?: strip_tags($post->body), 110) }}</p>
              </div>
            </div></div>
          </div>
        </div>
      @empty
        <div class="col-xs-12"><div class="alert alert-info text-center">Career articles will appear here once published.</div></div>
      @endforelse
    </div>
  </div>
</section>

<section class="testimonial section" style="background:url('{{ asset('assets/img/testimonial/testimonial-bg.jpg') }}') center/cover;">
  <div class="container">
    <h2 class="section-title" style="color:#fff;">What Our Community Says</h2>
    <div class="row">
      @forelse ($testimonials as $testimonial)
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="inner-box text-center" style="min-height:220px;">
            <img src="{{ $testimonial->avatar ? asset('storage/' . $testimonial->avatar) : asset('assets/img/testimonial/img1.jpg') }}" alt="{{ $testimonial->name }}" style="height:65px;width:65px;border-radius:50%;object-fit:cover;">
            <p style="margin:15px 0;">“{{ $testimonial->content }}”</p>
            <strong>{{ $testimonial->name }}</strong>
            @if ($testimonial->headline)<p class="text-muted">{{ $testimonial->headline }}</p>@endif
          </div>
        </div>
      @empty
        <div class="col-xs-12 text-center" style="color:#fff;">Testimonials will appear here when they are added by the team.</div>
      @endforelse
    </div>
  </div>
</section>

@include('partials.footer')

@endsection
