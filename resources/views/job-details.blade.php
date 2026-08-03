@extends('layouts.app')

@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-wrapper">
          <h2 class="product-title">Job Details</h2>
          <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="ti-home"></i> Home</a></li>
            <li><a href="{{ route('jobs.index') }}">Browse Jobs</a></li>
            <li class="current">{{ $job->title }}</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="job-detail section">
  <div class="container">
    @if (session('success'))
      <div class="alert alert-success"><i class="ti-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger"><i class="ti-alert"></i> {{ session('error') }}</div>
    @endif

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="header-detail">
          <div class="header-content pull-left">
            <h3>{{ $job->title }}</h3>
            <p><span>Posted: {{ $job->created_at->format('d M Y') }}</span></p>
            @if ($job->salary_display)
              <p>Salary: <strong class="price">{{ $job->salary_display }}</strong></p>
            @endif
          </div>
          <div class="detail-company pull-right text-right">
            <div class="img-thum">
              @if ($job->company_logo)
                <img class="img-responsive" src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company }}" style="max-height:80px; object-fit:contain;">
              @else
                <img class="img-responsive" src="{{ asset('assets/img/jobs/recent-job-detail.jpg') }}" alt="{{ $job->company }}">
              @endif
            </div>
            <div class="name">
              <h4>{{ $job->company }}</h4>
              @if ($job->location)<h5>{{ $job->location }}</h5>@endif
              <p>{{ $job->employer?->jobListings()->active()->count() ?? 0 }} current job opening(s)</p>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="meta">
            @auth
              @if (auth()->user()->isCandidate())
                <form method="POST" action="{{ route('jobs.save', $job->slug) }}" style="display:inline;">
                  @csrf
                  <button type="submit" class="btn btn-border btn-sm">
                    <i class="{{ $saved ? 'ti-heart-broken' : 'ti-heart' }}"></i>
                    {{ $saved ? 'Remove Saved Job' : 'Save This Job' }}
                  </button>
                </form>
              @endif
            @endauth
            @guest
              <a href="{{ route('my-account', ['form' => 'register', 'role' => 'candidate']) }}" class="btn btn-border btn-sm"><i class="ti-lock"></i> Create Account to Apply</a>
            @elseif (auth()->user()->isCandidate())
              <a href="#application" class="btn btn-border btn-sm"><i class="ti-pencil-alt"></i> Apply for This Job</a>
              <form method="POST" action="{{ route('chat.start', $job) }}" style="display:inline;">@csrf<button type="submit" class="btn btn-default btn-sm"><i class="ti-comments"></i> Chat with Employer</button></form>
            @endguest
          </div>
        </div>
      </div>

      <div class="col-md-8 col-sm-12 col-xs-12">
        <div class="content-area">
          <div class="box">
            <h4>Job Description</h4>
            <p>{!! nl2br(e($job->description)) !!}</p>

            @if ($job->requirements)
              <h4>Requirements</h4>
              <p>{!! nl2br(e($job->requirements)) !!}</p>
            @endif

            @if ($job->benefits)
              <h4>Benefits</h4>
              <p>{!! nl2br(e($job->benefits)) !!}</p>
            @endif

            @if ($job->tags)
              <h4>Skills / Tags</h4>
              <p>
                @foreach ($job->tags_array as $tag)
                  <span class="label label-default" style="display:inline-block; margin:0 4px 5px 0;">{{ $tag }}</span>
                @endforeach
              </p>
            @endif
          </div>
        </div>

        <div class="box" id="application" style="margin-top:30px;">
          <h4>Apply for This Job</h4>
          @guest
            <p>Create a candidate account or log in to submit an application.</p>
            <a href="{{ route('my-account', ['form' => 'register', 'role' => 'candidate']) }}" class="btn btn-common"><i class="ti-user"></i> Create Candidate Account</a>
            <a href="{{ route('my-account') }}" class="btn btn-default"><i class="ti-lock"></i> Log In</a>
          @else
            @if (auth()->user()->isCandidate())
              @if ($applied)
                <div class="alert alert-success" style="margin-bottom:0;">You have already applied for this job.</div>
              @else
                <form method="POST" action="{{ route('jobs.apply', $job->slug) }}" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group">
                    <label for="cover_letter">Cover Letter <small>(optional)</small></label>
                    <textarea id="cover_letter" name="cover_letter" class="form-control" rows="5" maxlength="2000" placeholder="Tell the employer why you are a good fit.">{{ old('cover_letter') }}</textarea>
                    @error('cover_letter')<small class="text-danger">{{ $message }}</small>@enderror
                  </div>
                  <div class="form-group">
                    <label for="cv">CV / Resume <small>(optional: PDF, DOC, DOCX)</small></label>
                    <input id="cv" type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                    @error('cv')<small class="text-danger">{{ $message }}</small>@enderror
                  </div>
                  <button type="submit" class="btn btn-common"><i class="ti-pencil-alt"></i> Submit Application</button>
                </form>
              @endif
            @else
              <div class="alert alert-info" style="margin-bottom:0;">Only candidate accounts can apply for jobs.</div>
            @endif
          @endguest
        </div>

        @if ($related->isNotEmpty())
          <h2 class="medium-title" style="margin-top:35px;">Related Jobs</h2>
          <div class="job-post-wrapper">
            @foreach ($related as $relatedJob)
              <div class="job-list">
                <div class="job-list-content">
                  <h4>
                    <a href="{{ route('jobs.show', $relatedJob->slug) }}">{{ $relatedJob->title }}</a>
                    <span class="{{ in_array($relatedJob->job_type, ['part-time', 'freelance', 'internship']) ? 'part-time' : 'full-time' }}">
                      {{ ucfirst(str_replace('-', ' ', $relatedJob->job_type)) }}
                    </span>
                  </h4>
                  <p>{{ Str::limit($relatedJob->description, 150) }}</p>
                  <div class="job-tag">
                    <div class="pull-left">
                      <div class="meta-tag">
                        @if ($relatedJob->location)<span><i class="ti-location-pin"></i>{{ $relatedJob->location }}</span>@endif
                        @if ($relatedJob->salary_display)<span><i class="ti-money"></i>{{ $relatedJob->salary_display }}</span>@endif
                      </div>
                    </div>
                    <div class="pull-right">
                      <a href="{{ route('jobs.show', $relatedJob->slug) }}" class="btn btn-common btn-rm">View Job</a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <div class="col-md-4 col-sm-12 col-xs-12">
        <aside>
          <div class="sidebar">
            <div class="box">
              <h2 class="small-title">Job Details</h2>
              <ul class="detail-list">
                <li><span>Job ID</span><span class="type-posts">#{{ $job->id }}</span></li>
                <li><span>Company</span><span class="type-posts">{{ $job->company }}</span></li>
                @if ($job->category)<li><span>Category</span><span class="type-posts">{{ $job->category->name }}</span></li>@endif
                @if ($job->location)<li><span>Location</span><span class="type-posts">{{ $job->location }}</span></li>@endif
                <li><span>Job Type</span><span class="type-posts">{{ ucfirst(str_replace('-', ' ', $job->job_type)) }}</span></li>
                <li><span>Posted</span><span class="type-posts">{{ $job->created_at->format('d M Y') }}</span></li>
                @if ($job->closing_date)<li><span>Closing Date</span><span class="type-posts">{{ $job->closing_date->format('d M Y') }}</span></li>@endif
                @if ($job->website)<li><span>Website</span><span class="type-posts"><a href="{{ $job->website }}" target="_blank" rel="noopener">Visit</a></span></li>@endif
              </ul>
              @if($job->location)
                <a class="btn btn-default btn-sm" style="margin-top:12px;" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($job->latitude && $job->longitude ? $job->latitude.','.$job->longitude : $job->location) }}"><i class="ti-map-alt"></i> Open location in Google Maps</a>
                <iframe title="Job location map" style="border:0;width:100%;height:180px;margin-top:12px;" loading="lazy" src="https://www.google.com/maps?q={{ rawurlencode($job->latitude && $job->longitude ? $job->latitude.','.$job->longitude : $job->location) }}&output=embed"></iframe>
              @endif
            </div>

            <div class="box">
              <h2 class="small-title">Share This Job</h2>
              <p>Know someone who may be a great fit? Share this job listing with them.</p>
              <a href="mailto:?subject={{ rawurlencode($job->title . ' at ' . $job->company) }}&body={{ rawurlencode(route('jobs.show', $job->slug)) }}" class="btn btn-border btn-sm">
                <i class="ti-email"></i> Email Job
              </a>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</section>

@include('partials.footer')

@endsection
