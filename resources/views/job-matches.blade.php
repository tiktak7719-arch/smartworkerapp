@extends('layouts.app')

@section('content')
@include('partials.header')
<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">{{ __('app.smart_matches') }}</h2><ol class="breadcrumb"><li><a href="{{ route('manage-jobs') }}">Manage Jobs</a></li><li class="current">{{ $job->title }}</li></ol></div></div></div>
<section class="section"><div class="container"><div class="row"><div class="col-md-10 col-md-offset-1"><div class="inner-box"><h3>Best candidates for {{ $job->title }}</h3><p class="text-muted">Scores are based on skill overlap, saved location/distance and worker ratings. This transparent rule-based score is ready to be upgraded to a Python AI service later.</p>
  <div class="table-responsive"><table class="table table-striped"><thead><tr><th>Candidate</th><th>Skills</th><th>Location</th><th>Predicted rating</th><th>{{ __('app.match_score') }}</th></tr></thead><tbody>
    @forelse($matches as $candidate)<tr><td><strong>{{ $candidate->name }}</strong><br><small>{{ $candidate->profile?->experience_years ?? '' }}</small></td><td>{{ $candidate->profile?->skills ?: 'Not provided' }}</td><td>{{ $candidate->location ?: 'Not provided' }}@if($candidate->distance_km !== null)<br><small>{{ $candidate->distance_km }} km away</small>@endif</td><td><i class="ti-star" style="color:#f5a623;"></i> {{ number_format((float) $candidate->predicted_rating, 1) }}</td><td><span class="label label-success" style="font-size:13px;">{{ $candidate->match_score }}%</span></td></tr>
    @empty<tr><td colspan="5" class="text-center text-muted">No strong matches yet. Ask candidates to add their skills and location.</td></tr>@endforelse
  </tbody></table></div>
</div></div></div></div></section>
@include('partials.footer')
@endsection
