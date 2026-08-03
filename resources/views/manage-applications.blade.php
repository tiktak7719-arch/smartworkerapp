@extends('layouts.app')
@section('content')

@include('partials.header')

<div class="page-header" style="background: url({{ asset('assets/img/banner1.jpg') }});">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="breadcrumb-wrapper">
          <h2 class="product-title">Manage Applications</h2>
          <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><i class="ti-home"></i> Home</a></li>
            <li class="current">Applications</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="content">
  <div class="container">
    <div class="row">

      {{-- Sidebar --}}
      <div class="col-md-3 col-sm-4">
        <div class="right-sideabr">
          <div class="inner-box">
            <h4>Employer Menu</h4>
            <ul class="lest item">
              <li><a href="{{ route('post-job') }}"><i class="ti-plus"></i> Post New Job</a></li>
              <li><a href="{{ route('manage-jobs') }}"><i class="ti-list"></i> My Jobs</a></li>
              <li class="active"><a href="{{ route('manage-applications') }}"><i class="ti-email"></i> Applications</a></li>
              <li><a href="{{ route('company.profile') }}"><i class="ti-user"></i> Company Profile</a></li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Applications Table --}}
      <div class="col-md-9 col-sm-8">

        @if(!Auth::check())
          <div class="alert alert-warning">Please <a href="{{ route('my-account') }}">login</a> to view applications.</div>
        @else

          @if(session('success'))
            <div class="alert alert-success"><i class="ti-check"></i> {{ session('success') }}</div>
          @endif

          <h3>Applications Received</h3>

          @if(isset($applications) && $applications->count() > 0)
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Applicant</th>
                    <th>Job</th>
                    <th>Applied</th>
                    <th>CV</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($applications as $app)
                  <tr>
                    <td>
                      <strong>{{ $app->applicant->name ?? 'N/A' }}</strong><br>
                      <small class="text-muted">{{ $app->applicant->email ?? '' }}</small>
                    </td>
                    <td>{{ Str::limit($app->job->title ?? 'N/A', 35) }}</td>
                    <td>{{ $app->created_at->format('d M Y') }}</td>
                    <td>
                      @if($app->cv_path)
                        <a href="{{ route('applications.cv.download', $app) }}" target="_blank" class="btn btn-default btn-xs">
                          <i class="ti-download"></i> Download
                        </a>
                      @else
                        <span class="text-muted">None</span>
                      @endif
                    </td>
                    <td>{!! $app->status_badge !!}</td>
                    <td>
                      <form method="POST" action="{{ route('applications.status', $app->id) }}">
                        @csrf @method('PATCH')
                        <div class="btn-group btn-group-xs" style="display:flex;flex-wrap:wrap;gap:4px;">
                          <button class="btn btn-info" type="submit" name="status" value="reviewing" @disabled($app->status === 'reviewing')>Review</button>
                          <button class="btn btn-primary" type="submit" name="status" value="shortlisted" @disabled($app->status === 'shortlisted')>Accept / Shortlist</button>
                          <button class="btn btn-success" type="submit" name="status" value="hired" @disabled($app->status === 'hired')>Hire</button>
                          <button class="btn btn-danger" type="submit" name="status" value="rejected" @disabled($app->status === 'rejected') onclick="return confirm('Reject this application?')">Reject</button>
                        </div>
                        <textarea name="employer_notes" class="form-control input-sm" rows="2" maxlength="1500" style="margin-top:8px;min-width:180px;" placeholder="Optional note for the candidate">{{ $app->employer_notes }}</textarea>
                        <button class="btn btn-default btn-xs" type="submit" name="status" value="{{ $app->status }}" style="margin-top:5px;">Save note</button>
                      </form>
                    </td>
                  </tr>
                  @if($app->cover_letter)
                  <tr>
                    <td colspan="6" style="background:#f9f9f9;">
                      <small><strong>Cover Letter:</strong> {{ $app->cover_letter }}</small>
                    </td>
                  </tr>
                  @endif
                  @if($app->status === 'hired' && auth()->id() === $app->job->user_id)
                  <tr><td colspan="6" style="background:#f7fbf7;"><form method="POST" action="{{ route('ratings.store', $app) }}" class="form-inline">@csrf <strong>Rate {{ $app->applicant->name }}:</strong> <select name="score" class="form-control input-sm">@foreach([5,4,3,2,1] as $score)<option value="{{ $score }}">{{ $score }} stars</option>@endforeach</select> <input name="comment" class="form-control input-sm" maxlength="1000" placeholder="Optional feedback" style="min-width:220px;"> <button class="btn btn-common btn-sm" type="submit"><i class="ti-star"></i> Save rating</button></form></td></tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
            @if($applications->hasPages())
              <div style="margin-top:15px;">{{ $applications->links() }}</div>
            @endif
          @else
            <div class="alert alert-info text-center" style="padding:40px;">
              <h4><i class="ti-email"></i> No applications yet</h4>
              <p>Applications will appear here when candidates apply for your jobs.</p>
              <a href="{{ route('jobs.index') }}" class="btn btn-common">View All Jobs</a>
            </div>
          @endif

        @endif
      </div>

    </div>
  </div>
</div>

@include('partials.footer')
@endsection
